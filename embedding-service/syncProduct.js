import axios from "axios";
import { QdrantClient } from "@qdrant/js-client-rest";
import { TransformersEmbeddings } from "@langchain/community/embeddings/xenova";

const qdrant = new QdrantClient({
  url: "http://localhost:6333",
  checkCompatibility: false
});

const embedder = new TransformersEmbeddings({
  model: "Xenova/bge-base-en-v1.5",
  pooling: "mean",
  normalize: true,
});

async function syncProducts() {
  const { data: products } = await axios.get("http://localhost:8000/api/all-products");

  let points = [];

  for (const p of products) {
    const text = `
      Name: ${p.name}
      Description: ${p.description}
      Tags: ${p.tags?.join(", ")}
      Category: ${p.category?.name}
    `.trim();

    const vector = await embedder.embedQuery(text);

    points.push({
      id: p.id,
      vector,
      payload: {
        id: p.id,
        name: p.name,
        description: p.description,
        tags: p.tags,
        price: p.price,
        category_id: p.category_id,
        category_name: p.category?.name,
        image_url: p.image_url,
      },
    });
  }

  await qdrant.upsert("products", { points });

  console.log("Synced", points.length, "products");
}

syncProducts().catch(console.error);
