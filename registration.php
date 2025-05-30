<?php

// Config file approach, tipo di approccio dove le credenziali del database vengono contenute in un file apparte
require_once 'config.php'; // Contiene le credenziali del database

// inizializza l'array associativo di risposta per le 3 opzioni qui messe. Successo, messaggio e errore.
$response = [
    'success' => false,
    'message' => '',
    'errors' => []
];

// funzione per "sterilizzare" (sanitize) i dati
function sanitizeInput($data) {
    $data = trim($data);                //rimuove spazi bianchi a inizio e fine stringa
    $data = stripslashes($data);        //rimuove backlash della stringa
    $data = htmlspecialchars($data);    //converte caratteri speciali in entità HTML per prevenire XSS
    return $data;
}

try {
        //crea la connesione al database dall'interfaccia PDO con i dati in "config.php" e le opzioni appena dichiarate
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";   // DNS
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,        //configura PDO per lanciare eccezioni, permette di usare try/catch in modo pulito
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,   //Imposta il modo predefinito in cui PDO restituisce i risultati delle query
        PDO::ATTR_EMULATE_PREPARES => false,                //Disabilita l'emulazione dei prepared statement forzandolo a usare veri prepared statements per una maggior esicurezza contro SQL injection
    ];
        
    $pdo = new PDO($dsn, $user, $pass, $options);
    /*Spiegare PDO: "PHP Data Objects"
    Interfaccia PHP che fornisce un modo per accedere a diversi tipi di database incluso MySQL
    */

    // Ottiene i dati dal form, "disinfettando" (sanitize) e validando i dati in input
    $name = sanitizeInput($_POST['name'] ?? '');
    $surname = sanitizeInput($_POST['surname'] ?? '');
    $birthdate = sanitizeInput($_POST['birthdate'] ?? '');
    $gender = sanitizeInput($_POST['gender'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $terms = isset($_POST['terms']) ? $_POST['terms'] : '';

    // Validazione (arrya per raccogliere tutti gli errori)
    $errors = [];

    // campi obbligatori
    if (empty($name)) $errors[] = "Name is required.";
    if (empty($surname)) $errors[] = "Surname is required.";
    if (empty($birthdate)) $errors[] = "Birth date is required.";
    if (empty($gender)) $errors[] = "Gender is required.";
    if (empty($email)) $errors[] = "Email is required.";
    if (empty($username)) $errors[] = "Username is required.";
    if (empty($password)) $errors[] = "Password is required.";

    // Validazione email
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // Validazione password
    if (!empty($password)) {
        if (strlen($password) < 8) {
            $errors[] = "Password must be at least 8 characters long.";
        }
        
        if ($password !== $confirm_password) {
            $errors[] = "Passwords do not match.";
        }
    }

    // Validazione termini
    if ($terms !== "accepted") {
        $errors[] = "You must accept the terms and conditions.";
    }

    // Controlla se l'email non eiste già
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM personale WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() > 0) {
        $errors[] = "Email already exists.";
    }

    // Controlla se l'username non esiste già
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM personale WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetchColumn() > 0) {
        $errors[] = "Username already exists.";
    }

    // ritorna gli errori raccolti dall'array $errors
    if (!empty($errors)) {
        $response['errors'] = $errors;
        $response['message'] = 'Please fix the following errors:';
        echo json_encode($response);
        exit;
    }

    // Hash password con la funzione di PHP
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // prepara la SQL statement, più sicura di direttamente eseguirla
    $stmt = $pdo->prepare("INSERT INTO personale (name, surname, birth_Date, gender, email, username, password) 
    VALUES (?, ?, ?, ?, ?, ?, ?)");

    // esegue la query con i dati immessi
    $stmt->execute([
    $name,
    $surname,
    $birthdate,
    $gender,
    $email,
    $username,
    $hashed_password
    ]);

    // ritorno del messaggio di successo
    $response['success'] = true;
    $response['message'] = 'User registered successfully!';
   
} catch (PDOException $e) {
    // gestione degli errori e il logging, log the error messagein a real application(?)
    error_log("Database error: " . $e->application);
    $response['message'] = 'Registration failed due to a database error.';

    //for debugging only (rimuovi dopo che hai finito di testare)
    if (defined('DEBUG') && DEBUG) {
        $response['debug'] = $e->getMessage();
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);

?>