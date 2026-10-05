# LWEB-HOMEWORK3 — Piattaforma Musicale (Spotify Style) con XML e DOM

## Componenti del Gruppo e Repository
* Valerio Ottani 
* Giovanni La Penna
* Indirizzo repository GitHub:
  - https://github.com/valerioottani/LWEB-HOMEWORK3.git
  - https://github.com/Giogiogabbana/LWEB-HOMEWORK3.git
## Descrizione dell'Applicazione
Questo progetto evolve la piattaforma musicale in stile Spotify passando a un'architettura basata su file XML in combinazione con il DOM (DOMDocument) in PHP. Il sistema gestisce il catalogo musicale, i brani, gli album, il merchandise, la community e gli eventi sfruttando documenti XML strutturati, mentre la base di dati relazionale MySQL è stata ridotta al minimo indispensabile, venendo impiegata esclusivamente per la tabella anagrafica degli utenti, come consentito dalle specifiche del docente.

## Credenziali di Accesso e Test
Lo script di installazione/configurazione predispone i dati iniziali e i seguenti account di test:

* **Utente Amministratore:** 
  - Username: `admin`
  - Password: `adminpassword`
  - CVV carta di credito: `123`

* **Utente Standard:**
  - Username: `utente`
  - Password: `utentepassword`
  - CVV carta di credito: `456`

## Scelta delle Grammatiche: DTD e XML Schema (XSD)
La grammatica dei documenti XML non è stata resa uniforme artificialmente, ma scelta caso per caso in base alla struttura e ai vincoli dei dati:
- **DTD** è stata adottata per documenti prevalentemente descrittivi e testuali, con struttura semplice e regolare, dove è importante stabilire l'ordine e la ripetibilità degli elementi.
- **XSD (XML Schema)** è stata adottata per documenti con dati maggiormente tipizzati o vincolati (interi, decimali, valori enumerati, identificativi e stati booleani).

### 1. Documenti validati tramite DTD:
* **artisti.xml -> artisti.dtd**: Documento anagrafico/descrittivo. I campi nome, biografia e immagine sono testuali.
* **brani.xml -> brani.dtd**: Struttura regolare per la collezione di brani (id, album_id, titolo, durata, immagine).
* **stazioni_radio.xml -> stazioni_radio.dtd**: Dati di presentazione per le stazioni (nome, descrizione, immagine, sfondo CSS).
* **articoli_blog.xml -> articoli_blog.dtd**: Documento testuale per la sezione editoriale e gli articoli del blog.
* **messaggi_community.xml -> messaggi_community.dtd**: Contenuto testuale e sequenziale con gestione dell'attributo opzionale `null` mediante ATTLIST.

### 2. Documenti validati tramite XML Schema (XSD):
* **album.xml -> album.xsd**: Tipizzazione rigorosa per anni, identificativi e relazioni logiche tra collezioni.
* **merchandise_album.xml -> merchandise_album.xsd**: Gestione di prezzi decimali, stati booleani/numerici per la disponibilità e ID.
* **acquisti_merch.xml -> acquisti_merch.xsd**: Modellazione avanzata di quantità numeriche, date e contenuti opzionali/nulli.
* **eventi.xml -> eventi.xsd**: Applicazione di tipi precisi per i dati dei concerti e i collegamenti web.
* **playlist.xml -> playlist.xsd**: Restrizione del dominio dei valori tramite `simpleType`/`restriction`/`enumeration` per le categorie delle playlist.
* **playlist_tracce.xml -> playlist_tracce.xsd**: Struttura associativa (tabella ponte) con identificativi numerici tipizzati.
* **posti_evento.xml -> posti_evento.xsd**: Tipizzazione fondamentale per scopi operativi di prenotazione (id ed evento_id numerici, prezzo decimale, stato occupato booleano e username opzionale/nullo).

## Architettura e Specifiche Tecniche (XML-DOM)
- MySQL viene usato esclusivamente per la tabella utenti.
- install.php crea esclusivamente la tabella utenti.
- Tutte le altre entità sono file XML memorizzati nella cartella `xml/`.
- Le pagine PHP non eseguono query MySQL sulle entità XML.
- La lettura e la modifica dei documenti XML avvengono mediante DOM (`DOMDocument`).
- Il file `dom.php` contiene esclusivamente funzioni procedurali comuni basate su DOM; non apre connessioni MySQL e non simula tabelle SQL.
- Non esiste alcuna cartella lib e non sono presenti file di astrazione relazionale non richiesti.
- DTD e XSD sono usati come grammatiche dei documenti XML secondo la scelta documentata.

## Difficoltà Incontrate e Soluzioni Tecniche
- La gestione dei riferimenti incrociati tra i dati (come il legame tra brani/album o acquisti/utenti) è stata risolta mappando i nodi ID all'interno degli alberi DOM e preservando il riferimento testuale `username` per collegare le azioni ai profili presenti sul database SQL.
- Si è curata la portabilità dei percorsi relativi dei file XML e delle grammatiche per consentire l'installazione dell'applicazione in qualsiasi directory scelta.

---
Homework realizzato per l'esame di Linguaggi per il Web - Homework 3 (XML con DOM).