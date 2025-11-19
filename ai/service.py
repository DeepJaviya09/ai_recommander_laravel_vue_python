# project/ai/service.py

from fastapi import FastAPI
from recommend import recommend_for_product, recommend_for_user
from sync_products import sync_products

app = FastAPI()


@app.get("/recommend/product/{product_id}")
def rec_product(product_id: int, limit: int = 10):
    return recommend_for_product(product_id, k=limit)


@app.get("/recommend/user/{user_id}")
def rec_user(user_id: int, limit: int = 10):
    return recommend_for_user(user_id, k=limit)


@app.post("/sync")
def sync():
    sync_products()
    return {"status": "ok"}
