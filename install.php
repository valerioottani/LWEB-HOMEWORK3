<?php
require_once 'dati_generali.php';
$conn = new mysqli(DB_SERVER, DB_USER, DB_PASS);
if ($conn->connect_error) die("<p style='color:red;'>Connessione al DBMS fallita: ".htmlspecialchars($conn->connect_error)."</p>");
$conn->query("CREATE DATABASE IF NOT EXISTS `".DB_NAME."` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
$conn->select_db(DB_NAME);

// Nell'Homework XML l'unica tabella relazionale mantenuta è UTENTI.
$conn->query("DROP TABLE IF EXISTS `".TAB_USERS."`");
$conn->query("CREATE TABLE `".TAB_USERS."` (
 id INT AUTO_INCREMENT PRIMARY KEY,
 username VARCHAR(50) NOT NULL UNIQUE,
 password VARCHAR(255) NOT NULL,
 ruolo VARCHAR(20) NOT NULL DEFAULT 'user',
 nome_completo VARCHAR(255) DEFAULT '', eta INT DEFAULT NULL,
 data_nascita DATE DEFAULT NULL, indirizzo VARCHAR(255) DEFAULT '',
 numero_carta VARCHAR(25) DEFAULT '', scadenza_carta VARCHAR(10) DEFAULT '',
 cvv VARCHAR(5) DEFAULT '', saldo_buoni DECIMAL(10,2) DEFAULT 0.00
)");
$pass_admin=password_hash('adminpassword',PASSWORD_DEFAULT);
$pass_user=password_hash('utentepassword',PASSWORD_DEFAULT);
$pass_other=password_hash('password123',PASSWORD_DEFAULT);
$stmt=$conn->prepare("INSERT INTO `".TAB_USERS."` (username,password,ruolo,nome_completo,eta,data_nascita,indirizzo,numero_carta,scadenza_carta,cvv,saldo_buoni) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
$users=[
 ['admin',$pass_admin,'admin','Valerio Ottani',24,'2002-04-18','Via Tiburtina 212, Roma','4532 1890 5674 1234','12/28','123',0.00],
 ['utente',$pass_user,'user','Martina Ferri',23,'2003-09-14','Via Monte Napoleone 8, Milano','5412 7500 1234 8901','09/27','456',0.00],
 ['federico_esposito',$pass_other,'user','Federico Esposito',26,'2000-11-03','Corso Umberto I 45, Napoli','','','',0.00],
 ['alessia_monti',$pass_other,'user','Alessia Monti',22,'2004-02-27','Via Mazzini 15, Bologna','','','',0.00],
 ['lorenzo_santoro',$pass_other,'user','Lorenzo Santoro',27,'1999-06-19','Via Etnea 102, Catania','','','',0.00]
];
foreach($users as $u){$stmt->bind_param('ssssisssssd',$u[0],$u[1],$u[2],$u[3],$u[4],$u[5],$u[6],$u[7],$u[8],$u[9],$u[10]);$stmt->execute();}
$stmt->close();$conn->close();
$xmlOk=is_dir(__DIR__.'/xml') && count(glob(__DIR__.'/xml/*.xml'))===12;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><title>Installazione</title></head>
<body style="background:#121212;color:#fff;font-family:Arial;padding:40px;text-align:center"><div style="max-width:700px;margin:auto;background:#181818;padding:32px;border-radius:8px">
<h1 style="color:#1db954">Installazione completata</h1><p>MySQL contiene esclusivamente la tabella <strong>utenti</strong>.</p><p>Le altre 12 entità sono persistite nei documenti XML della cartella <code>xml/</code>, validabili tramite DTD/XSD.</p><p>Documenti XML presenti: <?php echo $xmlOk?'12/12':'VERIFICARE CARTELLA XML'; ?></p><p><a href="login.php" style="color:#1db954">Vai al login</a></p></div></body></html>
