# embedder.py
import torch
from transformers import AutoTokenizer, AutoModel

MODEL_NAME = "sentence-transformers/all-MiniLM-L6-v2"

print("🔥 Loading MiniLM model (fast + lightweight)...")

tokenizer = AutoTokenizer.from_pretrained(MODEL_NAME)
model = AutoModel.from_pretrained(MODEL_NAME)
model.eval()

print("✅ MiniLM loaded successfully!")


def embed_text(text: str):
    inputs = tokenizer(text, return_tensors="pt", truncation=True, padding=True)

    with torch.no_grad():
        model_output = model(**inputs)

    # Mean Pooling (standard for MiniLM)
    embeddings = model_output.last_hidden_state.mean(dim=1)

    # Normalize the embeddings
    embeddings = torch.nn.functional.normalize(embeddings, p=2, dim=1)

    return embeddings[0].numpy()
