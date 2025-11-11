import json
import pandas as pd
import numpy as np
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity
import mysql.connector
from datetime import datetime

# ========================
# 1️⃣ Connect to Database
# ========================
db = mysql.connector.connect(
    host="127.0.0.1",
    user="root",
    password="",  # your DB password
    database="ai_recommand"
)
cursor = db.cursor(dictionary=True)

# ========================
# 2️⃣ Load Data (with JOIN to categories)
# ========================
cursor.execute("""
    SELECT 
        p.id, p.name, p.description, p.tags, p.price, p.image_url, 
        c.name AS category_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
""")
products = pd.DataFrame(cursor.fetchall())

cursor.execute("SELECT user_id, product_id FROM visited_products")
visited = pd.DataFrame(cursor.fetchall())

cursor.execute("SELECT user_id, product_id FROM purchased_products")
purchased = pd.DataFrame(cursor.fetchall())

if products.empty:
    print("⚠️ No products found. Exiting.")
    exit()

# ========================
# 3️⃣ Prepare Product Features (with category join)
# ========================
def combine_features(row):
    name = str(row.get('name', ''))
    desc = str(row.get('description', ''))
    category = str(row.get('category_name', ''))
    tags = ""
    try:
        if row.get('tags'):
            tags_data = json.loads(row['tags']) if isinstance(row['tags'], str) else row['tags']
            tags = " ".join(tags_data)
    except Exception:
        tags = ""
    return f"{name} {desc} {category} {tags}".strip()

products['combined_text'] = products.apply(combine_features, axis=1)

# ========================
# 4️⃣ TF-IDF Vectorization (Content Similarity)
# ========================
tfidf = TfidfVectorizer(stop_words='english')
tfidf_matrix = tfidf.fit_transform(products['combined_text'].fillna(''))
content_sim = cosine_similarity(tfidf_matrix, tfidf_matrix)

# ========================
# 5️⃣ Collaborative Filtering (Co-visitation)
# ========================
interactions = pd.concat([visited, purchased]).drop_duplicates()
user_groups = interactions.groupby('user_id')['product_id'].apply(list).to_dict()

recommendations = {}

id_to_index = {pid: idx for idx, pid in enumerate(products['id'])}

for user, items in user_groups.items():
    valid_indices = [id_to_index[i] for i in items if i in id_to_index]
    if not valid_indices:
        continue  # skip if user has no matching products

    # Average similarity scores for all user's interacted items
    scores = content_sim[valid_indices].mean(axis=0)
    top_indices = np.argsort(scores)[::-1]

    top_recs = [
        int(products.iloc[i]['id'])
        for i in top_indices
        if int(products.iloc[i]['id']) not in items
    ][:5]

    recommendations[user] = top_recs

# ========================
# 6️⃣ Save Results to DB
# ========================
cursor.execute("DELETE FROM recommendations")

for user_id, recs in recommendations.items():
    cursor.execute("""
        INSERT INTO recommendations (user_id, recommended_products, created_at, updated_at)
        VALUES (%s, %s, %s, %s)
    """, (user_id, json.dumps(recs), datetime.now(), datetime.now()))

db.commit()
print(f"✅ AI model retrained successfully for {len(recommendations)} users.")
