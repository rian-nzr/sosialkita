-- ============================================================
-- Forum JMK48 — Database
-- Cara pakai (XAMPP / phpMyAdmin):
--   1. Buka phpMyAdmin > Import > pilih file ini > Go
--   2. Atau via terminal: mysql -u root -p < database.sql
-- Akun bawaan:
--   Admin : admin@forumjmk48.test / admin123
--   User  : demo@forumjmk48.test  / demo123
-- ============================================================


-- ---------- Tabel: users ----------
CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  username VARCHAR(50) NOT NULL UNIQUE,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  bio VARCHAR(255) DEFAULT NULL,
  avatar VARCHAR(255) DEFAULT NULL,
  role ENUM('user','admin') NOT NULL DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------- Tabel: posts ----------
CREATE TABLE IF NOT EXISTS posts (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  content TEXT NOT NULL,
  image VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------- Tabel: comments ----------
CREATE TABLE IF NOT EXISTS comments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  post_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  content TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------- Tabel: likes ----------
CREATE TABLE IF NOT EXISTS likes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  post_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_like (post_id, user_id),
  FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------- Data awal (langsung aktif setelah import) ----------
-- Password sudah di-hash dengan password_hash() PHP, jadi
-- import file ini saja sudah cukup — seed.php TIDAK wajib lagi.
--   Admin : admin@forumjmk48.test / admin123
--   User  : demo@forumjmk48.test  / demo123
--   User  : rani@forumjmk48.test  / rani123
INSERT INTO users (id, name, username, email, password, bio, role) VALUES
(1, 'Admin Forum JMK48', 'admin', 'admin@forumjmk48.test',
 '$2y$10$bgj.jtqNDsqmgofFS3eyVumU/YntMh1er3753KPmgTNvtvSZJvRJy',
 'Akun admin — memantau semua postingan.', 'admin'),
(2, 'Demo User', 'demo', 'demo@forumjmk48.test',
 '$2y$10$hNrbfFNuxqUhFm0xATCNOOVGQNa8bBh3BecGTS38o6f5CR0tmFk4W',
 'Halo! Ini akun demo.', 'user'),
(3, 'Rani Putri', 'rani', 'rani@forumjmk48.test',
 '$2y$10$Ejqs7UON7cvxWjMCYJgUW.iYNHwPXBfuJfI0odYOteas3xTWwCwTG',
 'Suka foto & kopi ☕', 'user');

INSERT INTO posts (user_id, content) VALUES
(2, 'Selamat datang di Forum JMK48! Ini postingan pertama. Coba posting teks + foto, like, dan komentar ya.'),
(3, 'Hari ini langitnya bagus banget. Semoga harimu juga cerah!'),
(2, 'Tips hari ini: jangan lupa minum air putih dan istirahat yang cukup.');
