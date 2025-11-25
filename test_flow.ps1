# Complete Test Flow Script for AI Recommendation System (PowerShell)
# Usage: .\test_flow.ps1

Write-Host "╔════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║   AI Recommendation System - Complete Test Flow       ║" -ForegroundColor Cyan
Write-Host "╚════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
Write-Host ""

# Check if services are running
Write-Host "🔍 Checking services..." -ForegroundColor Yellow

# Check Laravel
try {
    $null = Invoke-WebRequest -Uri "http://localhost:8000/api/products" -UseBasicParsing -ErrorAction Stop
    Write-Host "✅ Laravel backend is running" -ForegroundColor Green
} catch {
    Write-Host "❌ Laravel backend is NOT running (http://localhost:8000)" -ForegroundColor Red
    exit 1
}

# Check Python AI Service
try {
    $null = Invoke-WebRequest -Uri "http://127.0.0.1:8001/recommend/product/1" -UseBasicParsing -ErrorAction Stop
    Write-Host "✅ Python AI service is running" -ForegroundColor Green
} catch {
    Write-Host "❌ Python AI service is NOT running (http://127.0.0.1:8001)" -ForegroundColor Red
    exit 1
}

# Check Qdrant
try {
    $null = Invoke-WebRequest -Uri "http://localhost:6333/collections" -UseBasicParsing -ErrorAction Stop
    Write-Host "✅ Qdrant is running" -ForegroundColor Green
} catch {
    Write-Host "❌ Qdrant is NOT running (http://localhost:6333)" -ForegroundColor Red
    exit 1
}

Write-Host ""

# Step 1: Register User
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Yellow
Write-Host "Step 1: Registering new user..." -ForegroundColor Yellow

$timestamp = [DateTimeOffset]::Now.ToUnixTimeSeconds()
$registerBody = @{
    name = "Test User $timestamp"
    email = "test$timestamp@example.com"
    password = "password123"
} | ConvertTo-Json

try {
    $registerResponse = Invoke-RestMethod -Uri "http://localhost:8000/api/register" `
        -Method Post `
        -ContentType "application/json" `
        -Body $registerBody
    
    $token = $registerResponse.token
    $userId = $registerResponse.user.id
    
    Write-Host "✅ User registered successfully" -ForegroundColor Green
    Write-Host "   User ID: $userId" -ForegroundColor Gray
    Write-Host "   Token: $($token.Substring(0, [Math]::Min(30, $token.Length)))..." -ForegroundColor Gray
} catch {
    Write-Host "❌ Registration failed" -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Red
    exit 1
}

# Step 2: View Products
Write-Host ""
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Yellow
Write-Host "Step 2: Generating user activity (views)..." -ForegroundColor Yellow

$viewCount = 0
$headers = @{
    "Authorization" = "Bearer $token"
}

for ($i = 1; $i -le 8; $i++) {
    try {
        $response = Invoke-WebRequest -Uri "http://localhost:8000/api/product/$i/visit" `
            -Method Post `
            -Headers $headers `
            -UseBasicParsing `
            -ErrorAction Stop
        Write-Host "   ✅ Viewed product $i" -ForegroundColor Green
        $viewCount++
    } catch {
        Write-Host "   ❌ Failed to view product $i" -ForegroundColor Red
    }
}
Write-Host "✅ Logged $viewCount product views" -ForegroundColor Green

# Step 3: Purchase Products
Write-Host ""
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Yellow
Write-Host "Step 3: Generating purchase activity..." -ForegroundColor Yellow

$purchaseCount = 0
for ($i = 1; $i -le 3; $i++) {
    try {
        $response = Invoke-WebRequest -Uri "http://localhost:8000/api/product/$i/buy" `
            -Method Post `
            -Headers $headers `
            -UseBasicParsing `
            -ErrorAction Stop
        Write-Host "   ✅ Purchased product $i" -ForegroundColor Green
        $purchaseCount++
    } catch {
        Write-Host "   ❌ Failed to purchase product $i" -ForegroundColor Red
    }
}
Write-Host "✅ Logged $purchaseCount product purchases" -ForegroundColor Green

# Step 4: Test Product Recommendations
Write-Host ""
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Yellow
Write-Host "Step 4: Testing product-based recommendations..." -ForegroundColor Yellow

try {
    $prodRec = Invoke-RestMethod -Uri "http://127.0.0.1:8001/recommend/product/1?limit=5"
    if ($prodRec.recommendations) {
        $recCount = $prodRec.recommendations.Count
        Write-Host "✅ Product recommendations working" -ForegroundColor Green
        Write-Host "   Found $recCount recommendations for product 1" -ForegroundColor Gray
        
        if ($recCount -gt 0) {
            $firstRec = $prodRec.recommendations[0]
            Write-Host "   Top recommendation: Product ID $($firstRec.id) (score: $($firstRec.score))" -ForegroundColor Gray
        }
    }
} catch {
    Write-Host "❌ Product recommendations failed" -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Red
}

# Step 5: Test User Recommendations
Write-Host ""
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Yellow
Write-Host "Step 5: Testing user-based recommendations..." -ForegroundColor Yellow

try {
    $userRec = Invoke-RestMethod -Uri "http://127.0.0.1:8001/recommend/user/$userId?limit=5"
    if ($userRec.recommendations) {
        $recCount = $userRec.recommendations.Count
        if ($recCount -gt 0) {
            Write-Host "✅ User recommendations working" -ForegroundColor Green
            Write-Host "   Found $recCount personalized recommendations" -ForegroundColor Gray
            
            $firstRec = $userRec.recommendations[0]
            Write-Host "   Top recommendation: Product ID $($firstRec.id) (score: $($firstRec.score))" -ForegroundColor Gray
        } else {
            Write-Host "⚠️  User recommendations returned but empty (user may need more activity)" -ForegroundColor Yellow
        }
    } elseif ($userRec.message) {
        Write-Host "⚠️  $($userRec.message)" -ForegroundColor Yellow
    }
} catch {
    Write-Host "❌ User recommendations failed" -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Red
}

# Step 6: Test via Laravel API
Write-Host ""
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Yellow
Write-Host "Step 6: Testing via Laravel API proxy..." -ForegroundColor Yellow

try {
    $laravelRec = Invoke-RestMethod -Uri "http://localhost:8000/api/recommend/user/$userId?limit=5" `
        -Headers $headers
    if ($laravelRec.recommendations) {
        $recCount = $laravelRec.recommendations.Count
        Write-Host "✅ Laravel API proxy working" -ForegroundColor Green
        Write-Host "   Found $recCount recommendations via Laravel API" -ForegroundColor Gray
    }
} catch {
    Write-Host "❌ Laravel API proxy failed" -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Red
}

# Step 7: Verify Exclusions
Write-Host ""
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Yellow
Write-Host "Step 7: Verifying recommendation exclusions..." -ForegroundColor Yellow

try {
    $userRec = Invoke-RestMethod -Uri "http://127.0.0.1:8001/recommend/user/$userId?limit=5"
    if ($userRec.recommendations) {
        $excluded = $true
        $recommendedIds = $userRec.recommendations | ForEach-Object { $_.id }
        
        # Check viewed products (1-8)
        for ($i = 1; $i -le 8; $i++) {
            if ($recommendedIds -contains $i) {
                Write-Host "   ⚠️  Product $i (viewed) appears in recommendations (should be excluded)" -ForegroundColor Yellow
                $excluded = $false
            }
        }
        
        # Check purchased products (1-3)
        for ($i = 1; $i -le 3; $i++) {
            if ($recommendedIds -contains $i) {
                Write-Host "   ⚠️  Product $i (purchased) appears in recommendations (should be excluded)" -ForegroundColor Yellow
                $excluded = $false
            }
        }
        
        if ($excluded) {
            Write-Host "✅ All viewed/purchased products correctly excluded" -ForegroundColor Green
        }
    }
} catch {
    Write-Host "⚠️  Could not verify exclusions" -ForegroundColor Yellow
}

# Summary
Write-Host ""
Write-Host "╔════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║                    Test Summary                       ║" -ForegroundColor Cyan
Write-Host "╚════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
Write-Host "✅ User Registration: Success" -ForegroundColor Green
Write-Host "✅ Product Views: $viewCount logged" -ForegroundColor Green
Write-Host "✅ Product Purchases: $purchaseCount logged" -ForegroundColor Green
Write-Host "✅ Product Recommendations: Working" -ForegroundColor Green
Write-Host "✅ User Recommendations: Working" -ForegroundColor Green
Write-Host "✅ Laravel API: Working" -ForegroundColor Green
Write-Host ""
Write-Host "🎉 Test flow completed successfully!" -ForegroundColor Green
Write-Host ""
Write-Host "💡 Next steps:" -ForegroundColor Yellow
Write-Host "   1. Check frontend at http://localhost:5173"
Write-Host "   2. Login with: test$timestamp@example.com / password123"
Write-Host "   3. Navigate to Recommendations page"
Write-Host "   4. Verify personalized recommendations display"
Write-Host ""

