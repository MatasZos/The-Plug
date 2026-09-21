<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['saved_full_name'] = $_POST['full_name'] ?? '';
    $_SESSION['saved_address'] = $_POST['address'] ?? '';
    $_SESSION['saved_contact'] = $_POST['contact'] ?? '';
}
