import numpy as np
from transformers import AutoTokenizer, AutoModel
import torch

MODEL_NAME = "sentence-transformers/all-MiniLM-L6-v2"

tokenizer = AutoTokenizer.from_pretrained(MODEL_NAME)
model = AutoModel.from_pretrained(MODEL_NAME)

if torch.cuda.is_available():
    model.to("cuda")

def mean_pooling(model_output, attention_mask):
    token_embeddings = model_output[0]
    input_mask_expanded = attention_mask.unsqueeze(-1).expand(token_embeddings.size()).float()
    return torch.sum(token_embeddings * input_mask_expanded, 1) / torch.clamp(input_mask_expanded.sum(1), min=1e-9)

def embed_text(text):
    encoded = tokenizer(text, return_tensors="pt", truncation=True, padding=True)
    if torch.cuda.is_available():
        encoded = {k: v.to("cuda") for k, v in encoded.items()}
    with torch.no_grad():
        model_output = model(**encoded)
    pooled = mean_pooling(model_output, encoded["attention_mask"])
    vec = pooled[0].cpu().numpy()
    vec = vec / (np.linalg.norm(vec) + 1e-12)
    return vec.tolist()
