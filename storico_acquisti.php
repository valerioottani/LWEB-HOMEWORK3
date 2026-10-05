<?php
if(session_status()===PHP_SESSION_NONE)session_start();require_once 'connection.php';if(!isset($_SESSION['user'])){header('Location: login.php');exit;}$u=$_SESSION['user'];
$mer=[];foreach(dom_rows('merchandise_album') as $m)$mer[$m['id']]=$m;$al=[];foreach(dom_rows('album') as $a)$al[$a['id']]=$a;$acquisti_merch=[];foreach(dom_rows('acquisti_merch') as $a)if(($a['username']??'')===$u&&isset($mer[$a['merchandise_id']])){$m=$mer[$a['merchandise_id']];$acquisti_merch[]=['ordine_id'=>$a['id'],'data_acquisto'=>$a['data_acquisto'],'quantita'=>$a['quantita'],'tipo_prodotto'=>$m['tipo_prodotto'],'prezzo'=>$m['prezzo'],'immagine_prodotto'=>$m['immagine_prodotto'],'album_titolo'=>$al[$m['album_id']]['titolo']??''];}usort($acquisti_merch,fn($a,$b)=>strcmp($b['data_acquisto'],$a['data_acquisto']));
$ev=[];foreach(dom_rows('eventi') as $e)$ev[$e['id']]=$e;$acquisti_eventi=[];foreach(dom_rows('posti_evento') as $p)if((int)$p['occupato']===1&&($p['username']??'')===$u){$e=$ev[$p['evento_id']]??[];$acquisti_eventi[]=['prenotazione_id'=>$p['id'],'settore'=>$p['settore'],'numero_posto'=>$p['numero_posto'],'prezzo'=>$p['prezzo'],'evento_titolo'=>$e['titolo']??'','giorno'=>$e['giorno']??'','mese'=>$e['mese']??'','luogo'=>$e['luogo']??''];}usort($acquisti_eventi,fn($a,$b)=>(int)$b['prenotazione_id']<=>(int)$a['prenotazione_id']);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Spotify - Storico Acquisti e Ricevute</title>
    <style type="text/css">
        .table-storico {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 6px;
            background-color: transparent;
            margin-bottom: 30px;
        }
        .table-storico th {
            padding: 10px 16px;
            text-align: left;
            background-color: transparent;
            color: #b3b3b3;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 1px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        .table-storico td {
            padding: 14px 16px;
            font-size: 14px;
        }
        /* Effetto in rilievo al passaggio del cursore sulle righe di entrambe le tabelle */
        .acq-row {
            background-color: #181818;
            transition: background-color 0.25s ease, transform 0.2s ease, box-shadow 0.2s ease;
            border: 1px solid rgba(255,255,255,0.03);
        }
        .acq-row:hover {
            background-color: #2a2a2a !important;
            transform: scale(1.01);
            box-shadow: 0 6px 20px rgba(0,0,0,0.7);
            position: relative;
            z-index: 2;
        }
        .btn-ricevuta {
            background-color: #1db954;
            color: #000000;
            padding: 6px 14px;
            border-radius: 500px;
            font-weight: bold;
            font-size: 11px;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.2s ease;
            white-space: nowrap;
        }
        .btn-ricevuta:hover {
            background-color: #1ed760;
        }
    </style>
</head>
<body style="background: linear-gradient(180deg, #1f1f1f 0%, #121212 40%, #0a0a0a 100%); background-attachment: fixed; color: #ffffff; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; margin: 0; padding: 0; min-height: 100vh;">

    <?php include 'menu.php'; ?>

    <div style="margin-left: 230px; padding: 32px; max-width: 1200px; box-sizing: border-box;">
        
        <h1 style="font-size: 28px; font-weight: 900; margin-bottom: 8px;">📦 Storico Acquisti & Ricevute</h1>
        <p style="color: #b3b3b3; font-size: 14px; margin-bottom: 30px;">Visualizza l'elenco completo di tutti i prodotti di merchandise e i biglietti acquistati con il tuo account.</p>

        <!-- SEZIONE MERCHANDISE -->
        <h2 style="font-size: 18px; font-weight: bold; margin-bottom: 14px; color: #1db954;">Articoli Merchandise</h2>
        <?php if (!empty($acquisti_merch)): ?>
            <table class="table-storico">
                <thead>
                    <tr>
                        <th style="width: 50px;">Prodotto</th>
                        <th>Articolo / Tipologia</th>
                        <th>Album Riferimento</th>
                        <th>Quantità</th>
                        <th>Data Ordine</th>
                        <th>Prezzo Totale</th>
                        <th style="text-align: center;">Ricevuta</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($acquisti_merch as $item): 
                        $totale_articolo = $item['prezzo'] * $item['quantita'];
                    ?>
                        <tr class="acq-row">
                            <td style="border-top-left-radius: 6px; border-bottom-left-radius: 6px;">
                                <img src="img/<?php echo htmlspecialchars($item['immagine_prodotto']); ?>" alt="" style="width: 36px; height: 36px; border-radius: 4px; object-fit: cover; background-color: #222;" />
                            </td>
                            <td><strong style="color: #fff;"><?php echo htmlspecialchars($item['tipo_prodotto']); ?></strong></td>
                            <td style="color: #b3b3b3;"><?php echo htmlspecialchars($item['album_titolo']); ?></td>
                            <td><?php echo (int)$item['quantita']; ?></td>
                            <td style="color: #888; font-size: 12px;"><?php echo htmlspecialchars($item['data_acquisto']); ?></td>
                            <td style="font-weight: bold; color: #1db954;">€ <?php echo number_format($totale_articolo, 2, ',', '.'); ?></td>
                            <td style="text-align: center; border-top-right-radius: 6px; border-bottom-right-radius: 6px;">
                                <a href="ricevuta_ordine.php?id=<?php echo $item['ordine_id']; ?>" target="_blank" class="btn-ricevuta">📄 Ricevuta</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div style="background-color: #181818; padding: 24px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.05); margin-bottom: 30px;">
                <p style="color: #b3b3b3; font-size: 13px; margin: 0;">Non hai ancora effettuato alcun acquisto di merchandise.</p>
            </div>
        <?php endif; ?>

        <!-- SEZIONE BIGLIETTI EVENTI LIVE -->
        <h2 style="font-size: 18px; font-weight: bold; margin-bottom: 14px; color: #1db954;">Biglietti & Eventi Live</h2>
        <?php if (!empty($acquisti_eventi)): ?>
            <table class="table-storico">
                <thead>
                    <tr>
                        <th>Evento Live</th>
                        <th>Luogo</th>
                        <th>Settore & Posto</th>
                        <th>Data Evento</th>
                        <th>Prezzo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($acquisti_eventi as $ev): ?>
                        <tr class="acq-row">
                            <td style="border-top-left-radius: 6px; border-bottom-left-radius: 6px;"><strong style="color: #fff;"><?php echo htmlspecialchars($ev['evento_titolo']); ?></strong></td>
                            <td style="color: #b3b3b3;"><?php echo htmlspecialchars($ev['luogo']); ?></td>
                            <td><?php echo htmlspecialchars($ev['settore'] . ' - Posto ' . $ev['numero_posto']); ?></td>
                            <td style="color: #888; font-size: 12px; border-top-right-radius: 6px; border-bottom-right-radius: 6px;"><?php echo htmlspecialchars($ev['giorno'] . ' ' . $ev['mese']); ?></td>
                            <td style="font-weight: bold; color: #1db954;">€ <?php echo number_format($ev['prezzo'], 2, ',', '.'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div style="background-color: #181818; padding: 24px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.05);">
                <p style="color: #b3b3b3; font-size: 13px; margin: 0;">Nessun biglietto per eventi live registrato al momento.</p>
            </div>
        <?php endif; ?>

    </div>

</body>
</html>
<?php 
if (isset($conn) && !$conn->connect_error) {
    $conn->close(); 
}
?>