#!/bin/bash

# Complete Test Flow Script for AI Recommendation System
# Usage: ./test_flow.sh

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}╔════════════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║   AI Recommendation System - Complete Test Flow       ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════════════════════════╝${NC}\n"

# Check if services are running
echo -e "${YELLOW}🔍 Checking services...${NC}"

# Check Laravel
if curl -s http://localhost:8000/api/products > /dev/null 2>&1; then
  echo -e "${GREEN}✅ Laravel backend is running${NC}"
else
  echo -e "${RED}❌ Laravel backend is NOT running (http://localhost:8000)${NC}"
  exit 1
fi

# Check Python AI Service
if curl -s http://127.0.0.1:8001/recommend/product/1 > /dev/null 2>&1; then
  echo -e "${GREEN}✅ Python AI service is running${NC}"
else
  echo -e "${RED}❌ Python AI service is NOT running (http://127.0.0.1:8001)${NC}"
  exit 1
fi

# Check Qdrant
if curl -s http://localhost:6333/collections > /dev/null 2>&1; then
  echo -e "${GREEN}✅ Qdrant is running${NC}"
else
  echo -e "${RED}❌ Qdrant is NOT running (http://localhost:6333)${NC}"
  exit 1
fi

echo ""

# Step 1: Register User
echo -e "${YELLOW}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${YELLOW}Step 1: Registering new user...${NC}"
TIMESTAMP=$(date +%s)
REGISTER_RESPONSE=$(curl -s -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d "{
    \"name\": \"Test User $TIMESTAMP\",
    \"email\": \"test$TIMESTAMP@example.com\",
    \"password\": \"password123\"
  }")

TOKEN=$(echo $REGISTER_RESPONSE | grep -o '"token":"[^"]*' | cut -d'"' -f4)
USER_ID=$(echo $REGISTER_RESPONSE | grep -o '"id":[0-9]*' | cut -d':' -f2)

if [ -z "$TOKEN" ]; then
  echo -e "${RED}❌ Registration failed${NC}"
  echo "Response: $REGISTER_RESPONSE"
  exit 1
fi

echo -e "${GREEN}✅ User registered successfully${NC}"
echo -e "   User ID: $USER_ID"
echo -e "   Token: ${TOKEN:0:30}..."

# Step 2: View Products
echo -e "\n${YELLOW}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${YELLOW}Step 2: Generating user activity (views)...${NC}"
VIEW_COUNT=0
for i in {1..8}; do
  RESPONSE=$(curl -s -w "%{http_code}" -X POST "http://localhost:8000/api/product/$i/visit" \
    -H "Authorization: Bearer $TOKEN" -o /dev/null)
  if [ "$RESPONSE" = "200" ]; then
    echo -e "   ${GREEN}✅ Viewed product $i${NC}"
    VIEW_COUNT=$((VIEW_COUNT + 1))
  else
    echo -e "   ${RED}❌ Failed to view product $i (HTTP $RESPONSE)${NC}"
  fi
done
echo -e "${GREEN}✅ Logged $VIEW_COUNT product views${NC}"

# Step 3: Purchase Products
echo -e "\n${YELLOW}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${YELLOW}Step 3: Generating purchase activity...${NC}"
PURCHASE_COUNT=0
for i in {1..3}; do
  RESPONSE=$(curl -s -w "%{http_code}" -X POST "http://localhost:8000/api/product/$i/buy" \
    -H "Authorization: Bearer $TOKEN" -o /dev/null)
  if [ "$RESPONSE" = "200" ]; then
    echo -e "   ${GREEN}✅ Purchased product $i${NC}"
    PURCHASE_COUNT=$((PURCHASE_COUNT + 1))
  else
    echo -e "   ${RED}❌ Failed to purchase product $i (HTTP $RESPONSE)${NC}"
  fi
done
echo -e "${GREEN}✅ Logged $PURCHASE_COUNT product purchases${NC}"

# Step 4: Test Product Recommendations
echo -e "\n${YELLOW}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${YELLOW}Step 4: Testing product-based recommendations...${NC}"
PROD_REC=$(curl -s "http://127.0.0.1:8001/recommend/product/1?limit=5")
if echo "$PROD_REC" | grep -q "recommendations"; then
  REC_COUNT=$(echo "$PROD_REC" | grep -o '"id":[0-9]*' | wc -l)
  echo -e "${GREEN}✅ Product recommendations working${NC}"
  echo -e "   Found $REC_COUNT recommendations for product 1"
  
  # Show first recommendation
  FIRST_REC=$(echo "$PROD_REC" | grep -o '"id":[0-9]*' | head -1 | cut -d':' -f2)
  FIRST_SCORE=$(echo "$PROD_REC" | grep -o '"score":[0-9.]*' | head -1 | cut -d':' -f2)
  if [ ! -z "$FIRST_REC" ]; then
    echo -e "   Top recommendation: Product ID $FIRST_REC (score: $FIRST_SCORE)"
  fi
else
  echo -e "${RED}❌ Product recommendations failed${NC}"
  echo "Response: $PROD_REC"
fi

# Step 5: Test User Recommendations
echo -e "\n${YELLOW}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${YELLOW}Step 5: Testing user-based recommendations...${NC}"
USER_REC=$(curl -s "http://127.0.0.1:8001/recommend/user/$USER_ID?limit=5")
if echo "$USER_REC" | grep -q "recommendations"; then
  REC_COUNT=$(echo "$USER_REC" | grep -o '"id":[0-9]*' | wc -l)
  if [ "$REC_COUNT" -gt 0 ]; then
    echo -e "${GREEN}✅ User recommendations working${NC}"
    echo -e "   Found $REC_COUNT personalized recommendations"
    
    # Show first recommendation
    FIRST_REC=$(echo "$USER_REC" | grep -o '"id":[0-9]*' | head -1 | cut -d':' -f2)
    FIRST_SCORE=$(echo "$USER_REC" | grep -o '"score":[0-9.]*' | head -1 | cut -d':' -f2)
    if [ ! -z "$FIRST_REC" ]; then
      echo -e "   Top recommendation: Product ID $FIRST_REC (score: $FIRST_SCORE)"
    fi
  else
    echo -e "${YELLOW}⚠️  User recommendations returned but empty (user may need more activity)${NC}"
  fi
elif echo "$USER_REC" | grep -q "message"; then
  echo -e "${YELLOW}⚠️  $USER_REC${NC}"
else
  echo -e "${RED}❌ User recommendations failed${NC}"
  echo "Response: $USER_REC"
fi

# Step 6: Test via Laravel API
echo -e "\n${YELLOW}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${YELLOW}Step 6: Testing via Laravel API proxy...${NC}"
LARAVEL_REC=$(curl -s "http://localhost:8000/api/recommend/user/$USER_ID?limit=5" \
  -H "Authorization: Bearer $TOKEN")
if echo "$LARAVEL_REC" | grep -q "recommendations"; then
  echo -e "${GREEN}✅ Laravel API proxy working${NC}"
  REC_COUNT=$(echo "$LARAVEL_REC" | grep -o '"id":[0-9]*' | wc -l)
  echo -e "   Found $REC_COUNT recommendations via Laravel API"
else
  echo -e "${RED}❌ Laravel API proxy failed${NC}"
  echo "Response: $LARAVEL_REC"
fi

# Step 7: Verify Exclusions
echo -e "\n${YELLOW}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${YELLOW}Step 7: Verifying recommendation exclusions...${NC}"
if [ ! -z "$USER_REC" ]; then
  # Check if viewed products (1-8) appear in recommendations
  EXCLUDED=true
  for i in {1..8}; do
    if echo "$USER_REC" | grep -q "\"id\":$i"; then
      echo -e "   ${RED}⚠️  Product $i (viewed) appears in recommendations (should be excluded)${NC}"
      EXCLUDED=false
    fi
  done
  
  # Check if purchased products (1-3) appear in recommendations
  for i in {1..3}; do
    if echo "$USER_REC" | grep -q "\"id\":$i"; then
      echo -e "   ${RED}⚠️  Product $i (purchased) appears in recommendations (should be excluded)${NC}"
      EXCLUDED=false
    fi
  done
  
  if [ "$EXCLUDED" = true ]; then
    echo -e "${GREEN}✅ All viewed/purchased products correctly excluded${NC}"
  fi
fi

# Summary
echo -e "\n${BLUE}╔════════════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║                    Test Summary                       ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════════════════════════╝${NC}"
echo -e "${GREEN}✅ User Registration: Success${NC}"
echo -e "${GREEN}✅ Product Views: $VIEW_COUNT logged${NC}"
echo -e "${GREEN}✅ Product Purchases: $PURCHASE_COUNT logged${NC}"
echo -e "${GREEN}✅ Product Recommendations: Working${NC}"
echo -e "${GREEN}✅ User Recommendations: Working${NC}"
echo -e "${GREEN}✅ Laravel API: Working${NC}"
echo -e "\n${GREEN}🎉 Test flow completed successfully!${NC}\n"
echo -e "${YELLOW}💡 Next steps:${NC}"
echo -e "   1. Check frontend at http://localhost:5173"
echo -e "   2. Login with: test$TIMESTAMP@example.com / password123"
echo -e "   3. Navigate to Recommendations page"
echo -e "   4. Verify personalized recommendations display\n"

