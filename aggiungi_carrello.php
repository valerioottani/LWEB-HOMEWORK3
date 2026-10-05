<?php
if(session_status()===PHP_SESSION_NONE)session_start();require_once 'connection.php';
if($_SERVER['REQUEST_METHOD']==='POST'){$mid=(int)($_POST['merch_id']??0);if($mid>0){$row=dom_by_id('merchandise_album',$mid);if($row){$alb=dom_by_id('album',(int)$row['album_id']);$row['album_titolo']=$alb['titolo']??'Album Ufficiale';$_SESSION['carrello']=$_SESSION['carrello']??[];if(isset($_SESSION['carrello'][$mid]))$_SESSION['carrello'][$mid]['quantita']++;else $_SESSION['carrello'][$mid]=['id'=>$row['id'],'tipo_prodotto'=>$row['tipo_prodotto'],'album_titolo'=>$row['album_titolo'],'prezzo'=>(float)$row['prezzo'],'quantita'=>1];}}}
$conn->close();header('Location: carrello.php');exit;
?>