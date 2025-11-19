from qdrant_client import QdrantClient
from qdrant_client.models import PointStruct, Filter, FieldCondition, MatchValue
from embedder import embed_text

QDRANT_URL = "http://127.0.0.1:6333"
COLLECTION_NAME = "products"

client = QdrantClient(url=QDRANT_URL)


def recommend_for_product(product_id: int, k: int = 5):
    # Fetch the product text to embed
    payload = client.retrieve(
        collection_name=COLLECTION_NAME,
        ids=[product_id],
        with_payload=True,
        with_vectors=False
    )

    if not payload:
        return {"error": "Product not found"}

    product = payload[0].payload
    text = f"{product.get('name', '')} {product.get('description', '')}"

    # Embed query
    query_vector = embed_text(text)

    # Search similar products
    results = client.search(
        collection_name=COLLECTION_NAME,
        query_vector=query_vector,
        limit=k + 1,
        with_payload=True
    )

    # Remove the product itself
    filtered = [r for r in results if r.id != product_id][:k]

    return {
        "product_id": product_id,
        "recommendations": [
            {
                "id": r.id,
                "score": r.score,
                "payload": r.payload
            } for r in filtered
        ]
    }


def recommend_for_user(user_id: int, k: int = 5):
    # Get user's last visited or purchased product
    # You can extend this logic later

    return {"message": "User-based recommendation coming soon."}
