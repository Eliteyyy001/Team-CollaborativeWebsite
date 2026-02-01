const bcrypt = require("bcryptjs");
const users = [];

function signup(req, res) {
  const { username, password, role } = req.body;

  const hashedPassword = bcrypt.hashSync(password, 10);

  users.push({
    username,
    password: hashedPassword,
    role: role || "employee"
  });

  res.status(201).json({ message: "User created" });
}

module.exports = signup;
