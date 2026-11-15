<?php
/**
 * config.php
 * Database connection (PDO) and session start.
 * Every page includes this file first, so $pdo is shared everywhere.
 */

const DB_HOST = 'localhost';
const DB_NAME = 'contact_manager';
const DB_USER = 'root';
const DB_PASS = '';

// Flash messages are stored in the session.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // real prepared statements
} catch (PDOException $e) {
    die('Database connection failed: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}

require_once __DIR__ . '/helpers.php';
