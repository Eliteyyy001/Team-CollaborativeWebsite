const express = require("express");
const app = express();

app.use(express.json());

const productRoutes = require("./routes/productRoutes");

app.use("/api/products", productRoutes);

app.listen(5000, () => console.log("Server running"));
