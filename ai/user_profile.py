# user_profile.py
"""
Helper module for building user profiles from activity data.
Fetches user activity from MySQL and creates aggregated embeddings.
"""
import json
import mysql.connector
import numpy as np
from typing import List, Dict, Optional, Tuple
from embedder import embed_text
from sync_products import fetch_product, DB


def fetch_user_activity(user_id: int) -> Dict[str, List[int]]:
    """
    Fetch all user activity from MySQL.
    
    Returns:
        {
            'purchases': [product_id, ...],
            'views': [product_id, ...],
            'purchase_dates': {product_id: timestamp, ...},
            'view_dates': {product_id: timestamp, ...}
        }
    """
    db = mysql.connector.connect(**DB)
    cursor = db.cursor(dictionary=True)
    
    activity = {
        'purchases': [],
        'views': [],
        'purchase_dates': {},
        'view_dates': {}
    }
    
    try:
        # Fetch purchases (ordered by most recent first)
        cursor.execute("""
            SELECT product_id, purchased_at
            FROM purchased_products
            WHERE user_id = %s
            ORDER BY purchased_at DESC
        """, (user_id,))
        purchases = cursor.fetchall()
        activity['purchases'] = [p['product_id'] for p in purchases]
        activity['purchase_dates'] = {p['product_id']: p['purchased_at'] for p in purchases}
        
        # Fetch views (ordered by most recent first)
        cursor.execute("""
            SELECT product_id, visited_at
            FROM visited_products
            WHERE user_id = %s
            ORDER BY visited_at DESC
        """, (user_id,))
        views = cursor.fetchall()
        activity['views'] = [v['product_id'] for v in views]
        activity['view_dates'] = {v['product_id']: v['visited_at'] for v in views}
        
    finally:
        cursor.close()
        db.close()
    
    return activity


def get_top_representative_products(user_id: int, max_products: int = 10) -> List[int]:
    """
    Select top representative products for user profile.
    Priority: recent purchases > recent views > most viewed category.
    
    Args:
        user_id: User ID
        max_products: Maximum number of products to include
        
    Returns:
        List of product IDs
    """
    activity = fetch_user_activity(user_id)
    
    selected_products = []
    seen = set()
    
    # 1. Prioritize recent purchases (up to 5)
    for product_id in activity['purchases'][:5]:
        if product_id not in seen:
            selected_products.append(product_id)
            seen.add(product_id)
            if len(selected_products) >= max_products:
                return selected_products
    
    # 2. Add recent views (up to 5)
    for product_id in activity['views'][:5]:
        if product_id not in seen:
            selected_products.append(product_id)
            seen.add(product_id)
            if len(selected_products) >= max_products:
                return selected_products
    
    # 3. Fallback: most viewed category
    if len(selected_products) < max_products:
        db = mysql.connector.connect(**DB)
        cursor = db.cursor(dictionary=True)
        
        try:
            # Find most viewed category
            cursor.execute("""
                SELECT p.category_id, COUNT(*) as view_count
                FROM visited_products vp
                JOIN products p ON vp.product_id = p.id
                WHERE vp.user_id = %s
                GROUP BY p.category_id
                ORDER BY view_count DESC
                LIMIT 1
            """, (user_id,))
            result = cursor.fetchone()
            
            if result:
                category_id = result['category_id']
                # Get products from that category (excluding already selected)
                placeholders = ','.join(['%s'] * len(selected_products)) if selected_products else '0'
                query = f"""
                    SELECT id FROM products
                    WHERE category_id = %s
                    AND id NOT IN ({placeholders})
                    LIMIT %s
                """
                params = [category_id] + selected_products + [max_products - len(selected_products)]
                cursor.execute(query, params)
                category_products = [row['id'] for row in cursor.fetchall()]
                selected_products.extend(category_products)
        finally:
            cursor.close()
            db.close()
    
    return selected_products[:max_products]


def build_user_profile_vector(user_id: int, max_products: int = 10) -> Optional[np.ndarray]:
    """
    Build aggregated user profile vector from their activity.
    
    Args:
        user_id: User ID
        max_products: Maximum number of products to consider
        
    Returns:
        Aggregated embedding vector (numpy array) or None if no activity
    """
    product_ids = get_top_representative_products(user_id, max_products)
    
    if not product_ids:
        return None
    
    embeddings = []
    weights = []
    activity = fetch_user_activity(user_id)
    
    for product_id in product_ids:
        product = fetch_product(product_id)
        if not product:
            continue
        
        # Build combined text (same format as sync_products.py)
        tags = json.loads(product["tags"]) if product.get("tags") else []
        combined = f"{product['name']} {product['description']} {product.get('category_name', '')} {' '.join(tags)}"
        
        # Generate embedding
        embedding = embed_text(combined)
        embeddings.append(embedding)
        
        # Weight by activity type: purchases = 2.0, views = 1.0
        if product_id in activity['purchases']:
            weights.append(2.0)
        elif product_id in activity['views']:
            weights.append(1.0)
        else:
            weights.append(0.5)  # Category fallback
    
    if not embeddings:
        return None
    
    # Weighted average of embeddings
    embeddings_array = np.array(embeddings)
    weights_array = np.array(weights)
    weights_array = weights_array / weights_array.sum()  # Normalize weights
    
    # Calculate weighted average
    user_vector = np.average(embeddings_array, axis=0, weights=weights_array)
    
    # Normalize the result
    norm = np.linalg.norm(user_vector)
    if norm > 0:
        user_vector = user_vector / norm
    
    return user_vector


def get_user_interacted_products(user_id: int) -> set:
    """
    Get all product IDs the user has interacted with (purchases + views).
    
    Returns:
        Set of product IDs
    """
    activity = fetch_user_activity(user_id)
    interacted = set(activity['purchases']) | set(activity['views'])
    return interacted

