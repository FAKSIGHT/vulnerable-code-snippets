/**
 * YesWeHack - Vulnerable code snippets with SQLite integration (POST form and CORS)
 */
const express = require('express');
const sqlite3 = require('sqlite3').verbose();
const cookieParser = require('cookie-parser');
const bodyParser = require('body-parser');
const app = express();

app.use(bodyParser.urlencoded({ extended: true })); // For parsing form data
app.use(cookieParser());

// Create a SQLite database and users table
const db = new sqlite3.Database(':memory:');

db.serialize(() => {
    db.run("CREATE TABLE users (id INTEGER PRIMARY KEY, username TEXT, password TEXT)");
    db.run("INSERT INTO users (username, password) VALUES ('James007', 'password123')");
});

app.use((req, res, next) => {
    res.header("Access-Control-Allow-Origin", "*");
    res.header("Access-Control-Allow-Credentials", "true");
    res.header("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, OPTIONS");
    res.header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");

    if (req.method === "OPTIONS") {
        return res.status(200).end();
    }

    next();
});

// Serve links to the forms
app.get('/', function (req, res) {
    res.send(`
        <h1>Welcome</h1>
        <p><a href="/login-form">Login Form</a></p>
        <p><a href="/change-password-form">Change Password Form</a></p>
    `);
});

// Serve a simple login form
app.get('/login-form', function (req, res) {
    res.send(`
        <form action="/login" method="POST">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>
            <button type="submit">Login</button>
        </form>
    `);
});

// Endpoint to authenticate user and set a cookie if credentials are correct (POST)
app.post('/login', function (req, res) {
    const { username, password } = req.body;

    GetUserCredentials(username, (user) => {
        if (user && user.password === password) {
            // Set a session cookie if login is successful
            res.cookie('session', user.id, { httpOnly: true });
            res.send("Login successful");
        } else {
            res.status(401).send("Invalid credentials");
        }
    });
});

// Serve a simple password change form
app.get('/change-password-form', function (req, res) {
    res.send(`
        <form action="/change-password" method="POST">
            <label for="newPassword">New Password:</label>
            <input type="password" name="newPassword" id="newPassword" required>
            <button type="submit">Change Password</button>
        </form>
    `);
});

// Endpoint to change password (requires a session cookie, POST with CORS headers)
app.post('/change-password', function (req, res) {
    const { newPassword } = req.body;
    const userId = req.cookies.session;

    if (!userId) {
        return res.status(401).send("Not authenticated");
    }

    // Validate password length
    if (newPassword.length < 6) {
        return res.status(400).send("Password too short");
    }

    // Update the password in the database
    db.run("UPDATE users SET password = ? WHERE id = ?", [newPassword, userId], function (err) {
        if (err) {
            res.status(500).json({ error: "Database error" });
        } else {
            res.json({ message: "Password changed successfully" });
        }
    });
});

// Function to get user credentials from the database
function GetUserCredentials(username, callback) {
    db.get("SELECT * FROM users WHERE username = ?", [username], (err, row) => {
        if (err) {
            callback(null);
        } else {
            callback(row);
        }
    });
}

// Start web app:
const PORT = 1337;
app.listen(PORT, '0.0.0.0', () => {
    console.log(`Server is running on http://0.0.0.0:${PORT}`);
});
