<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'connection.php';
if (!isset($_SESSION['user']) || ($_SESSION['ruolo']??'') !== 'admin') { header('Location: homepage.php'); exit(); }
$messaggio='';$errore='';
if($_SERVER['REQUEST_METHOD']==='POST'&&($_POST['azione']??'')==='aggiungi'){
 $nome=trim($_POST['nome']??'');$artisti=trim($_POST['artisti']??'');$immagine=trim($_POST['immagine']??'');$sfondo_css=trim($_POST['sfondo_css']??'linear-gradient(135deg,#1e3264,#000)');
 if($nome!==''){dom_insert('stazioni_radio',['nome'=>$nome,'artisti'=>$artisti,'immagine'=>$immagine,'sfondo_css'=>$sfondo_css]);$messaggio='Stazione aggiunta con successo!';}else $errore='Il nome è obbligatorio.';
}
if(isset($_GET['elimina'])){dom_delete('stazioni_radio',fn($r)=>(int)$r['id']===(int)$_GET['elimina']);$messaggio='Stazione eliminata con successo!';}
if($_SERVER['REQUEST_METHOD']==='POST'&&($_POST['azione']??'')==='modifica'){
 $id=(int)($_POST['stazione_id']??0);$nome=trim($_POST['nome']??'');$artisti=trim($_POST['artisti']??'');$immagine=trim($_POST['immagine']??'');$old=dom_by_id('stazioni_radio',$id);
 dom_update('stazioni_radio',fn($r)=>(int)$r['id']===$id,['nome'=>$nome,'artisti'=>$artisti,'immagine'=>$immagine,'sfondo_css'=>$old['sfondo_css']??'']);$messaggio='Stazione aggiornata con successo!';
}
$res_stazioni=dom_result(dom_sort(dom_rows('stazioni_radio'),'id'));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Spotify - Gestione Stazioni Radio</title>
    <style type="text/css">
        .form-input {
            background-color: #181818;
            color: #ffffff;
            border: 1px solid #444;
            padding: 8px;
            border-radius: 4px;
            font-size: 12px;
            box-sizing: border-box;
        }
        .btn-green {
            background-color: #1db954;
            color: #000000;
            border: none;
            padding: 9px 18px;
            border-radius: 500px;
            font-weight: bold;
            font-size: 12px;
            cursor: pointer;
        }
        .btn-green:hover {
            background-color: #1ed760;
        }
        .btn-red {
            background-color: #e22134;
            color: #ffffff;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 11px;
            text-decoration: none;
            display: inline-block;
        }
        .btn-red:hover {
            background-color: #ff334b;
        }
    </style>
</head>
<body style="background: linear-gradient(180deg, #1f1f1f 0%, #121212 40%, #0a0a0a 100%); background-attachment: fixed; color: #ffffff; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; margin: 0; padding: 0; min-height: 100vh;">

    <?php include 'menu.php'; ?>

    <div style="margin-left: 230px; padding: 32px; max-width: 1300px; box-sizing: border-box;">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h1 style="font-size: 24px; font-weight: bold; margin: 0;">Gestione Stazioni Radio</h1>
            <a href="homepage.php" style="color: #1db954; text-decoration: none; font-size: 13px; font-weight: bold;">← Torna alla Homepage</a>
        </div>

        <?php if (!empty($messaggio)): ?>
            <div style="background-color: rgba(29,185,84,0.15); border: 1px solid #1db954; color: #1db954; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 13px;">
                <?php echo htmlspecialchars($messaggio); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errore)): ?>
            <div style="background-color: rgba(226,33,52,0.15); border: 1px solid #e22134; color: #e22134; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 13px;">
                <?php echo htmlspecialchars($errore); ?>
            </div>
        <?php endif; ?>

        <!-- FORM PER AGGIUNGERE UNA NUOVA STAZIONE -->
        <div style="background-color: #181818; padding: 24px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.05); margin-bottom: 30px;">
            <h3 style="margin-top: 0; font-size: 16px; color: #1db954; margin-bottom: 16px;">+ Aggiungi Nuova Stazione Radio</h3>
            <form action="gestione_stazioni.php" method="POST" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;">
                <input type="hidden" name="azione" value="aggiungi" />
                <div>
                    <label style="font-size: 11px; color: #b3b3b3; display: block; margin-bottom: 4px;">Nome Stazione</label>
                    <input type="text" name="nome" placeholder="es. Luchè Radio" class="form-input" required="required" />
                </div>
                <div style="flex-grow: 1;">
                    <label style="font-size: 11px; color: #b3b3b3; display: block; margin-bottom: 4px;">Artisti inclusi</label>
                    <input type="text" name="artisti" placeholder="es. Con Geolier, Guè, ecc." class="form-input" style="width: 100%;" />
                </div>
                <div>
                    <label style="font-size: 11px; color: #b3b3b3; display: block; margin-bottom: 4px;">Immagine (es. file.jpg)</label>
                    <input type="text" name="immagine" value="primo_piano.png" class="form-input" />
                </div>
                <div>
                    <button type="submit" class="btn-green">Crea Stazione</button>
                </div>
            </form>
        </div>

        <!-- TABELLA DI GESTIONE / MODIFICA ED ELIMINAZIONE -->
        <h3 style="font-size: 18px; margin-bottom: 16px;">Stazioni Radio Esistenti</h3>
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px; background-color: #181818; border-radius: 8px; overflow: hidden;">
            <thead>
                <tr style="border-bottom: 1px solid #333; color: #b3b3b3; font-size: 11px; text-transform: uppercase;">
                    <th style="padding: 12px;">Nome</th>
                    <th style="padding: 12px;">Artisti</th>
                    <th style="padding: 12px;">Immagine</th>
                    <th style="padding: 12px; text-align: center;">Azioni</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($res_stazioni && $res_stazioni->num_rows > 0): ?>
                    <?php while ($stazione = $res_stazioni->fetch_assoc()): ?>
                        <tr style="border-bottom: 1px solid #222;">
                            <form action="gestione_stazioni.php" method="POST">
                                <input type="hidden" name="azione" value="modifica" />
                                <input type="hidden" name="stazione_id" value="<?php echo $stazione['id']; ?>" />
                                
                                <td style="padding: 14px;">
                                    <input type="text" name="nome" value="<?php echo htmlspecialchars($stazione['nome']); ?>" class="form-input" style="width: 180px; font-weight: bold;" required="required" />
                                </td>
                                <td style="padding: 14px;">
                                    <input type="text" name="artisti" value="<?php echo htmlspecialchars($stazione['artisti']); ?>" class="form-input" style="width: 400px;" />
                                </td>
                                <td style="padding: 14px;">
                                    <input type="text" name="immagine" value="<?php echo htmlspecialchars($stazione['immagine']); ?>" class="form-input" style="width: 150px;" />
                                </td>
                                <td style="padding: 14px; text-align: center; white-space: nowrap;">
                                    <button type="submit" class="btn-green" style="padding: 6px 14px; margin-right: 6px;">Salva</button>
                                    <a href="gestione_stazioni.php?elimina=<?php echo $stazione['id']; ?>" class="btn-red" onclick="return confirm('Sei sicuro di voler eliminare questa stazione radio?');">Elimina</a>
                                </td>
                            </form>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="padding: 20px; text-align: center; color: #888;">Nessuna stazione radio trovata.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

    </div>

</body>
</html>
<?php 
if (isset($conn) && !$conn->connect_error) {
    $conn->close(); 
}
?>