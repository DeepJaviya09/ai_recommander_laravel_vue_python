# 🧠 AI Product Recommender System

A full-stack, AI-powered recommendation engine built using:

- **Laravel** – Backend API + Admin Panel  
- **Vue 3** – Frontend UI  
- **Python FastAPI** – Vector AI engine  
- **Qdrant** – Local vector database  
- **BGE-base-en-v1.5** – State-of-the-art embedding model  

Provides Amazon-style **semantic product recommendations** using text embeddings.

---

## 📁 Project Structure

ai_recommander/
│
├── backend/ # Laravel API + Admin
├── frontend/ # Vue 3 Frontend
└── ai/ # Python AI Engine
├── service.py # FastAPI server
├── embedder.py # Embeddings (BGE)
├── recommend.py # Recommendation logic
├── sync_products.py # Sync products → Qdrant
├── requirements.txt
└── .env

---

# ⚙️ Installation & Setup

---

## 1️⃣ Backend Setup (Laravel)

```bash
cd backend
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```
Optional: Import Excel product data
```bash
php artisan products:import-excel
```

## 2️⃣ Frontend (Vue)
```bash
cd frontend
cp .env.example .env
npm install
npm run dev
```

## 3️⃣ AI Engine Setup (Python + FastAPI + Qdrant)
# ▶ Create virtual environment
```bash
cd ai
python -m venv venv
venv/Scripts/activate   # Windows
# or: source venv/bin/activate  # Mac/Linux
```

# ▶ Install dependencies
```bash
pip install -r requirements.txt
```

## 4️⃣ Start Qdrant Vector Database
# Option A — Docker (recommended)
```bash
docker run -p 6333:6333 qdrant/qdrant
```


# Option B — Windows Binary
Download from:  
https://qdrant.tech/documentation/install/  
Run:  
```bash
qdrant.exe
```

## 5️⃣ Start the Python FastAPI Server
```bash
cd ai
venv/Scripts/activate
uvicorn service:app --port 8001
```

## 🔄 Syncing Products Into Qdrant
```bash
POST http://127.0.0.1:8001/sync

GET http://127.0.0.1:8001/recommend/product/{id}?limit=10
```

## 🧰 Useful Commands

| Task                | Command                                 |
| ------------------- | --------------------------------------- |
| Run Laravel backend | `php artisan serve`                     |
| Run Vue frontend    | `npm run dev`                           |
| Start AI server     | `uvicorn service:app --port 8001`       |
| Start Qdrant        | `docker run -p 6333:6333 qdrant/qdrant` |
| Sync vectors        | `POST /sync`                            |
| Get recommendations | `GET /recommend/product/{id}`           |


🔁 Workflow

- Users register/login through frontend.

- User activity (browsing, purchasing) gets stored in the backend.

- A cron job or command triggers Python’s train_model.py.

- Python updates the recommendations table in the database.

- The frontend fetches personalized recommendations from /api/recommendations.

🧠 Tech Stack

- Layer	Technology	Description
- Backend	Laravel 12 + Sanctum	Secure REST API
- Frontend	Vue 3 + Tailwind	Modern UI
- AI Engine	Python + scikit-learn + MySQL connector	TF-IDF + content similarity
- DB	MySQL	Persistent data layer

🧰 Commands Reference  
Run Laravel server - php artisan serve  
Run AI training -	python ai/train_model.py  
Import Excel data  -	php artisan products:import-excel    
Queue jobs	- php artisan queue:work  
Schedule cron -	php artisan schedule:run

🧩 Environment Variables  
Backend (.env):
```bash
DB_HOST=127.0.0.1  
DB_DATABASE=ai_recommand  
DB_USERNAME=root  
DB_PASSWORD=  
APP_URL=http://localhost:8000  
```

Frontend (.env):

```bash
VITE_API_URL=http://localhost:8000/api
```


Python (.env):
```bash
DB_HOST=127.0.0.1
DB_NAME=ai_recommand
DB_USER=root
DB_PASS=
```

📅 Cron Job Example
To automate AI retraining:

# Run every day at midnight
0 0 * * * cd /path/to/backend && php artisan ai:retrain  


## 🧑‍💻 Contributors
Deep Javiya – Project Lead & Developer

AI/Backend Integration inspired by Amazon-style recommender systems.


