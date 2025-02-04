<?php
// Database.php
class Database {
    private $connection;
    private $dsn = 'mysql:host=localhost;dbname=user information;port=3307'; // Update port and dbname
    private $username = 'root';
    private $password = '';
    private $options = [
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ];

    public function __construct() {
        try {
            $this->connection = new PDO($this->dsn, $this->username, $this->password, $this->options);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public function query($sql, $params = []) {
        try {
            $statement = $this->connection->prepare($sql);
            $statement->execute($params);
            return $statement;
        } catch (PDOException $e) {
            // Log the error details
            error_log("Database Error: " . $e->getMessage());
            throw $e;
        }
    }

    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }
}