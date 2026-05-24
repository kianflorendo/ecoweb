-- ============================================================
--  BottleBack Database Setup (Updated with users table)
-- ============================================================

CREATE DATABASE IF NOT EXISTS bottleback
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bottleback;

CREATE TABLE IF NOT EXISTS transactions (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  bottle_count   INT          NOT NULL DEFAULT 1,
  reward_amount  INT          NOT NULL DEFAULT 1,
  status         VARCHAR(20)  NOT NULL DEFAULT 'Accepted',
  node_id        VARCHAR(50)  NOT NULL DEFAULT 'node_001',
  created_at     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS machine_status (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  node_id     VARCHAR(50)  NOT NULL DEFAULT 'node_001',
  bin_level   INT          NOT NULL DEFAULT 0,
  is_online   TINYINT(1)   NOT NULL DEFAULT 0,
  updated_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS contact_messages (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  name       VARCHAR(120) NOT NULL,
  email      VARCHAR(200) NOT NULL,
  subject    VARCHAR(100) NOT NULL,
  message    TEXT         NOT NULL,
  created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Client user accounts
CREATE TABLE IF NOT EXISTS users (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  first_name      VARCHAR(80)  NOT NULL,
  last_name       VARCHAR(80)  NOT NULL,
  email           VARCHAR(200) NOT NULL UNIQUE,
  password_hash   VARCHAR(255) NOT NULL,
  barangay        VARCHAR(120) NOT NULL DEFAULT 'Muzon',
  total_bottles   INT          NOT NULL DEFAULT 0,
  total_rewards   INT          NOT NULL DEFAULT 0,
  is_active       TINYINT(1)   NOT NULL DEFAULT 1,
  created_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  last_login      DATETIME     NULL
) ENGINE=InnoDB;

INSERT INTO machine_status (node_id, bin_level, is_online)
VALUES ('node_001', 0, 0);
