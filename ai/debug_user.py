# Debug script to check user activity and recommendations
import sys
from user_profile import fetch_user_activity, get_user_interacted_products, get_top_representative_products
from sync_products import fetch_product, fetch_products_batch, DB
import mysql.connector

def debug_user(user_id: int):
    """Debug user activity and see what's happening with recommendations."""
    
    print(f"\n{'='*60}")
    print(f"DEBUG: User {user_id} Activity Analysis")
    print(f"{'='*60}\n")
    
    # 1. Fetch user activity
    from user_profile import fetch_user_activity
    activity = fetch_user_activity(user_id)
    
    print(f"📊 User Activity Summary:")
    print(f"   Views: {len(activity['views'])} products")
    print(f"   Purchases: {len(activity['purchases'])} products")
    print(f"   Total interactions: {len(set(activity['views'] + activity['purchases']))} unique products\n")
    
    # 2. Get interacted products
    interacted = get_user_interacted_products(user_id)
    print(f"🚫 Excluded Products: {len(interacted)} products")
    print(f"   Product IDs: {sorted(list(interacted))}\n")
    
    # 3. Get top representative products
    top_products = get_top_representative_products(user_id, max_products=10)
    print(f"⭐ Top Representative Products: {len(top_products)} products")
    print(f"   Product IDs: {top_products}\n")
    
    # 4. Fetch product details and show categories
    if top_products:
        products = fetch_products_batch(top_products)
        print(f"📚 Product Details:")
        for pid in top_products:
            if pid in products:
                prod = products[pid]
                print(f"   ID {pid}: {prod.get('name', 'N/A')[:50]}")
                print(f"      Category: {prod.get('category_name', 'N/A')} (ID: {prod.get('category_id', 'N/A')})")
                print(f"      Type: {'PURCHASED' if pid in activity['purchases'] else 'VIEWED' if pid in activity['views'] else 'FALLBACK'}")
        print()
    
    # 5. Calculate category weights
    user_categories = {}
    for pid in activity['purchases']:
        prod = fetch_product(pid)
        if prod and prod.get('category_id'):
            user_categories[prod['category_id']] = user_categories.get(prod['category_id'], 0) + 2.0
    
    for pid in activity['views']:
        prod = fetch_product(pid)
        if prod and prod.get('category_id'):
            user_categories[prod['category_id']] = user_categories.get(prod['category_id'], 0) + 1.0
    
    print(f"🎯 Category Preferences (weighted):")
    if user_categories:
        # Get category names
        db = mysql.connector.connect(**DB)
        cursor = db.cursor(dictionary=True)
        cursor.execute("SELECT id, name FROM categories")
        categories = {c['id']: c['name'] for c in cursor.fetchall()}
        cursor.close()
        db.close()
        
        sorted_cats = sorted(user_categories.items(), key=lambda x: x[1], reverse=True)
        for cat_id, weight in sorted_cats:
            cat_name = categories.get(cat_id, f"Unknown (ID: {cat_id})")
            print(f"   {cat_name}: {weight:.1f} points")
    else:
        print("   No category preferences detected")
    print()
    
    # 6. Check if books exist in database
    db = mysql.connector.connect(**DB)
    cursor = db.cursor(dictionary=True)
    cursor.execute("SELECT id, name FROM categories WHERE name LIKE '%Book%'")
    book_categories = cursor.fetchall()
    print(f"📖 Book Categories in Database:")
    if book_categories:
        for cat in book_categories:
            print(f"   ID {cat['id']}: {cat['name']}")
            
            # Count products in this category
            cursor.execute("SELECT COUNT(*) as count FROM products WHERE category_id = %s", (cat['id'],))
            count = cursor.fetchone()['count']
            print(f"      Products: {count}")
            
            # Check if user interacted with any
            cursor.execute("""
                SELECT COUNT(DISTINCT vp.product_id) as views,
                       COUNT(DISTINCT pp.product_id) as purchases
                FROM products p
                LEFT JOIN visited_products vp ON p.id = vp.product_id AND vp.user_id = %s
                LEFT JOIN purchased_products pp ON p.id = pp.product_id AND pp.user_id = %s
                WHERE p.category_id = %s
            """, (user_id, user_id, cat['id']))
            stats = cursor.fetchone()
            print(f"      User views: {stats['views']}, purchases: {stats['purchases']}")
            
            # Get book product IDs
            cursor.execute("SELECT id FROM products WHERE category_id = %s", (cat['id'],))
            book_ids = [r['id'] for r in cursor.fetchall()]
            print(f"      Book product IDs: {book_ids}")
            
            # Check which are excluded
            excluded_books = set(book_ids) & interacted
            available_books = set(book_ids) - interacted
            print(f"      Excluded books: {len(excluded_books)} ({list(excluded_books)})")
            print(f"      Available books: {len(available_books)} ({list(available_books)})")
    else:
        print("   No 'Book' category found in database")
    cursor.close()
    db.close()
    print()
    
    # 7. Check Qdrant for books
    print(f"🔍 Checking Qdrant for books...")
    from qdrant_client import QdrantClient
    client = QdrantClient(url="http://127.0.0.1:6333")
    
    # Get all products from Qdrant
    try:
        scroll_result = client.scroll(
            collection_name="products",
            limit=1000,
            with_payload=True
        )
        
        qdrant_products = {p.payload['id']: p.payload for p in scroll_result[0]}
        
        # Find books in Qdrant
        book_cat_ids = [cat['id'] for cat in book_categories] if book_categories else []
        books_in_qdrant = []
        for pid, payload in qdrant_products.items():
            if payload.get('category_id') in book_cat_ids:
                books_in_qdrant.append(pid)
        
        print(f"   Books in Qdrant: {len(books_in_qdrant)} products")
        print(f"   Book IDs in Qdrant: {books_in_qdrant}")
        
        if books_in_qdrant:
            available_in_qdrant = set(books_in_qdrant) - interacted
            print(f"   Available books in Qdrant (not excluded): {len(available_in_qdrant)}")
            print(f"   Available book IDs: {list(available_in_qdrant)}")
        else:
            print(f"   ⚠️  WARNING: No books found in Qdrant! You may need to sync products.")
    except Exception as e:
        print(f"   ❌ Error checking Qdrant: {e}")
    
    print(f"\n{'='*60}\n")

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print("Usage: python debug_user.py <user_id>")
        sys.exit(1)
    
    user_id = int(sys.argv[1])
    debug_user(user_id)

