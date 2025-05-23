<?php

// Config file approach
require_once 'config.php'; // Contiene le credenziali del database

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'errors' => []
];

// Function to sanitize input data
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Create database connection using PDO for better security
        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        $pdo = new PDO($dsn, $user, $pass, $options);


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
    
} catch (PDOException $e) {
        // gestione degli errori e il logging, log the error messagein a real application(?)
        error_log("Database error: " . $e->application);
        $response['message'] = 'Registration failed due to a database error.';

        //for debugging only (rimuovi dopo che hai finito di testare)
        if (defined('DEBUG') && DEBUG) {
            $response['debug'] = $e->getMessage();
        }
    }
    /*
if ($conn->query($sql) === TRUE) {
    echo "Nuovo utente registrato con successo";
} else {
    echo "Errore: " . $sql . "<br>" . $conn->error;
}*/

$conn->close();

?>