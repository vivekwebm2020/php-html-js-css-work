### How to use this ?
1. Copy `db.config.sample.php` into `db.config.php` and update the database connection details.
2. Open `http://localhost/ModalPopupLogin` in your browser (Chrome / FF preferred).
3. Make sure your database connection details are correct and you've created the `users` table with below query:
```sql
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_login DATETIME DEFAULT NULL,
    status ENUM('active', 'inactive', 'banned') NOT NULL DEFAULT 'active',
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    profile_picture VARCHAR(255) DEFAULT NULL,
    bio TEXT DEFAULT NULL,
    INDEX (username),
    INDEX (email),
    INDEX (status),
    INDEX (created_at)
);
```

> Reference Links:
> https://chatgpt.com/share/6720058f-4c64-800b-87b1-f13f33f33d01
