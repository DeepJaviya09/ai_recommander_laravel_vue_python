# AI Recommender Frontend

Modern Vue 3 frontend for the AI Recommendation System with Laravel + Sanctum backend.

## 🚀 Quick Start

### Prerequisites
- Node.js 18+ and npm
- Laravel backend running on `http://localhost:8000`

### Installation

1. Install dependencies:
```bash
cd frontend
npm install
```

2. Start the development server:
```bash
npm run dev
```

The app will be available at `http://localhost:5173`

### Build for Production

```bash
npm run build
```

The built files will be in the `dist` folder.

## 📁 Project Structure

```
frontend/
├── src/
│   ├── assets/          # CSS and static assets
│   ├── components/      # Reusable Vue components
│   │   ├── ui/          # Base UI components (Card, Button, Input)
│   │   ├── ProductCard.vue
│   │   └── Navbar.vue
│   ├── pages/           # Page components
│   │   ├── auth/        # Login, Register
│   │   ├── user/        # Products, ProductDetails, Recommendations
│   │   └── admin/       # Dashboard, SyncModel
│   ├── router/          # Vue Router configuration
│   ├── services/        # API service (Axios)
│   ├── store/           # Pinia stores
│   ├── App.vue
│   └── main.js
├── index.html
└── package.json
```

## 🎨 Features

- ✅ Modern gradient UI with glassmorphism effects
- ✅ Full authentication with Laravel Sanctum
- ✅ Product browsing with search and category filters
- ✅ Personalized recommendations
- ✅ Admin panel for model synchronization
- ✅ Responsive design
- ✅ Toast notifications
- ✅ Route guards for authentication and admin access

## 🔌 API Integration

The frontend connects to the Laravel backend at `http://localhost:8000/api`.

### Authentication Endpoints
- `POST /api/register` - User registration
- `POST /api/login` - User login
- `POST /api/logout` - User logout
- `GET /api/user` - Get current user

### Product Endpoints
- `GET /api/products` - List products (with pagination, search, category filter)
- `GET /api/products/{id}` - Get product details
- `POST /api/product/{id}/visit` - Log product view
- `POST /api/product/{id}/buy` - Log product purchase

### Recommendation Endpoints
- `GET /api/recommendations` - Get personalized recommendations

### Admin Endpoints
- `POST /api/admin/sync-model` - Sync AI model
- `GET /api/admin/products` - List products (admin)
- `POST /api/admin/products` - Create product (admin)
- `PUT /api/admin/products/{id}` - Update product (admin)
- `DELETE /api/admin/products/{id}` - Delete product (admin)

## 🎯 User Roles

- **User**: Can browse products, view details, purchase, and see recommendations
- **Admin**: Has all user permissions plus access to admin dashboard and model sync

## 🛠️ Tech Stack

- **Vue 3** - Progressive JavaScript framework
- **Vite** - Next generation frontend tooling
- **Vue Router** - Official router for Vue.js
- **Pinia** - State management
- **Axios** - HTTP client
- **TailwindCSS** - Utility-first CSS framework
- **Vue Toastification** - Toast notifications
- **Heroicons** - Beautiful SVG icons

## 📝 Notes

- Make sure the Laravel backend CORS is configured to allow requests from `http://localhost:5173`
- The authentication token is stored in localStorage
- All authenticated requests automatically include the Bearer token
- 401 responses automatically log out the user and redirect to login



