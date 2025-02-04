<?php
// config.php
require_once 'Database.php';

session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

function getDB() {
    static $db = null;
    if ($db === null) {
        $db = new Database();
    }
    return $db;
}