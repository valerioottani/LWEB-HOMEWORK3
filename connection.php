<?php
require_once __DIR__ . '/dati_generali.php';

/*
 * Connessione MySQL usata ESCLUSIVAMENTE per la tabella utenti.
 * Tutte le altre entità dell'applicazione sono memorizzate nei file XML
 * e vengono lette/modificate con DOM direttamente nelle relative pagine PHP.
 */
$conn = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die('Connessione MySQL fallita: ' . $conn->connect_error);
}
$conn->set_charset('utf8mb4');
?>
<?php require_once __DIR__ . '/dom.php'; ?>
