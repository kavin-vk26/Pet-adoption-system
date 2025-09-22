-- Run this in pet_adoption DB
-- Users table
CREATE TABLE IF NOT EXISTS users (
  id SERIAL PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  email VARCHAR(200) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  is_admin BOOLEAN DEFAULT FALSE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Pets table
CREATE TABLE IF NOT EXISTS pets (
  id SERIAL PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  species VARCHAR(100),
  age INTEGER,
  description TEXT,
  image VARCHAR(255),
  is_adopted BOOLEAN DEFAULT FALSE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Adoptions table
CREATE TABLE IF NOT EXISTS adoptions (
  id SERIAL PRIMARY KEY,
  user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  pet_id INTEGER NOT NULL REFERENCES pets(id) ON DELETE CASCADE,
  adopted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE(user_id, pet_id)
);

-- create a default admin user (password: admin123) -- CHANGE in prod
INSERT INTO users (name, email, password, is_admin)
VALUES ('Admin', 'admin@example.com', crypt('admin123', gen_salt('bf')), true)
ON CONFLICT DO NOTHING;

-- Sample pets
INSERT INTO pets (name, species, age, description, image)
VALUES
('Buddy','Dog',3,'Friendly golden retriever. Good with kids.',''),
('Mittens','Cat',2,'Calm indoor cat. Loves naps.','')
ON CONFLICT DO NOTHING;


-- & "D:\xampp\php\php.exe" -S localhost:8000 -t public
