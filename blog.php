<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'connection.php';
$is_logged=isset($_SESSION['user']); $ruolo=$_SESSION['ruolo']??'';
$articoli_db=dom_sort(dom_rows('articoli_blog'),'id',true);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Spotify - Blog & News</title>
    <style type="text/css">
        .blog-post-card {
            background-color: #202020;
            padding: 14px;
            border-radius: 6px;
            transition: background-color 0.2s ease, transform 0.2s ease;
            cursor: pointer;
            border: 1px solid rgba(255,255,255,0.03);
        }
        .blog-post-card:hover {
            background-color: #2a2a2a;
            transform: translateY(-2px);
        }
        .admin-link-btn {
            background-color: #1db954;
            color: #000000;
            padding: 6px 14px;
            border-radius: 500px;
            font-weight: bold;
            font-size: 12px;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.2s ease;
        }
        .admin-link-btn:hover {
            background-color: #1ed760;
        }
    </style>
</head>
<body style="background: linear-gradient(180deg, #1f1f1f 0%, #121212 40%, #0a0a0a 100%); background-attachment: fixed; color: #ffffff; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; margin: 0; padding: 0; min-height: 100vh;">

    <?php include 'menu.php'; ?>

    <div style="margin-left: 230px; padding: 32px; max-width: 800px; box-sizing: border-box;">
        
        <!-- Intestazione con titolo e pulsante admin per la gestione -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1 style="font-size: 26px; font-weight: bold; margin: 0; color: #ffffff;">📰 Spotify Blog & News</h1>
            
            <?php if ($is_logged && $ruolo === 'admin'): ?>
                <div>
                    <a href="gestione_articoli.php" class="admin-link-btn">⚙️ Gestisci Articoli</a>
                </div>
            <?php endif; ?>
        </div>

        <div style="padding: 24px; border-radius: 12px; background-color: #181818; border: 1px solid rgba(255,255,255,0.05);">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
                <h3 style="font-size: 20px; font-weight: bold; margin: 0; color: #ffffff;">Ultime Notizie</h3>
                <span style="font-size: 10px; color: #1db954; font-weight: bold; text-transform: uppercase;">Aggiornato</span>
            </div>
            <div style="display: flex; flex-direction: column; gap: 14px;">
                <?php foreach ($articoli_db as $post): ?>
                    <a href="articolo.php?titolo=<?php echo urlencode($post['titolo']); ?>" style="text-decoration: none; color: inherit;">
                        <div class="blog-post-card">
                            <p style="font-size: 15px; font-weight: bold; margin: 0 0 6px 0; color: #ffffff; line-height: 1.3;"><?php echo htmlspecialchars($post['titolo']); ?></p>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-size: 12px; color: #b3b3b3;"><?php echo htmlspecialchars(isset($post['autore']) ? $post['autore'] : 'Redazione'); ?></span>
                                <span style="font-size: 11px; color: #888888;"><?php echo htmlspecialchars(isset($post['data']) ? $post['data'] : 'Oggi'); ?></span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

</body>
</html>
<?php 
if (isset($conn) && !$conn->connect_error) {
    $conn->close(); 
}
?>