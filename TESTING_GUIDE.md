# 🧪 Complete Testing Guide - AI Recommendation System

This guide walks you through testing the entire recommendation system from start to finish.

---

## ✅ Prerequisites Check

Before testing, ensure all services are running:

```bash
# Check Laravel Backend
curl http://localhost:8000/api/products
# Should return JSON with products

# Check Python AI Service
curl http://127.0.0.1:8001/recommend/product/1
# Should return recommendations or error (if no products synced)

# Check Qdrant
curl http://localhost:6333/collections
# Should return JSON with collections

# Check Frontend
# Open http://localhost:5173 in browser
```

---

## 🎯 Complete Testing Flow

### Phase 1: Initial Setup & Data Preparation

#### Step 1.1: Verify Database Setup

```bash
# Connect to MySQL
mysql -u root -p

# Check database exists
USE ai_recommand;
SHOW TABLES;
# Should show: users, products, categories, visited_products, purchased_products, etc.

# Check if data exists
SELECT COUNT(*) FROM products;
SELECT COUNT(*) FROM categories;
SELECT COUNT(*) FROM users;
```

#### Step 1.2: Sync Products to Qdrant

```bash
# Method 1: Via API (Recommended)
curl -X POST http://127.0.0.1:8001/sync

# Method 2: Via Python script
cd ai
venv\Scripts\activate  # Windows
python sync_products.py

# Verify in Qdrant Dashboard
# Open: http://localhost:6333/dashboard
# Should see "products" collection with your products
```

**Expected Result:**
```json
{
  "status": "ok"
}
```

---

### Phase 2: User Registration & Authentication

#### Step 2.1: Register a New User

**Via Frontend:**
1. Open `http://localhost:5173`
2. Click "Register" or navigate to `/register`
3. Fill in:
   - Name: `Test User`
   - Email: `test@example.com`
   - Password: `password123`
4. Click "Register"

**Via API (curl):**
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123"
  }'
```

**Expected Result:**
```json
{
  "token": "1|abc123...",
  "user": {
    "id": 1,
    "name": "Test User",
    "email": "test@example.com",
    "role": "user"
  }
}
```

**Save the token** for subsequent API calls:
```bash
export TOKEN="1|abc123..."  # Replace with actual token
```

#### Step 2.2: Login (Alternative)

**Via Frontend:**
1. Navigate to `/login`
2. Enter email and password
3. Click "Login"

**Via API:**
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'
```

---

### Phase 3: Browse Products & Generate Activity

#### Step 3.1: View Product List

**Via Frontend:**
1. After login, you should be redirected to `/products`
2. Browse the product list
3. Click on a product to view details

**Via API:**
```bash
# Get all products
curl http://localhost:8000/api/products

# Get specific product
curl http://localhost:8000/api/products/1
```

#### Step 3.2: Log Product Views (Generate User Activity)

**Via Frontend:**
1. Click on multiple products (at least 5-10)
2. Each click automatically logs a view

**Via API (Manual):**
```bash
# View product 1
curl -X POST http://localhost:8000/api/product/1/visit \
  -H "Authorization: Bearer $TOKEN"

# View product 2
curl -X POST http://localhost:8000/api/product/2/visit \
  -H "Authorization: Bearer $TOKEN"

# View product 3
curl -X POST http://localhost:8000/api/product/3/visit \
  -H "Authorization: Bearer $TOKEN"

# View more products (4, 5, 6, etc.)
```

**Verify Views Logged:**
```bash
# Connect to MySQL
mysql -u root -p ai_recommand

# Check visited products
SELECT * FROM visited_products WHERE user_id = 1;
```

#### Step 3.3: Log Product Purchases (Generate Purchase Activity)

**Via Frontend:**
1. On product detail page, click "Buy" or "Purchase" button
2. Purchase at least 2-3 products

**Via API (Manual):**
```bash
# Purchase product 1
curl -X POST http://localhost:8000/api/product/1/buy \
  -H "Authorization: Bearer $TOKEN"

# Purchase product 5
curl -X POST http://localhost:8000/api/product/5/buy \
  -H "Authorization: Bearer $TOKEN"

# Purchase product 8
curl -X POST http://localhost:8000/api/product/8/buy \
  -H "Authorization: Bearer $TOKEN"
```

**Verify Purchases Logged:**
```sql
SELECT * FROM purchased_products WHERE user_id = 1;
```

---

### Phase 4: Test Product-Based Recommendations

#### Step 4.1: Test via Python API (Direct)

```bash
# Get recommendations for product ID 1
curl "http://127.0.0.1:8001/recommend/product/1?limit=10"

# Get recommendations for product ID 5
curl "http://127.0.0.1:8001/recommend/product/5?limit=10"
```

**Expected Result:**
```json
{
  "product_id": 1,
  "recommendations": [
    {
      "id": 15,
      "score": 0.92,
      "payload": {
        "id": 15,
        "name": "Similar Product",
        "description": "...",
        "category_name": "Electronics",
        "price": 99.99,
        "tags": "[\"tag1\", \"tag2\"]"
      }
    },
    {
      "id": 23,
      "score": 0.88,
      "payload": {...}
    }
  ]
}
```

#### Step 4.2: Test via Frontend

1. Navigate to a product detail page
2. Scroll down to see "Similar Products" or "Recommendations" section
3. Verify similar products are displayed

---

### Phase 5: Test User-Based Recommendations

#### Step 5.1: Test via Python API (Direct)

```bash
# Get personalized recommendations for user ID 1
curl "http://127.0.0.1:8001/recommend/user/1?limit=10"
```

**Expected Result (if user has activity):**
```json
{
  "user_id": 1,
  "source": "user-profile",
  "recommendations": [
    {
      "id": 12,
      "score": 0.95,
      "payload": {
        "id": 12,
        "name": "Recommended Product",
        "description": "...",
        "category_name": "Electronics",
        "price": 149.99,
        "tags": "[\"tag1\", \"tag2\"]"
      }
    },
    {
      "id": 18,
      "score": 0.91,
      "payload": {...}
    }
  ]
}
```

**Expected Result (if user has NO activity):**
```json
{
  "user_id": 1,
  "source": "user-profile",
  "message": "No user activity found. Please browse or purchase products to get recommendations.",
  "recommendations": []
}
```

#### Step 5.2: Test via Laravel API

```bash
# Get user recommendations (requires authentication)
curl "http://localhost:8000/api/recommend/user/1?limit=10" \
  -H "Authorization: Bearer $TOKEN"
```

#### Step 5.3: Test via Frontend

1. Navigate to `/recommendations` page
2. You should see personalized recommendations based on your activity
3. Verify:
   - Products you've already viewed/purchased are NOT shown
   - Recommendations are relevant to your browsing history
   - Scores are displayed (if implemented in UI)

---

### Phase 6: Verify Recommendation Quality

#### Step 6.1: Check Recommendation Logic

**Verify exclusions:**
- Products you've viewed should NOT appear in recommendations
- Products you've purchased should NOT appear in recommendations

**Verify relevance:**
- Recommendations should be from similar categories
- Recommendations should have similar tags
- Scores should be reasonable (0.7 - 1.0 for good matches)

#### Step 6.2: Test Edge Cases

**Test 1: New User (No Activity)**
```bash
# Register new user
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "New User",
    "email": "newuser@example.com",
    "password": "password123"
  }'

# Get recommendations (should return empty or message)
curl "http://127.0.0.1:8001/recommend/user/2?limit=10"
```

**Test 2: User with Only Views**
```bash
# Login as user, view 5 products, then get recommendations
# Should still return recommendations
```

**Test 3: User with Only Purchases**
```bash
# Login as user, purchase 3 products, then get recommendations
# Should return recommendations (purchases weighted higher)
```

**Test 4: Product with No Similar Products**
```bash
# Get recommendations for a product with unique category/tags
# Should still return some results (even if lower scores)
```

---

## 📊 Automated Test Scripts

### Bash Script (Linux/Mac)

Here's a complete bash script to test the entire flow:

```bash
#!/bin/bash

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}🧪 Starting Complete Test Flow...${NC}\n"

# Step 1: Register User
echo -e "${YELLOW}Step 1: Registering new user...${NC}"
REGISTER_RESPONSE=$(curl -s -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test'$(date +%s)'@example.com",
    "password": "password123"
  }')

TOKEN=$(echo $REGISTER_RESPONSE | grep -o '"token":"[^"]*' | cut -d'"' -f4)
USER_ID=$(echo $REGISTER_RESPONSE | grep -o '"id":[0-9]*' | cut -d':' -f2)

if [ -z "$TOKEN" ]; then
  echo -e "${RED}❌ Registration failed${NC}"
  exit 1
fi

echo -e "${GREEN}✅ User registered: ID=$USER_ID${NC}"
echo "Token: ${TOKEN:0:20}..."

# Step 2: View Products
echo -e "\n${YELLOW}Step 2: Generating user activity (views)...${NC}"
for i in {1..5}; do
  curl -s -X POST "http://localhost:8000/api/product/$i/visit" \
    -H "Authorization: Bearer $TOKEN" > /dev/null
  echo -e "${GREEN}✅ Viewed product $i${NC}"
done

# Step 3: Purchase Products
echo -e "\n${YELLOW}Step 3: Generating purchase activity...${NC}"
for i in {1..3}; do
  curl -s -X POST "http://localhost:8000/api/product/$i/buy" \
    -H "Authorization: Bearer $TOKEN" > /dev/null
  echo -e "${GREEN}✅ Purchased product $i${NC}"
done

# Step 4: Test Product Recommendations
echo -e "\n${YELLOW}Step 4: Testing product-based recommendations...${NC}"
PROD_REC=$(curl -s "http://127.0.0.1:8001/recommend/product/1?limit=5")
if echo "$PROD_REC" | grep -q "recommendations"; then
  echo -e "${GREEN}✅ Product recommendations working${NC}"
else
  echo -e "${RED}❌ Product recommendations failed${NC}"
  echo "$PROD_REC"
fi

# Step 5: Test User Recommendations
echo -e "\n${YELLOW}Step 5: Testing user-based recommendations...${NC}"
USER_REC=$(curl -s "http://127.0.0.1:8001/recommend/user/$USER_ID?limit=5")
if echo "$USER_REC" | grep -q "recommendations"; then
  REC_COUNT=$(echo "$USER_REC" | grep -o '"id":[0-9]*' | wc -l)
  echo -e "${GREEN}✅ User recommendations working (found $REC_COUNT recommendations)${NC}"
else
  echo -e "${RED}❌ User recommendations failed${NC}"
  echo "$USER_REC"
fi

# Step 6: Test via Laravel API
echo -e "\n${YELLOW}Step 6: Testing via Laravel API...${NC}"
LARAVEL_REC=$(curl -s "http://localhost:8000/api/recommend/user/$USER_ID?limit=5" \
  -H "Authorization: Bearer $TOKEN")
if echo "$LARAVEL_REC" | grep -q "recommendations"; then
  echo -e "${GREEN}✅ Laravel API working${NC}"
else
  echo -e "${RED}❌ Laravel API failed${NC}"
  echo "$LARAVEL_REC"
fi

echo -e "\n${GREEN}🎉 Test flow completed!${NC}"
```

**Run the test script:**
```bash
# Make executable (Linux/Mac)
chmod +x test_flow.sh

# Run
./test_flow.sh
```

### PowerShell Script (Windows)

A PowerShell version is also available for Windows users:

**Run the test script:**
```powershell
# Run in PowerShell
.\test_flow.ps1
```

Both scripts will:
- ✅ Check all services are running
- ✅ Register a new test user
- ✅ Generate user activity (views and purchases)
- ✅ Test product-based recommendations
- ✅ Test user-based recommendations
- ✅ Test Laravel API proxy
- ✅ Verify exclusions work correctly
- ✅ Display a summary of results

---

## 🧪 Manual Testing Checklist

### ✅ Setup & Configuration
- [ ] All services running (Laravel, Python AI, Qdrant, Frontend)
- [ ] Database seeded with products and categories
- [ ] Products synced to Qdrant
- [ ] Qdrant dashboard accessible

### ✅ Authentication
- [ ] User registration works
- [ ] User login works
- [ ] Token is stored and used correctly
- [ ] Protected routes require authentication

### ✅ Product Browsing
- [ ] Product list displays correctly
- [ ] Product search works
- [ ] Category filtering works
- [ ] Product details page loads

### ✅ User Activity Tracking
- [ ] Product views are logged
- [ ] Product purchases are logged
- [ ] Activity appears in database

### ✅ Product-Based Recommendations
- [ ] Recommendations API returns results
- [ ] Similar products are relevant
- [ ] Scores are reasonable
- [ ] Recommendations exclude the source product

### ✅ User-Based Recommendations
- [ ] New user (no activity) returns appropriate message
- [ ] User with views gets recommendations
- [ ] User with purchases gets recommendations
- [ ] Recommendations exclude viewed/purchased products
- [ ] Recommendations are personalized
- [ ] Scores reflect relevance

### ✅ Frontend Integration
- [ ] Recommendations page loads
- [ ] Recommendations display correctly
- [ ] Product cards show properly
- [ ] Navigation works
- [ ] Error handling works

---

## 🔍 Verification Queries

### Check User Activity in Database

```sql
-- View all user activity
SELECT 
  u.id as user_id,
  u.name,
  u.email,
  COUNT(DISTINCT vp.product_id) as views_count,
  COUNT(DISTINCT pp.product_id) as purchases_count
FROM users u
LEFT JOIN visited_products vp ON u.id = vp.user_id
LEFT JOIN purchased_products pp ON u.id = pp.user_id
GROUP BY u.id;

-- View specific user's activity
SELECT 
  'view' as activity_type,
  p.name as product_name,
  vp.visited_at as activity_date
FROM visited_products vp
JOIN products p ON vp.product_id = p.id
WHERE vp.user_id = 1

UNION ALL

SELECT 
  'purchase' as activity_type,
  p.name as product_name,
  pp.purchased_at as activity_date
FROM purchased_products pp
JOIN products p ON pp.product_id = p.id
WHERE pp.user_id = 1

ORDER BY activity_date DESC;
```

### Check Qdrant Collection

```bash
# List all collections
curl http://localhost:6333/collections

# Get collection info
curl http://localhost:6333/collections/products

# Count points in collection
curl http://localhost:6333/collections/products | grep -o '"points_count":[0-9]*'
```

---

## 🐛 Common Issues & Solutions

### Issue: "No recommendations returned"

**Check:**
1. Products synced to Qdrant? → Run sync again
2. User has activity? → View/purchase some products
3. Qdrant running? → Check `http://localhost:6333/dashboard`

### Issue: "Recommendations include viewed products"

**Check:**
- Verify `get_user_interacted_products()` is working
- Check exclusion logic in `recommend_for_user()`

### Issue: "Low recommendation scores"

**Check:**
- Products have sufficient data (name, description, tags)?
- Categories and tags are populated?
- User activity is diverse enough?

### Issue: "API returns 503"

**Check:**
- Python AI service running?
- `AI_SERVICE_URL` in Laravel `.env` is correct?
- Network connectivity between Laravel and Python service?

---

## 📈 Performance Testing

### Test Response Times

```bash
# Time product recommendations
time curl -s "http://127.0.0.1:8001/recommend/product/1?limit=10"

# Time user recommendations
time curl -s "http://127.0.0.1:8001/recommend/user/1?limit=10"
```

**Expected:**
- Product recommendations: < 500ms
- User recommendations: < 1000ms (includes DB queries + embedding)

### Load Testing

```bash
# Test with multiple concurrent requests
for i in {1..10}; do
  curl -s "http://127.0.0.1:8001/recommend/product/1?limit=10" &
done
wait
```

---

## 🎯 Success Criteria

Your system is working correctly if:

1. ✅ Users can register and login
2. ✅ Product views and purchases are tracked
3. ✅ Product-based recommendations return relevant results
4. ✅ User-based recommendations are personalized
5. ✅ Recommendations exclude already-interacted products
6. ✅ Frontend displays recommendations correctly
7. ✅ All API endpoints respond correctly
8. ✅ Error handling works (no activity, service down, etc.)

---

## 📝 Test Report Template

After testing, document your results:

```
Test Date: ___________
Tester: ___________

Services Status:
- Laravel Backend: ✅ / ❌
- Python AI Service: ✅ / ❌
- Qdrant: ✅ / ❌
- Frontend: ✅ / ❌

Test Results:
- User Registration: ✅ / ❌
- User Login: ✅ / ❌
- Product Views: ✅ / ❌
- Product Purchases: ✅ / ❌
- Product Recommendations: ✅ / ❌
- User Recommendations: ✅ / ❌
- Frontend Integration: ✅ / ❌

Issues Found:
1. ___________
2. ___________

Performance:
- Product Recommendations: ___ms
- User Recommendations: ___ms
```

---

## 🚀 Next Steps After Testing

Once testing is complete:

1. **Monitor logs** for any errors
2. **Check database** for data integrity
3. **Verify Qdrant** collection health
4. **Test with real user data** (if available)
5. **Optimize** based on performance results
6. **Document** any customizations or changes

---

Happy Testing! 🎉

