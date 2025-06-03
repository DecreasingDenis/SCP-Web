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

// Rate limiting semplice basato su sessione
session_start();
$max_attempts = 5;
$time_window = 300; // 5 minuti

// Inizializza l'array dei tentativi di login se non esiste
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = [];
}

// Pulisce i tentativi vecchi quando l'utente ha fatto un tentativo di login
$_SESSION['login_attempts'] = array_filter($_SESSION['login_attempts'], function($timestamp) use ($time_window) {
    return (time() - $timestamp) < $time_window;
});

// Controlla se ci sono troppi tentativi
if (count($_SESSION['login_attempts']) >= $max_attempts) {
    $response['message'] = 'Too many login attempts. Please try again later.';
    $response['error_type'] = 'rate_limit';
    header('Content-Type: application/json');
    http_response_code(429);
    echo json_encode($response);
    exit;
}

try {
    //crea la connesione al database dall'interfaccia PDO con i dati in "config.php" e le opzioni appena dichiarate
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";   // DNS
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,             //configura PDO per lanciare eccezioni, permette di usare try/catch in modo pulito
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,   //Imposta il modo predefinito in cui PDO restituisce i risultati delle query
        PDO::ATTR_EMULATE_PREPARES => false,                                    //Disabilita l'emulazione dei prepared statement forzandolo a usare veri prepared statements per una maggior esicurezza contro SQL injection
    ];
        
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Ottiene i dati dal form, "disinfettando" (sanitize) e validando i dati in input
    $usernameEmail = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';       // Non sanificare la password, poiché deve essere confrontata con l'hash nel database

    // Validazione (array per raccogliere tutti gli errori)
    $errors = [];

    // campi obbligatori, anche se c'è già un controllo in frontend
    if (empty($usernameEmail)) $errors[] = "Username or email is required.";
    if (empty($password)) $errors[] = "Password is required.";

    // ritorna gli errori raccolti dall'array $errors
    if (!empty($errors)) {
        $response['errors'] = $errors;
        $response['message'] = 'Please fix the following errors:';
        $response['field_errors'] = [
            'usernameEmail' => empty($usernameEmail) ? 'Username or email is required' : '',
            'password' => empty($password) ? 'Password is required' : ''
        ];
        header('Content-Type: application/json');   // Imposta il tipo di contenuto della risposta come JSON (così il browser sa come interpretare la risposta)
        echo json_encode($response);
        exit;
    }

    // Cerca l'utente nel database per username o email
    $stmt = $pdo->prepare("SELECT user_ID, username, email, password, clearance_Level FROM personale WHERE username = ? OR email = ? LIMIT 1");  // Usa LIMIT 1 per evitare di prendere più di un record
    $stmt->execute([$usernameEmail, $usernameEmail]);
    $user = $stmt->fetch();     // Restituisce il risultato

    // Controlla se l'utente esiste
    if (!$user) {
        // Aggiunge tentativo fallito
        $_SESSION['login_attempts'][] = time();     // Registra il tentativo fallito (e il tempo corrente)
        
        $response['message'] = 'Invalid credentials.';
        $response['error_type'] = 'user_not_found'; // Specifica il tipo di errore per il frontend
        $response['field_errors'] = [
            'usernameEmail' => 'Username or email not found'
        ];
        header('Content-Type: application/json');
        http_response_code(401);    // Imposta il codice di stato HTTP 401 Unauthorized, che indica che le credenziali fornite non sono valide
        echo json_encode($response);
        exit;
    }

    // Verifica la password usando password_verify
    if (!password_verify($password, $user['password'])) {   // Confronta la password inserita con l'hash della password nel database
        // Aggiunge tentativo fallito
        $_SESSION['login_attempts'][] = time();   // Registra il tentativo fallito (e il tempo corrente)
        
        $response['message'] = 'Invalid credentials.';
        $response['error_type'] = 'wrong_password'; // Specifica il tipo di errore per il frontend
        $response['field_errors'] = [
            'password' => 'Incorrect password'
        ];
        header('Content-Type: application/json');
        http_response_code(401);    // Imposta il codice di stato HTTP 401 Unauthorized, che indica che le credenziali fornite non sono valide
        echo json_encode($response);
        exit;
    }

    // Login di successo

    // Avvia sessione utente
    $_SESSION['user_id'] = $user['user_ID'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['clearance_Level'] = $user['clearance_Level']; // <-- AGGIUNGI QUESTA RIGA
    $_SESSION['logged_in'] = true;
    $_SESSION['login_time'] = time();

    // Pulisce i tentativi falliti
    $_SESSION['login_attempts'] = [];

    // ritorno del messaggio di successo
    $response['success'] = true;
    $response['message'] = 'Login successful! Welcome back.';
    $response['user'] = [
        'user_ID' => $user['user_ID'],
        'username' => $user['username'],
        'email' => $user['email']
    ];
   
} catch (PDOException $e) {
    // gestione degli errori e il logging
    error_log("Database error: " . $e->getMessage());
    $response['message'] = 'Login failed due to a database error.';

    /*for debugging only (rimuovi dopo che hai finito di testare)
    if (defined('DEBUG') && DEBUG) {
        $response['debug'] = $e->getMessage();
    }*/
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);

?>
