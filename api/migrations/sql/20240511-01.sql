CREATE TABLE wallets (
     id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
     identifier VARCHAR(255) UNIQUE NOT NULL,
     address VARCHAR(244) NOT NULL,
     balance DECIMAL(10, 4),
     created_at DATETIME NOT NULL,
     updated_at DATETIME NOT NULL
);
