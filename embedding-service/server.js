import express from "express";
import { HuggingFaceTransformersEmbeddings } from "langchain/embeddings/hf_transformers";

const app = express();
app.use(express.json());

const embedder = new HuggingFaceTransformersEmbeddings({
  modelName: "BAAI/bge-base-en-v1.5",
});

app.post("/embed", async (req, res) => {
  const [vector] = await embedder.embedDocuments([req.body.text]);
  res.json({ vector });
});

app.listen(8001, () => console.log("Embedding service on port 8001"));
