<?php
/* Accesso procedurale DOM ai file XML. MySQL NON viene usato per queste entità. */
final class DomResult {
    private array $rows; private int $i = 0; public int $num_rows;
    function __construct(array $rows){ $this->rows=array_values($rows); $this->num_rows=count($this->rows); }
    function fetch_assoc(): ?array { return $this->rows[$this->i++] ?? null; }
}
function dom_file(string $name): string { return __DIR__ . '/xml/' . $name . '.xml'; }
function dom_item_name(string $name): string {
    return ['artisti'=>'artista','album'=>'disco','brani'=>'brano','merchandise_album'=>'prodotto','acquisti_merch'=>'acquisto','eventi'=>'evento','playlist'=>'lista','stazioni_radio'=>'stazione','playlist_tracce'=>'associazione','messaggi_community'=>'messaggio_community','posti_evento'=>'posto','articoli_blog'=>'articolo'][$name] ?? 'record';
}
function dom_fields(string $name): array {
    return [
      'artisti'=>['id','nome','biografia','immagine'],
      'album'=>['id','artista_id','titolo','anno','copertina','curiosita'],
      'brani'=>['id','album_id','titolo','durata','immagine_brano'],
      'merchandise_album'=>['id','album_id','tipo_prodotto','prezzo','immagine_prodotto','disponibile'],
      'acquisti_merch'=>['id','username','merchandise_id','quantita','data_acquisto'],
      'eventi'=>['id','giorno','mese','titolo','luogo','link_biglietti'],
      'playlist'=>['id','titolo','descrizione','immagine','sfondo','tipo'],
      'stazioni_radio'=>['id','nome','artisti','immagine','sfondo_css'],
      'playlist_tracce'=>['playlist_id','traccia_id'],
      'messaggi_community'=>['id','artista_gruppo','username','messaggio','data_invio'],
      'posti_evento'=>['id','evento_id','settore','numero_posto','prezzo','occupato','username'],
      'articoli_blog'=>['id','titolo','contenuto','autore','data']
    ][$name] ?? [];
}
function dom_defaults(string $name): array {
    return [
      'album'=>['curiosita'=>''], 'brani'=>['immagine_brano'=>'album.png'],
      'merchandise_album'=>['disponibile'=>1], 'acquisti_merch'=>['quantita'=>1,'data_acquisto'=>date('Y-m-d H:i:s')],
      'eventi'=>['link_biglietti'=>'#'], 'playlist'=>['descrizione'=>'','immagine'=>'album.png','sfondo'=>'linear-gradient(135deg,#1e3264,#000)','tipo'=>'playlist'],
      'stazioni_radio'=>['artisti'=>'','immagine'=>'album.png','sfondo_css'=>'linear-gradient(135deg,#1e3264,#000)'],
      'messaggi_community'=>['data_invio'=>date('Y-m-d H:i:s')], 'posti_evento'=>['occupato'=>0,'username'=>null],
      'articoli_blog'=>['autore'=>'Redazione','data'=>'Oggi']
    ][$name] ?? [];
}
function dom_normalize_row(string $name,array $row): array {
    $row=$row+dom_defaults($name);
    $integerFields=['id','artista_id','album_id','merchandise_id','quantita','giorno','playlist_id','traccia_id','evento_id','occupato','disponibile'];
    $decimalFields=['prezzo'];
    foreach($row as $k=>$v){
        if($v===null) continue;
        if(in_array($k,$integerFields,true) && $v!=='') $row[$k]=(string)(int)$v;
        elseif(in_array($k,$decimalFields,true) && $v!=='') $row[$k]=number_format((float)$v,2,'.','');
        else $row[$k]=(string)$v;
    }
    return $row;
}
function dom_rows(string $name): array {
    $file=dom_file($name); if(!is_file($file)) return [];
    $dom=new DOMDocument(); $dom->preserveWhiteSpace=false; $old=libxml_use_internal_errors(true);
    $ok=$dom->load($file, LIBXML_NOBLANKS|LIBXML_DTDLOAD); libxml_clear_errors(); libxml_use_internal_errors($old); if(!$ok) return [];
    $rows=[]; foreach($dom->documentElement->childNodes as $node){
        if($node->nodeType!==XML_ELEMENT_NODE) continue; $r=[];
        foreach($node->childNodes as $f){ if($f->nodeType!==XML_ELEMENT_NODE) continue; $r[$f->nodeName]=$f->getAttribute('null')==='true'?null:$f->textContent; }
        $rows[]=$r;
    } return $rows;
}
function dom_result(array $rows): DomResult { return new DomResult($rows); }
function dom_validation_errors(): string {
    $parts=[]; foreach(libxml_get_errors() as $e){ $m=trim($e->message); if($m!=='') $parts[]=$m; }
    return implode(' | ',array_unique($parts));
}
/* Valida il candidato prima di sostituire il file reale. Per le DTD il candidato
   viene scritto temporaneamente NELLA CARTELLA xml, così il SYSTEM ../dtd/... resta risolvibile. */
function dom_validate_xml(string $name,string $xml,string $targetFile): array {
    $xsd=__DIR__.'/xsd/'.$name.'.xsd'; $dtd=__DIR__.'/dtd/'.$name.'.dtd';
    $old=libxml_use_internal_errors(true); libxml_clear_errors(); $ok=false; $detail='';
    if(is_file($xsd)){
        $check=new DOMDocument(); $check->preserveWhiteSpace=false;
        $ok=$check->loadXML($xml,LIBXML_NOBLANKS) && $check->schemaValidate($xsd);
        if(!$ok) $detail=dom_validation_errors();
    } elseif(is_file($dtd)){
        $tmp=dirname($targetFile).'/.'.basename($targetFile).'.validate.'.uniqid('',true).'.xml';
        if(file_put_contents($tmp,$xml,LOCK_EX)===false){ libxml_use_internal_errors($old); return [false,'Impossibile creare il file temporaneo di validazione']; }
        $check=new DOMDocument(); $check->preserveWhiteSpace=false;
        $loaded=$check->load($tmp,LIBXML_NOBLANKS|LIBXML_DTDLOAD);
        $ok=$loaded && $check->validate();
        if(!$ok) $detail=dom_validation_errors();
        @unlink($tmp);
    } else {
        $check=new DOMDocument(); $ok=$check->loadXML($xml,LIBXML_NOBLANKS); if(!$ok) $detail=dom_validation_errors();
    }
    libxml_clear_errors(); libxml_use_internal_errors($old); return [(bool)$ok,$detail];
}
function dom_save_rows(string $name,array $rows): void {
    $file=dom_file($name); if(!is_file($file)) throw new RuntimeException("File XML non trovato: $name");
    $old=new DOMDocument(); $old->preserveWhiteSpace=false;
    if(!$old->load($file,LIBXML_NOBLANKS|LIBXML_DTDLOAD)) throw new RuntimeException("XML non leggibile: $name");
    $rootName=$old->documentElement->nodeName; $attrs=[];
    foreach($old->documentElement->attributes as $a) $attrs[]=['name'=>$a->nodeName,'value'=>$a->nodeValue,'ns'=>$a->namespaceURI];
    $raw=file_get_contents($file); $doctype=''; if(preg_match('/<!DOCTYPE[^>]+>/',$raw,$m)) $doctype=$m[0];
    $dom=new DOMDocument('1.0','UTF-8'); $dom->formatOutput=true; $root=$dom->createElement($rootName); $dom->appendChild($root);
    foreach($attrs as $a){ if($a['ns']) $root->setAttributeNS($a['ns'],$a['name'],$a['value']); else $root->setAttribute($a['name'],$a['value']); }
    $fields=dom_fields($name);
    foreach($rows as $row){
        $row=dom_normalize_row($name,$row); $item=$dom->createElement(dom_item_name($name)); $root->appendChild($item);
        foreach($fields as $k){
            if(!array_key_exists($k,$row)) continue;
            $v=$row[$k]; $e=$dom->createElement($k);
            if($v===null){
                /* Solo la DTD dei messaggi prevede esplicitamente l'attributo null. Negli XSD
                   i campi opzionali vengono rappresentati come elemento vuoto/omesso senza attributi estranei. */
                if($name==='messaggi_community' && $k==='data_invio') $e->setAttribute('null','true');
            } else $e->appendChild($dom->createTextNode((string)$v));
            $item->appendChild($e);
        }
    }
    $xml=$dom->saveXML(); if($doctype) $xml=preg_replace('/(<\?xml[^?]+\?>)/','$1'."\n".$doctype,$xml,1);
    [$valid,$detail]=dom_validate_xml($name,$xml,$file);
    if(!$valid) throw new RuntimeException("Scrittura annullata: il documento $name non rispetta DTD/XSD".($detail!==''?". Dettaglio: $detail":'.'));
    /* Compatibile anche con XAMPP/Windows: evitiamo rename() sopra un file esistente. */
    if(file_put_contents($file,$xml,LOCK_EX)===false) throw new RuntimeException("Impossibile scrivere $name");
}
function dom_insert(string $name,array $row): int {
    $rows=dom_rows($name); $row=dom_normalize_row($name,$row);
    if(!array_key_exists('id',$row) && in_array('id',dom_fields($name),true)){ $ids=array_map(fn($r)=>(int)($r['id']??0),$rows); $row=['id'=>(string)(empty($ids)?1:max($ids)+1)]+$row; }
    if($name==='playlist_tracce') foreach($rows as $r) if((int)$r['playlist_id']===(int)$row['playlist_id'] && (int)$r['traccia_id']===(int)$row['traccia_id']) return 0;
    $rows[]=$row; dom_save_rows($name,$rows); return (int)($row['id']??0);
}
function dom_update(string $name,callable $match,array $changes): int {
    $rows=dom_rows($name); $n=0;
    foreach($rows as &$r){ if($match($r)){ foreach($changes as $k=>$v) if(in_array($k,dom_fields($name),true)) $r[$k]=$v; $r=dom_normalize_row($name,$r); $n++; } }
    unset($r); if($n) dom_save_rows($name,$rows); return $n;
}
function dom_delete(string $name,callable $match): int {
    $rows=dom_rows($name); $before=count($rows); $rows=array_values(array_filter($rows,fn($r)=>!$match($r))); $deleted=$before-count($rows);
    if($deleted) dom_save_rows($name,$rows); return $deleted;
}
function dom_by_id(string $name,int $id): ?array { foreach(dom_rows($name) as $r) if((int)($r['id']??0)===$id)return $r; return null; }
function dom_sort(array $rows,string $key,bool $desc=false): array { usort($rows,fn($a,$b)=>$desc?strnatcasecmp((string)($b[$key]??''),(string)($a[$key]??'')):strnatcasecmp((string)($a[$key]??''),(string)($b[$key]??'')));return $rows; }
function dom_albums_with_artists(): array { $arts=[];foreach(dom_rows('artisti') as $a)$arts[$a['id']]=$a; $out=[];foreach(dom_rows('album') as $a){$a['album']=$a['titolo'];$a['artista']=$arts[$a['artista_id']]['nome']??'';$out[]=$a;}return $out; }
function dom_tracks_with_album_artist(): array { $albs=[];foreach(dom_albums_with_artists() as $a)$albs[$a['id']]=$a;$out=[];foreach(dom_rows('brani') as $t){$t['track_id']=$t['id'];$t['album']=$albs[$t['album_id']]['titolo']??'';$t['album_nome']=$t['album'];$t['artista']=$albs[$t['album_id']]['artista']??'';$out[]=$t;}return $out; }
?>
