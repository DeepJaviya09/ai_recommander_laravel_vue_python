# 🧠 AI Product Recommender System

A full-stack AI-powered product recommendation platform built with:
- **Laravel (Backend + Sanctum Auth)**
- **Vue.js (Frontend)**
- **Python (AI Recommendation Engine)**

---

## 🚀 Project Structure

ai_recommander/
│
├── backend/ → Laravel API + Cron + Sanctum Auth
├── frontend/ → Vue.js 3 + Tailwind UI
└── ai/ → Python ML engine (TF-IDF + Collaborative filtering)

yaml
Copy code

---

## ⚙️ Setup Instructions

## 1️⃣ Backend (Laravel)
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

2️⃣ Frontend (Vue)
```bash
cd frontend
cp .env.example .env
npm install
npm run dev
```

3️⃣ AI (Python)
```bash
cd ai
python -m venv venv
venv/Scripts/activate  # or source venv/bin/activate
pip install -r requirements.txt
python train_model.py
```
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


