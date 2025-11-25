# 🔧 Books Recommendation Fix

## Problem
User viewed and purchased Books, but recommendations are showing Home & Kitchen, Electronics, etc., with no Books appearing.

## Root Causes Identified

1. **Search limit too small** - Only fetching 30 results, books might not be in top 30
2. **Weak category preference** - Category boost was only +0.15, not strong enough
3. **No category prioritization** - Algorithm didn't ensure preferred categories appear
4. **Possible exclusions** - All books might be excluded if user viewed/purchased them all

## Fixes Applied

### 1. Increased Search Limit
- **Before**: `min(k * 3, 50)` = 30 results max
- **After**: `max(k * 5, 100)` = 100+ results
- Ensures more products from all categories are considered

### 2. Weighted Category Tracking
- Purchases: weight 2.0
- Views: weight 1.0
- Categories are now tracked with weights based on activity type

### 3. Stronger Category Boosting
- **Multiplier**: 1.0x to 2.5x based on category preference strength
- **Fixed boost**: +0.3 for preferred categories
- **Tag boost**: +0.15 per shared tag (increased from 0.10)

### 4. Category Prioritization (70/30 Split)
- 70% of recommendations from preferred categories
- 30% from other categories
- Ensures preferred categories are well-represented

### 5. Category-Specific Fallback Search
- If preferred categories are < 50% of results, do explicit category-filtered search
- Uses Qdrant filter to find products in preferred categories
- Ensures books appear even if not in top similarity results

## How to Test

### Step 1: Restart Python AI Service
```bash
cd ai
venv\Scripts\activate  # Windows
# Kill existing process if running
uvicorn service:app --port 8001 --reload
```

### Step 2: Run Diagnostic Script
```bash
cd ai
python debug_user.py 6
```

This will show:
- User's activity (views/purchases)
- Excluded products
- Category preferences
- Books in database
- Books in Qdrant
- Available books (not excluded)

### Step 3: Test Recommendations
```bash
curl "http://127.0.0.1:8001/recommend/user/6?limit=10"
```

### Expected Results
- **At least 7 out of 10 recommendations should be Books** (if available)
- **Higher scores** (0.6+ instead of 0.4)
- **Books should appear** even if similarity is lower

## Debugging Checklist

### ✅ Check 1: Are Books in Database?
```sql
SELECT id, name FROM categories WHERE name LIKE '%Book%';
SELECT COUNT(*) FROM products WHERE category_id = 3;  -- Assuming Books is ID 3
```

### ✅ Check 2: Are Books in Qdrant?
```bash
# Check Qdrant dashboard
http://localhost:6333/dashboard

# Or via API
curl "http://localhost:6333/collections/products/points/scroll?limit=1000" | grep -i "book"
```

If books are missing, sync:
```bash
curl -X POST http://127.0.0.1:8001/sync
```

### ✅ Check 3: Are All Books Excluded?
```sql
-- Check user's book interactions
SELECT 
    p.id,
    p.name,
    CASE 
        WHEN vp.product_id IS NOT NULL THEN 'VIEWED'
        WHEN pp.product_id IS NOT NULL THEN 'PURCHASED'
        ELSE 'NONE'
    END as interaction
FROM products p
LEFT JOIN visited_products vp ON p.id = vp.product_id AND vp.user_id = 6
LEFT JOIN purchased_products pp ON p.id = pp.product_id AND pp.user_id = 6
WHERE p.category_id = 3  -- Books category ID
ORDER BY interaction;
```

If ALL books are viewed/purchased, they will be excluded. Solution: Add more books to database.

### ✅ Check 4: What Categories Does User Prefer?
Run the diagnostic script:
```bash
python debug_user.py 6
```

Look for "Category Preferences" section. Books should have the highest weight if user purchased books.

## Common Issues & Solutions

### Issue: Still No Books in Recommendations

**Possible Causes:**
1. All books excluded (user viewed/purchased all)
2. Books not in Qdrant
3. Books category ID mismatch

**Solutions:**
1. Check exclusions with diagnostic script
2. Re-sync products to Qdrant
3. Verify category IDs match between database and Qdrant

### Issue: Low Scores for Books

**Possible Causes:**
1. User profile vector doesn't match book embeddings well
2. Limited user activity (only 2 purchases)

**Solutions:**
1. User needs more activity (more views/purchases)
2. The category-specific fallback should help

### Issue: Books Appear But Low in List

**Solution:**
The 70/30 prioritization should fix this. If books still appear low, the category-specific search will boost them.

## Code Changes Summary

### File: `ai/recommend.py`

1. **Increased search limit** (line ~121)
2. **Weighted category tracking** (lines ~138-158)
3. **Stronger category boosting** (lines ~180-200)
4. **Category prioritization** (lines ~208-238)
5. **Category-specific fallback search** (lines ~221-280)

### New File: `ai/debug_user.py`

Diagnostic script to analyze user activity and identify issues.

## Next Steps

1. **Run diagnostic script** to identify the exact issue
2. **Check if books are excluded** - if all books are viewed/purchased, add more books
3. **Verify books in Qdrant** - re-sync if needed
4. **Test recommendations** - should see books now

## Expected Behavior After Fix

- **Books should appear** in recommendations (if not all excluded)
- **At least 70% from preferred categories** (Books + Home & Kitchen)
- **Higher scores** for preferred category products
- **Category-specific search** ensures books are found even with lower similarity

---

**If books still don't appear after these fixes, run the diagnostic script and share the output for further analysis.**

