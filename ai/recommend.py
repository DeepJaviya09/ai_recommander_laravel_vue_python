from qdrant_client import QdrantClient
from embedder import embed_text
from sync_products import fetch_product
import json

QDRANT_URL = "http://127.0.0.1:6333"
COLLECTION_NAME = "products"

client = QdrantClient(url=QDRANT_URL)


def recommend_for_product(product_id: int, k: int = 10):
    product = fetch_product(product_id)
    if not product:
        return []

    # Build combined text
    combined = f"{product['name']} {product['description']} {product['category_name']} "

    try:
        tags = json.loads(product["tags"]) if isinstance(product["tags"], str) else product["tags"]
        combined_tags = tags
        combined += " ".join(tags)
    except:
        combined_tags = []
        pass

    vector = embed_text(combined).tolist()

    # Fetch more results (for reranking)
    raw_results = client.search(
        collection_name=COLLECTION_NAME,
        query_vector=vector,
        limit=30,
        with_payload=True
    )

    boosted = []

    for r in raw_results:
        if r.payload["id"] == product_id:
            continue

        score = r.score

        # --- Category Boost ---
        if r.payload["category_name"] == product["category_name"]:
            score += 0.20   # 20% boost

        # --- Tag Boost ---
        try:
            r_tags = json.loads(r.payload["tags"])
        except:
            r_tags = []

        shared_tags = set(combined_tags).intersection(set(r_tags))
        score += 0.10 * len(shared_tags)

        boosted.append((score, r))

    # Sort again after boosting
    boosted_sorted = sorted(boosted, key=lambda x: x[0], reverse=True)

    final = boosted_sorted[:k]

    return {
        "product_id": product_id,
        "recommendations": [
            {
                "id": entry[1].payload["id"],
                "score": float(entry[0]),
                "payload": entry[1].payload
            }
            for entry in final
        ]
    }


def recommend_for_user(user_id: int, k: int = 5):
    return {"message": "User-based recommendation coming soon."}
