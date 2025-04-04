<?php

//dettagli per la connesione al database
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "scpDatabase";

// Crea la connesione e controlla se si è connesso senza errori
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ottiene i dati dal form
$name =             $_POST['name'];
$surname =          $_POST['surname'];
$birthdate =        $_POST['birthdate'];
$gender =           $_POST['gender'];
$email =            $_POST['email'];
$username =         $_POST['username'];
$password =         $_POST['password'];
$confirm_password = $_POST['confirm_password'];

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