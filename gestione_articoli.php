<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'connection.php';
if (!isset($_SESSION['user']) || ($_SESSION['ruolo']??'') !== 'admin') { header('Location: homepage.php'); exit(); }
$messaggio=''; $errore='';
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['azione']??'')==='aggiungi') {
    $titolo=trim($_POST['titolo']??''); $contenuto=trim($_POST['contenuto']??''); $autore=trim($_POST['autore']??'') ?: 'Redazione'; $data_art=date('d M Y');
    $dup=false; foreach(dom_rows('articoli_blog') as $a) if($a['titolo']===$titolo)$dup=true;
    if($titolo!=='' && $contenuto!=='' && !$dup){ dom_insert('articoli_blog',['titolo'=>$titolo,'contenuto'=>$contenuto,'autore'=>$autore,'data'=>$data_art]); $messaggio='Articolo pubblicato con successo!'; }
    else $errore=$dup?'Esiste già un articolo con questo titolo.':'Compila tutti i campi obbligatori.';
}
if(isset($_GET['elimina'])){ dom_delete('articoli_blog',fn($r)=>(int)$r['id']===(int)$_GET['elimina']); $messaggio='Articolo eliminato con successo!'; }
if($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['azione']??'')==='modifica'){
    $id=(int)($_POST['articolo_id']??0); $titolo=trim($_POST['titolo']??''); $autore=trim($_POST['autore']??''); $contenuto=trim($_POST['contenuto']??'');
    if($titolo!==''&&$contenuto!==''){dom_update('articoli_blog',fn($r)=>(int)$r['id']===$id,['titolo'=>$titolo,'autore'=>$autore,'contenuto'=>$contenuto]);$messaggio='Articolo aggiornato con successo!';}else $errore='I campi non possono essere vuoti.';
}
$res_art=dom_result(dom_sort(dom_rows('articoli_blog'),'id',true));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Spotify - Gestione Articoli Blog</title>
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

    <div style="margin-left: 230px; padding: 32px; max-width: 1400px; box-sizing: border-box;">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h1 style="font-size: 24px; font-weight: bold; margin: 0;">Gestione Articoli del Blog</h1>
            <a href="blog.php" style="color: #1db954; text-decoration: none; font-size: 13px; font-weight: bold;">← Torna al Blog</a>
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

        <!-- FORM AGGIUNTA ARTICOLO -->
        <div style="background-color: #181818; padding: 24px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.05); margin-bottom: 30px;">
            <h3 style="margin-top: 0; font-size: 16px; color: #1db954; margin-bottom: 16px;">+ Scrivi Nuovo Articolo</h3>
            <form action="gestione_articoli.php" method="POST" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;">
                <input type="hidden" name="azione" value="aggiungi" />
                
                <div style="flex-grow: 1; min-width: 250px;">
                    <label style="font-size: 11px; color: #b3b3b3; display: block; margin-bottom: 4px;">Titolo Articolo</label>
                    <input type="text" name="titolo" placeholder="Titolo accattivante..." class="form-input" style="width: 100%;" required="required" />
                </div>

                <div style="width: 180px;">
                    <label style="font-size: 11px; color: #b3b3b3; display: block; margin-bottom: 4px;">Autore</label>
                    <input type="text" name="autore" placeholder="Redazione Urban" class="form-input" style="width: 100%;" />
                </div>

                <div style="flex-basis: 100%;">
                    <label style="font-size: 11px; color: #b3b3b3; display: block; margin-bottom: 4px;">Contenuto</label>
                    <textarea name="contenuto" placeholder="Testo dell'articolo..." class="form-input" style="width: 100%; height: 90px; resize: vertical;" required="required"></textarea>
                </div>

                <div>
                    <button type="submit" class="btn-green">Pubblica Articolo</button>
                </div>
            </form>
        </div>

        <!-- TABELLA ARTICOLI ESISTENTI -->
        <h3 style="font-size: 18px; margin-bottom: 16px;">Articoli Inseriti e Pubblicati</h3>
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px; background-color: #181818; border-radius: 8px; overflow: hidden;">
            <thead>
                <tr style="border-bottom: 1px solid #333; color: #b3b3b3; font-size: 11px; text-transform: uppercase;">
                    <th style="padding: 12px;">Titolo</th>
                    <th style="padding: 12px;">Autore</th>
                    <th style="padding: 12px;">Contenuto</th>
                    <th style="padding: 12px;">Data</th>
                    <th style="padding: 12px; text-align: center;">Azioni</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($res_art && $res_art->num_rows > 0): ?>
                    <?php while ($art = $res_art->fetch_assoc()): ?>
                        <tr style="border-bottom: 1px solid #222;">
                            <form action="gestione_articoli.php" method="POST">
                                <input type="hidden" name="azione" value="modifica" />
                                <input type="hidden" name="articolo_id" value="<?php echo $art['id']; ?>" />
                                
                                <td style="padding: 14px;">
                                    <input type="text" name="titolo" value="<?php echo htmlspecialchars($art['titolo']); ?>" class="form-input" style="width: 220px; font-weight: bold;" required="required" />
                                </td>
                                <td style="padding: 14px;">
                                    <input type="text" name="autore" value="<?php echo htmlspecialchars(isset($art['autore']) ? $art['autore'] : 'Redazione'); ?>" class="form-input" style="width: 130px;" />
                                </td>
                                <td style="padding: 14px;">
                                    <textarea name="contenuto" class="form-input" style="width: 320px; height: 50px; resize: vertical;" required="required"><?php echo htmlspecialchars(isset($art['contenuto']) ? $art['contenuto'] : ''); ?></textarea>
                                </td>
                                <td style="padding: 14px; color: #aaa; white-space: nowrap;"><?php echo htmlspecialchars($art['data']); ?></td>
                                <td style="padding: 14px; text-align: center; white-space: nowrap;">
                                    <button type="submit" class="btn-green" style="padding: 6px 14px; margin-right: 6px;">Salva</button>
                                    <a href="gestione_articoli.php?elimina=<?php echo $art['id']; ?>" class="btn-red" onclick="return confirm('Eliminare questo articolo?');">Elimina</a>
                                </td>
                            </form>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="padding: 20px; text-align: center; color: #888;">Nessun articolo trovato nel database.</td>
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