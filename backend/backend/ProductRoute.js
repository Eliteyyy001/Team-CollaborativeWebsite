const express = require("express");
const router = express.Router();

const authMiddleware = require("../middleware/auth");

// mock DB
let products = [];

// inventory function
const adjustStock = (product, changeAmount) => {
  const newQuantity = product.quantity + changeAmount;

  if (newQuantity < 0) {
    throw new Error("Stock cannot go below zero");
  }

  product.quantity = newQuantity;
};

// ROUTES HERE (POST, GET, PUT, DELETE, PATCH)

module.exports = router;
