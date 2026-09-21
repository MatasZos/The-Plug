<?php
require_once "../src/config.php";

try {
    $pdo = new PDO($dsn, $username, $password, $options);

    $sql = file_get_contents("../data/init.sql");
    $pdo->exec($sql);

    echo "<p>✅ Database and tables created successfully!</p>";

} catch (PDOException $e) {
    echo "<p>❌ Error creating database: " . $e->getMessage() . "</p>";
}
