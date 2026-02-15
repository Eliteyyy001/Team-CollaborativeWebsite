const jwt = require("jsonwebtoken");
const express = require("express");
const app = express();

app.use(express.json());

// ========================
// Mock database
// ========================
let products = [];

// ========================
// Auth Middleware
// ========================
const authMiddleware = (req, res, next) => {
  const authHeader = req.headers.authorization;

  if (!authHeader || !authHeader.startsWith("Bearer ")) {
    return res.status(401).json({
      success: false,
      message: "Access denied. No token provided."
    });
  }

  const token = authHeader.split(" ")[1];

  try {
    const decoded = jwt.verify(token, process.env.JWT_SECRET || "secretkey");
    req.user = decoded;
    next();
  } catch (error) {
    return res.status(401).json({
      success: false,
      message: "Invalid or expired token."
    });
  }
};

// ========================
// Product Logic
// ========================

// Add new product
app.post("/api/products", authMiddleware, (req, res) => {
  const { name, category, price, quantity } = req.body;

  if (!name || !category || price == null || quantity == null) {
    return res.status(400).json({ success: false, message: "All fields are required" });
  }

  if (price < 0 || quantity < 0) {
    return res.status(400).json({ success: false, message: "Price and quantity cannot be negative" });
  }

  const newProduct = {
    id: products.length + 1,
    name,
    category,
    price,
    quantity,
    active: true
  };

  products.push(newProduct);
  res.status(201).json({ success: true, product: newProduct });
});

// View all active products
app.get("/api/products", authMiddleware, (req, res) => {
  res.json(products.filter(p => p.active));
});

// Edit product and update stock
app.put("/api/products/:id", authMiddleware, (req, res) => {
  const { id } = req.params;
  const { name, category, price, quantity } = req.body;

  const product = products.find(p => p.id == id && p.active);
  if (!product) return res.status(404).json({ success: false, message: "Product not found" });

  if ((price != null && price < 0) || (quantity != null && quantity < 0)) {
    return res.status(400).json({ success: false, message: "Price and quantity cannot be negative" });
  }

  product.name = name ?? product.name;
  product.category = category ?? product.category;
  product.price = price ?? product.price;
  product.quantity = quantity ?? product.quantity;

  res.json({ success: true, product });
});

// Delete product (mark inactive)
app.delete("/api/products/:id", authMiddleware, (req, res) => {
  const { id } = req.params;
  const product = products.find(p => p.id == id && p.active);
  if (!product) return res.status(404).json({ success: false, message: "Product not found" });

  product.active = false;
  res.json({ success: true, message: "Product marked inactive" });
});

// ========================
// Start server
// ========================
const PORT = process.env.PORT || 5000;
app.listen(PORT, () => console.log(`Server running on port ${PORT}`));
