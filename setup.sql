-- ============================================================
-- setup.sql
-- Creates the contact_manager database, the contacts table and
-- some sample data.
-- Run with:  mysql -u root -p < setup.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS contact_manager
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE contact_manager;

DROP TABLE IF EXISTS contacts;

CREATE TABLE contacts (
    id         INT           NOT NULL AUTO_INCREMENT,
    name       VARCHAR(100)  NOT NULL,
    email      VARCHAR(150)  UNIQUE,
    phone      VARCHAR(20),
    group_name VARCHAR(50)   DEFAULT 'General',
    notes      TEXT,
    created_at DATETIME      DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO contacts (name, email, phone, group_name, notes) VALUES
('Nguyen Van An',  'an.nguyen@example.com',  '0901 234 567', 'Family',  'Older brother. Birthday on 12 March.'),
('Tran Thi Binh',  'binh.tran@example.com',  '0912 345 678', 'Friends', 'Classmate from high school.'),
('Le Hoang Cuong', 'cuong.le@example.com',   '0987 654 321', 'Work',    'Project manager at the Hanoi office.'),
('Pham Minh Duc',  'duc.pham@example.com',   '0933 111 222', 'Work',    'Backend developer, handles the API team.'),
('Hoang Thu Ha',   'ha.hoang@example.com',   '0977 888 999', 'Family',  'Cousin living in Da Nang.'),
('Vu Quoc Khanh',  'khanh.vu@example.com',   '0866 123 456', 'Other',   'Landlord. Rent is due on the 5th.'),
('Dang Mai Linh',  'linh.dang@example.com',  NULL,           'General', 'Met at the tech meetup.');
