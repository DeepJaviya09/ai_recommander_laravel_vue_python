# 🧠 AI Product Recommender System

A full-stack, AI-powered recommendation engine built using:

- **Laravel 12** – Backend API + Admin Panel  
- **Vue 3** – Modern Frontend UI  
- **Python FastAPI** – Vector AI engine with SentenceTransformer embeddings  
- **Qdrant** – Local vector database  
- **SentenceTransformer (all-MiniLM-L6-v2)** – Fast & lightweight embedding model  

Provides Amazon-style **semantic product recommendations** using:
- **Product-based recommendations**: Similar products based on embeddings
- **User-based recommendations**: Personalized recommendations based on user activity (views, purchases)

---

## 📋 Prerequisites

Before you begin, ensure you have the following installed:

- **PHP 8.2+** with Composer
- **Node.js 18+** and npm
- **Python 3.9+** with pip
- **MySQL 8.0+** (or MariaDB)
- **Docker** (for Qdrant, optional - can use binary instead)
- **Git**

---

## 📁 Project Structure

```
ai_recommander/
│
├── backend/              # Laravel API + Admin
│   ├── app/
│   ├── database/
│   └── routes/
│
├── frontend/             # Vue 3 Frontend
│   ├── src/
│   └── package.json
│
└── ai/                   # Python AI Engine
    ├── service.py        # FastAPI server
    ├── embedder.py       # Embeddings (SentenceTransformer)
    ├── recommend.py      # Recommendation logic
    ├── sync_products.py  # Sync products → Qdrant
    ├── user_profile.py   # User profile building
    ├── requirements.txt
    └── venv/             # Python virtual environment
```

---

## 🚀 Step-by-Step Setup Guide

### 1️⃣ Database Setup

1. **Create MySQL database:**
   ```sql
   CREATE DATABASE ai_recommand CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

2. **Note your database credentials:**
   - Host: `127.0.0.1` (or `localhost`)
   - Database: `ai_recommand`
   - Username: `root` (or your MySQL username)
   - Password: (your MySQL password)

---

### 2️⃣ Backend Setup (Laravel)

1. **Navigate to backend directory:**
   ```bash
   cd backend
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install
   ```

3. **Create environment file:**
   ```bash
   cp .env.example .env
   # Or create .env manually if .env.example doesn't exist
   ```

4. **Configure `.env` file:**
   ```env
   APP_NAME="AI Recommender"
   APP_ENV=local
   APP_KEY=
   APP_DEBUG=true
   APP_URL=http://localhost:8000

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=ai_recommand
   DB_USERNAME=root
   DB_PASSWORD=

   # AI Service URL (Python FastAPI)
   AI_SERVICE_URL=http://127.0.0.1:8001
   ```

5. **Generate application key:**
   ```bash
   php artisan key:generate
   ```

6. **Run migrations and seeders:**
   ```bash
   php artisan migrate --seed
   ```
   This will create all necessary tables and populate initial data (categories, sample products, users).

7. **Start Laravel server:**
   ```bash
   php artisan serve
   ```
   Backend will run on `http://localhost:8000`

---

### 3️⃣ Python AI Service Setup

1. **Navigate to AI directory:**
   ```bash
   cd ai
   ```

2. **Create virtual environment:**
   ```bash
   # Windows
   python -m venv venv
   venv\Scripts\activate

   # Mac/Linux
   python3 -m venv venv
   source venv/bin/activate
   ```

3. **Install Python dependencies:**
   ```bash
   pip install -r requirements.txt
   ```
   
   **Note:** This will download the SentenceTransformer model (~90MB) on first run. It may take a few minutes.

4. **Configure database connection in `sync_products.py` and `user_profile.py`:**
   
   Edit `ai/sync_products.py` (lines 8-13) and `ai/user_profile.py` (imports DB from sync_products):
   ```python
   DB = {
       "host": "127.0.0.1",
       "user": "root",        # Your MySQL username
       "password": "",        # Your MySQL password
       "database": "ai_recommand"
   }
   ```

5. **Start FastAPI server:**
   ```bash
   # Make sure virtual environment is activated
   uvicorn service:app --host 127.0.0.1 --port 8001 --reload
   ```
   AI service will run on `http://127.0.0.1:8001`

---

### 4️⃣ Qdrant Vector Database Setup

**Option A: Docker (Recommended)**

```bash
docker run -p 6333:6333 -p 6334:6334 qdrant/qdrant
```

**Option B: Windows Binary**

1. Download Qdrant from: https://qdrant.tech/documentation/install/
2. Extract and run:
   ```bash
   qdrant.exe
   ```

**Option C: Linux/Mac Binary**

1. Download from: https://github.com/qdrant/qdrant/releases
2. Run:
   ```bash
   ./qdrant
   ```

Qdrant will run on `http://localhost:6333`

**Verify Qdrant is running:**
- Open browser: `http://localhost:6333/dashboard`
- You should see the Qdrant dashboard

---

### 5️⃣ Frontend Setup (Vue 3)

1. **Navigate to frontend directory:**
   ```bash
   cd frontend
   ```

2. **Install Node dependencies:**
   ```bash
   npm install
   ```

3. **Configure API URL (if needed):**
   
   The frontend is already configured to use `http://localhost:8000/api` in `src/services/api.js`.
   If your Laravel backend runs on a different port, update it there.

4. **Start development server:**
   ```bash
   npm run dev
   ```
   Frontend will run on `http://localhost:5173`

---

## 🔄 Initial Setup: Syncing Products to Qdrant

After setting up all services, you need to sync products from MySQL to Qdrant:

1. **Make sure all services are running:**
   - ✅ Laravel backend: `http://localhost:8000`
   - ✅ Python AI service: `http://127.0.0.1:8001`
   - ✅ Qdrant: `http://localhost:6333`
   - ✅ Frontend: `http://localhost:5173`

2. **Sync products to Qdrant:**

   **Option A: Via API (Recommended)**
   ```bash
   # Using curl
   curl -X POST http://127.0.0.1:8001/sync

   # Or using Postman/Thunder Client
   POST http://127.0.0.1:8001/sync
   ```

   **Option B: Via Laravel Admin Panel**
   - Login as admin user
   - Navigate to Admin Dashboard
   - Click "Sync AI Model" button

   **Option C: Direct Python script**
   ```bash
   cd ai
   venv\Scripts\activate  # Windows
   python sync_products.py
   ```

3. **Verify sync:**
   - Check Qdrant dashboard: `http://localhost:6333/dashboard`
   - You should see a collection named "products" with your products

---

## ✅ Testing the System

### 1. **Test Product Recommendations**

```bash
# Get recommendations for a product (ID 1)
GET http://127.0.0.1:8001/recommend/product/1?limit=10

# Or via browser:
http://127.0.0.1:8001/recommend/product/1?limit=10
```

### 2. **Test User Recommendations**

```bash
# Get personalized recommendations for user (ID 1)
GET http://127.0.0.1:8001/recommend/user/1?limit=10

# Via Laravel API (requires authentication):
GET http://localhost:8000/api/recommend/user/1?limit=10
Headers: Authorization: Bearer <token>
```

### 3. **Test Frontend**

1. Open `http://localhost:5173`
2. Register a new user or login
3. Browse products and click on some products (this logs views)
4. "Purchase" some products (this logs purchases)
5. Navigate to "Recommendations" page to see personalized recommendations

---

## 🧰 Useful Commands

| Task | Command | Location |
|------|---------|----------|
| **Run Laravel backend** | `php artisan serve` | `backend/` |
| **Run Vue frontend** | `npm run dev` | `frontend/` |
| **Run AI server** | `uvicorn service:app --port 8001` | `ai/` (with venv activated) |
| **Start Qdrant** | `docker run -p 6333:6333 qdrant/qdrant` | Anywhere |
| **Sync products** | `POST http://127.0.0.1:8001/sync` | API call |
| **Run migrations** | `php artisan migrate` | `backend/` |
| **Seed database** | `php artisan db:seed` | `backend/` |

---

## 🔧 Environment Variables

### Backend (Laravel) - `backend/.env`

```env
APP_NAME="AI Recommender"
APP_ENV=local
APP_KEY=base64:...  # Generated by php artisan key:generate
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ai_recommand
DB_USERNAME=root
DB_PASSWORD=your_password

# AI Service URL
AI_SERVICE_URL=http://127.0.0.1:8001
```

### Python AI Service - `ai/sync_products.py` & `ai/user_profile.py`

Edit the `DB` dictionary directly in the files:

```python
DB = {
    "host": "127.0.0.1",
    "user": "root",
    "password": "your_password",
    "database": "ai_recommand"
}
```

### Frontend - `frontend/src/services/api.js`

The API base URL is hardcoded. To change it, edit:
```javascript
const api = axios.create({
  baseURL: 'http://localhost:8000/api',  // Change this if needed
  // ...
})
```

---

## 📡 API Endpoints

### Laravel Backend (`http://localhost:8000/api`)

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| POST | `/register` | User registration | No |
| POST | `/login` | User login | No |
| GET | `/products` | List products | No |
| GET | `/products/{id}` | Get product details | No |
| POST | `/product/{id}/visit` | Log product view | Yes |
| POST | `/product/{id}/buy` | Log product purchase | Yes |
| GET | `/recommend/user/{id}` | Get user recommendations | Yes |
| POST | `/admin/sync-model` | Sync products to Qdrant | Admin |

### Python AI Service (`http://127.0.0.1:8001`)

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/sync` | Sync products from MySQL to Qdrant |
| GET | `/recommend/product/{id}` | Get product-based recommendations |
| GET | `/recommend/user/{id}` | Get user-based recommendations |

---

## 🎯 How It Works

### Product-Based Recommendations

1. User views a product
2. System embeds the product (name + description + category + tags)
3. Searches Qdrant for similar products using cosine similarity
4. Applies category and tag boosting
5. Returns top K similar products

### User-Based Recommendations (Hybrid CF + Content)

1. System fetches user activity (views, purchases) from MySQL
2. Selects top representative products (prioritizes: purchases → views → category)
3. Builds weighted user profile vector:
   - Purchases weighted 2.0x
   - Views weighted 1.0x
   - Category fallback weighted 0.5x
4. Searches Qdrant with user profile vector
5. Excludes products user already interacted with
6. Applies category and tag boosting
7. Returns personalized recommendations sorted by score

---

## 🐛 Troubleshooting

### Issue: "Connection refused" when calling AI service

**Solution:**
- Make sure Python FastAPI server is running: `uvicorn service:app --port 8001`
- Check `AI_SERVICE_URL` in Laravel `.env` matches the Python service URL

### Issue: "No module named 'embedder'" or import errors

**Solution:**
- Make sure you're in the `ai/` directory
- Activate virtual environment: `venv\Scripts\activate` (Windows) or `source venv/bin/activate` (Mac/Linux)
- Install dependencies: `pip install -r requirements.txt`

### Issue: Qdrant connection error

**Solution:**
- Verify Qdrant is running: `http://localhost:6333/dashboard`
- Check Qdrant URL in `ai/recommend.py` (default: `http://127.0.0.1:6333`)

### Issue: Database connection error

**Solution:**
- Verify MySQL is running
- Check database credentials in Laravel `.env` and Python `sync_products.py`
- Ensure database `ai_recommand` exists
- Run migrations: `php artisan migrate`

### Issue: No recommendations returned

**Solution:**
- Make sure products are synced to Qdrant: `POST http://127.0.0.1:8001/sync`
- For user recommendations, ensure user has activity (views/purchases)
- Check Qdrant dashboard to verify products collection exists

### Issue: Frontend can't connect to backend

**Solution:**
- Verify Laravel backend is running on `http://localhost:8000`
- Check CORS settings in Laravel (should allow `http://localhost:5173`)
- Verify API base URL in `frontend/src/services/api.js`

---

## 📚 Additional Resources

- **Laravel Documentation:** https://laravel.com/docs
- **Vue 3 Documentation:** https://vuejs.org/
- **FastAPI Documentation:** https://fastapi.tiangolo.com/
- **Qdrant Documentation:** https://qdrant.tech/documentation/
- **SentenceTransformer Models:** https://www.sbert.net/docs/pretrained_models.html

---

## 🧑‍💻 Development Workflow

1. **Start all services:**
   ```bash
   # Terminal 1: Laravel
   cd backend && php artisan serve

   # Terminal 2: Python AI
   cd ai && venv\Scripts\activate && uvicorn service:app --port 8001

   # Terminal 3: Qdrant (if using Docker)
   docker run -p 6333:6333 qdrant/qdrant

   # Terminal 4: Frontend
   cd frontend && npm run dev
   ```

2. **After adding new products:**
   - Sync to Qdrant: `POST http://127.0.0.1:8001/sync`

3. **Testing recommendations:**
   - Product-based: `GET http://127.0.0.1:8001/recommend/product/1`
   - User-based: `GET http://127.0.0.1:8001/recommend/user/1`

---

## 🧪 Testing the System

For a complete testing guide with step-by-step instructions, see **[TESTING_GUIDE.md](./TESTING_GUIDE.md)**.

**Quick Test:**
```bash
# Make test script executable (Linux/Mac)
chmod +x test_flow.sh

# Run automated test flow
./test_flow.sh

# Or test manually:
# 1. Register a user via frontend or API
# 2. View and purchase some products
# 3. Check recommendations at /recommendations page
```

---

## 🎉 You're All Set!

Your AI recommendation system is now ready to use. Start browsing products, and the system will learn from user behavior to provide personalized recommendations!

---

## 📝 Notes

- The embedding model (`all-MiniLM-L6-v2`) is downloaded automatically on first use (~90MB)
- Qdrant stores vectors in memory by default. For persistence, configure Qdrant storage path
- User recommendations require at least some user activity (views or purchases)
- The system uses weighted embeddings: purchases are weighted more heavily than views

---

## 🧑‍💻 Contributors

Deep Javiya – Project Lead & Developer

AI/Backend Integration inspired by Amazon-style recommender systems.
