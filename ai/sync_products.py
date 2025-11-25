# sync_products.py
import json
import mysql.connector
from qdrant_client import QdrantClient
from qdrant_client.http import models as qmodels
from embedder import embed_text

DB = {
    "host": "127.0.0.1",
    "user": "root",
    "password": "",
    "database": "ai_recommand"
}

QDRANT_URL = "http://localhost:6333"
COLLECTION_NAME = "products"
VECTOR_SIZE = 384


def sync_products():
    db = mysql.connector.connect(**DB)
    cursor = db.cursor(dictionary=True)

    print("Fetching products...")
    cursor.execute("""
        SELECT p.*, c.name AS category_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
    """)
    products = cursor.fetchall()

    print(f"Found {len(products)} products")

    client = QdrantClient(url=QDRANT_URL)

    client.recreate_collection(
        collection_name=COLLECTION_NAME,
        vectors_config=qmodels.VectorParams(
            size=VECTOR_SIZE,
            distance=qmodels.Distance.COSINE
        )
    )

    points = []

    for p in products:
        tags = json.loads(p["tags"]) if p["tags"] else []
        combined = f"{p['name']} {p['description']} {p['category_name']} {' '.join(tags)}"

        vec = embed_text(combined)

        points.append(
            qmodels.PointStruct(
                id=int(p["id"]),
                vector=vec,
                payload=p
            )
        )

    print("Uploading to Qdrant...")
    client.upsert(collection_name=COLLECTION_NAME, points=points)

    print("✅ Sync completed successfully.")


def fetch_product(product_id: int):
    db = mysql.connector.connect(**DB)
    cursor = db.cursor(dictionary=True)
    cursor.execute("""
        SELECT p.*, c.name AS category_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.id = %s
    """, (product_id,))
    result = cursor.fetchone()
    cursor.close()
    db.close()
    return result


def fetch_products_batch(product_ids: list):
    """
    Fetch multiple products by IDs in a single query (optimization).
    
    Args:
        product_ids: List of product IDs
        
    Returns:
        Dictionary mapping product_id -> product data
    """
    if not product_ids:
        return {}
    
    db = mysql.connector.connect(**DB)
    cursor = db.cursor(dictionary=True)
    
    # Build IN clause with placeholders
    placeholders = ','.join(['%s'] * len(product_ids))
    query = f"""
        SELECT p.*, c.name AS category_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.id IN ({placeholders})
    """
    
    cursor.execute(query, tuple(product_ids))
    results = cursor.fetchall()
    cursor.close()
    db.close()
    
    # Return as dictionary for easy lookup
    return {product['id']: product for product in results}


if __name__ == "__main__":
    sync_products()
