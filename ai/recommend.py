from qdrant_client import QdrantClient
from qdrant_client.http import models as qmodels
from embedder import embed_text
from sync_products import fetch_product, fetch_products_batch
from user_profile import build_user_profile_vector, get_user_interacted_products, fetch_user_activity
import json
import numpy as np

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


def recommend_for_user(user_id: int, k: int = 10):
    """
    Generate personalized recommendations for a user based on their activity.
    
    Uses a hybrid approach:
    1. Content-based: Build user profile vector from viewed/purchased products
    2. Collaborative: (Future extension - find similar users)
    
    Args:
        user_id: User ID
        k: Number of recommendations to return
        
    Returns:
        {
            "user_id": int,
            "source": "user-profile",
            "recommendations": [
                {"id": int, "score": float, "payload": {...}},
                ...
            ]
        }
    """
    try:
        # 1. Build user profile vector from activity
        user_vector = build_user_profile_vector(user_id, max_products=10)
        
        if user_vector is None:
            # No user activity - return empty recommendations
            return {
                "user_id": user_id,
                "source": "user-profile",
                "message": "No user activity found. Please browse or purchase products to get recommendations.",
                "recommendations": []
            }
        
        # 2. Get products user has already interacted with (to exclude)
        interacted_products = get_user_interacted_products(user_id)
        
        # 3. Fetch user activity and compute preferred categories
        activity = fetch_user_activity(user_id)
        
        # Pre-fetch user's product data in batch for optimization
        user_product_ids = list(set(activity['purchases'][:5] + activity['views'][:10]))  # Top products
        user_products = fetch_products_batch(user_product_ids) if user_product_ids else {}
        
        # Pre-compute user categories and tags with weights
        user_categories = {}  # category_id -> weight (higher for purchases)
        user_tags = set()
        
        # Process purchases (higher weight)
        for pid in activity['purchases'][:5]:
            if pid in user_products:
                prod = user_products[pid]
                if prod.get('category_id'):
                    # Purchases get weight 2.0
                    user_categories[prod['category_id']] = user_categories.get(prod['category_id'], 0) + 2.0
                try:
                    tags = json.loads(prod["tags"]) if isinstance(prod["tags"], str) else prod.get("tags", [])
                    user_tags.update(tags)
                except:
                    pass
        
        # Process views (lower weight)
        for pid in activity['views'][:10]:
            if pid in user_products:
                prod = user_products[pid]
                if prod.get('category_id'):
                    # Views get weight 1.0
                    user_categories[prod['category_id']] = user_categories.get(prod['category_id'], 0) + 1.0
        
        # Get top preferred categories (sorted by weight)
        preferred_categories = sorted(user_categories.items(), key=lambda x: x[1], reverse=True)
        preferred_category_ids = set([cat_id for cat_id, _ in preferred_categories])
        
        # 4. Search Qdrant with user profile vector
        # Increase search limit significantly to ensure we get products from all categories
        search_limit = max(k * 5, 100)  # Get 5x results or at least 100
        
        raw_results = client.search(
            collection_name=COLLECTION_NAME,
            query_vector=user_vector.tolist(),
            limit=search_limit,
            with_payload=True
        )
        
        # 5. Filter out interacted products and apply boosting
        boosted = []
        
        for r in raw_results:
            product_id = r.payload["id"]
            
            # Skip products user already interacted with
            if product_id in interacted_products:
                continue
            
            score = float(r.score)
            product_category_id = r.payload.get("category_id")
            
            # --- Strong Category Boost (if user has viewed/purchased from this category) ---
            if product_category_id in preferred_category_ids:
                # Get category weight (normalized)
                category_weight = user_categories.get(product_category_id, 1.0)
                max_weight = max(user_categories.values()) if user_categories else 1.0
                normalized_weight = category_weight / max_weight if max_weight > 0 else 1.0
                
                # Apply stronger boost: multiply score by category preference
                # This ensures preferred categories rank much higher
                category_multiplier = 1.0 + (normalized_weight * 1.5)  # 1.0x to 2.5x multiplier
                score = score * category_multiplier
                
                # Additional fixed boost for preferred categories
                score += 0.3  # Strong boost for preferred categories
            
            # --- Tag Boost (if product shares tags with user's preferred products) ---
            try:
                r_tags = json.loads(r.payload["tags"]) if isinstance(r.payload["tags"], str) else r.payload.get("tags", [])
                shared_tags = set(r_tags).intersection(user_tags)
                if shared_tags:
                    score += 0.15 * len(shared_tags)  # 15% per shared tag
            except:
                pass
            
            boosted.append((score, r))
        
        # 6. Sort by boosted score (high to low)
        boosted_sorted = sorted(boosted, key=lambda x: x[0], reverse=True)
        
        # 7. Ensure preferred categories are well-represented
        # Separate results by category
        preferred_category_results = []
        other_results = []
        
        for entry in boosted_sorted:
            product_category_id = entry[1].payload.get("category_id")
            if product_category_id in preferred_category_ids:
                preferred_category_results.append(entry)
            else:
                other_results.append(entry)
        
        # If preferred categories are not well-represented, do a category-specific search
        if len(preferred_category_results) < k * 0.5 and preferred_category_ids:
            # Search specifically for products in preferred categories
            
            for preferred_cat_id in list(preferred_category_ids)[:3]:  # Top 3 preferred categories
                try:
                    # Search with category filter
                    filtered_results = client.search(
                        collection_name=COLLECTION_NAME,
                        query_vector=user_vector.tolist(),
                        query_filter=qmodels.Filter(
                            must=[
                                qmodels.FieldCondition(
                                    key="category_id",
                                    match=qmodels.MatchValue(value=preferred_cat_id)
                                )
                            ]
                        ),
                        limit=20,
                        with_payload=True
                    )
                    
                    # Add filtered results that aren't already included and aren't excluded
                    for r in filtered_results:
                        product_id = r.payload["id"]
                        if product_id not in interacted_products:
                            # Check if already in results
                            already_included = any(
                                entry[1].payload["id"] == product_id 
                                for entry in preferred_category_results + other_results
                            )
                            
                            if not already_included:
                                score = float(r.score)
                                # Apply same boosting
                                category_weight = user_categories.get(preferred_cat_id, 1.0)
                                max_weight = max(user_categories.values()) if user_categories else 1.0
                                normalized_weight = category_weight / max_weight if max_weight > 0 else 1.0
                                category_multiplier = 1.0 + (normalized_weight * 1.5)
                                score = score * category_multiplier
                                score += 0.3
                                
                                # Tag boost
                                try:
                                    r_tags = json.loads(r.payload["tags"]) if isinstance(r.payload["tags"], str) else r.payload.get("tags", [])
                                    shared_tags = set(r_tags).intersection(user_tags)
                                    if shared_tags:
                                        score += 0.15 * len(shared_tags)
                                except:
                                    pass
                                
                                preferred_category_results.append((score, r))
                except Exception as e:
                    # If filter search fails, continue
                    pass
        
        # Prioritize preferred category results: take 80% from preferred, 20% from others
        preferred_count = min(len(preferred_category_results), int(k * 0.8))
        other_count = k - preferred_count
        
        final_results = preferred_category_results[:preferred_count] + other_results[:other_count]
        
        # If we don't have enough preferred category results, use what we have
        if len(final_results) < k:
            remaining = k - len(final_results)
            # Add more from preferred first, then others
            if len(preferred_category_results) > preferred_count:
                final_results.extend(preferred_category_results[preferred_count:preferred_count + remaining])
                remaining = k - len(final_results)
            if remaining > 0 and len(other_results) > other_count:
                final_results.extend(other_results[other_count:other_count + remaining])
        
        # Final sort by score
        final_results = sorted(final_results, key=lambda x: x[0], reverse=True)[:k]
        
        # 8. Format response
        recommendations = [
            {
                "id": entry[1].payload["id"],
                "score": entry[0],
                "payload": entry[1].payload
            }
            for entry in final_results
        ]
        
        return {
            "user_id": user_id,
            "source": "user-profile",
            "recommendations": recommendations
        }
        
    except Exception as e:
        # Error handling
        return {
            "user_id": user_id,
            "source": "user-profile",
            "error": str(e),
            "recommendations": []
        }
