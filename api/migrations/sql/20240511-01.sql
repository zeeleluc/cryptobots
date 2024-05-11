CREATE TABLE wallets (
     id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
     identifier TEXT NOT NULL,
     address VARCHAR(244) NOT NULL,
     balance DECIMAL(10, 4) NOT NULL,
     created_at DATETIME NOT NULL,
     updated_at DATETIME NOT NULL,
     UNIQUE (id)
);
