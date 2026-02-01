const bcrypt = require("bcryptjs");
const users = require("./signup").users;

function login(req, res) {
  const { username, password } = req.body;

  const user = users.find(u => u.username === username);
  if (!user) return res.status(401).json({ message: "Invalid credentials" });

  const match = bcrypt.compareSync(password, user.password);
  if (!match) return res.status(401).json({ message: "Invalid credentials" });

  res.json({ message: "Login successful", role: user.role });
}

module.exports = login;
