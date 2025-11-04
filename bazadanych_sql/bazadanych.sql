CREATE DATABASE IF NOT EXISTS czatonotatnik CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE czatonotatnik;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('teacher','student') NOT NULL DEFAULT 'student',
  last_online TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  text TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE board (
  id INT PRIMARY KEY DEFAULT 1, -- tylko jeden wiersz
  content TEXT,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE notes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  content TEXT,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Seed: przykładowi użytkownicy (hasła do zahashowania w PHP poniżej)
INSERT INTO users (name, username, password_hash, role) VALUES
('Nauczyciel', 'teacher1', '$2y$10$examplehashreplace', 'teacher'),
('Uczeń A', 'student1', '$2y$10$examplehashreplace', 'student');
-- W praktyce ustawiaj password_hash przez password_hash() w PHP
