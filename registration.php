<?php

// Config file approach
require_once 'config.php'; // Contiene le credenziali del database

// Function to sanitize input data
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Crea la connesione e controlla se si è connesso senza errori
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ottiene i dati dal form
// Sanitize and validate input data
$name = sanitizeInput($_POST['name'] ?? '');
$surname = sanitizeInput($_POST['surname'] ?? '');
$birthdate = sanitizeInput($_POST['birthdate'] ?? '');
$gender = sanitizeInput($_POST['gender'] ?? '');
$email = sanitizeInput($_POST['email'] ?? '');
$username = sanitizeInput($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$terms = isset($_POST['terms']) ? $_POST['terms'] : '';


// Controlla che le password siano uguali, se lo sono hasha la password
if ($password != $confirm_password) {
    echo "Passwords do not match!";
    $conn->close();
    exit();
}   else    {
    $password = password_hash($password, PASSWORD_DEFAULT);
}

// Controlla se la casella è stata accettata
$terms = isset($_POST['terms']) ? $_POST['terms'] : ""; 
if ($terms != "accepted") {
    echo "You must accept the terms and conditions.";
    $conn->close();
    exit();
}

// SQL query per inserire i dati e creare una nuova tupla
$sql = INSERT INTO personale (name, surname, birth_Date, gender, email, username, password) 
VALUES ('$name', '$surname', 'birthdate', '$gender', '$email', '$username', '$password');

if ($conn->query($sql) === TRUE) {
    echo "Nuovo utente registrato con successo";
} else {
    echo "Errore: " . $sql . "<br>" . $conn->error;
}

$conn->close();

?>