<?php
require_once 'config.php';

try {
    $connection = new PDO($dsn, $username, $password, $options);
} catch (PDOException $error) {
    echo "Database Connection Failed: " . $error->getMessage();
    exit;
}
?>
