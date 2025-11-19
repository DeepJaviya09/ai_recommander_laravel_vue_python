# project/ai/sync_products.py

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

    # Connect Qdrant
    client = QdrantClient(url=QDRANT_URL)

    # Create collection
    client.recreate_collection(
        collection_name=COLLECTION_NAME,
        vectors_config=qmodels.VectorParams(size=VECTOR_SIZE, distance=qmodels.Distance.COSINE)
    )

    points = []

    for p in products:
        tags = json.loads(p['tags']) if p['tags'] else []
        text = f"{p['name']} {p['description']} {p['category_name']} {' '.join(tags)}"

        vector = embed_text(text)

        points.append(
            qmodels.PointStruct(
                id=int(p['id']),
                vector=vector,
                payload=p
            )
        )

    print("Uploading to Qdrant...")
    client.upsert(collection_name=COLLECTION_NAME, points=points)

    print("✅ Sync completed successfully.")


if __name__ == "__main__":
    sync_products()
