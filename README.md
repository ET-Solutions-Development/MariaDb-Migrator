# DatabaseMigrator

Il **DatabaseMigrator** è uno strumento PHP che consente di migrare tabelle e viste da un database "mittente" a un database "destinazione" con una gestione avanzata di esclusioni, inclusioni, viste e log dettagliati.

## Caratteristiche principali

- **Migrazione di tabelle**: Copia sia la struttura che i dati delle tabelle.
- **Migrazione di viste**: Facoltativamente, è possibile scegliere di includere o meno le viste.
- **Controllo di esclusione e inclusione delle tabelle**: Puoi scegliere di escludere o includere specifiche tabelle.
- **Controllo di sovrascrittura**: Puoi forzare la sovrascrittura delle tabelle già esistenti o ignorarle.
- **Log dettagliati**: Ogni azione è accompagnata da un messaggio di log chiaro e ben formattato.

---

## Requisiti

- PHP 7.4 o superiore
- Estensione MySQLi abilitata

---

## Installazione

1. Clona il repository:
   ```bash
   git clone https://github.com/tuo-username/DatabaseMigrator.git
   ```
2. Sposta i file nella directory del progetto.

---

## Esempio di utilizzo

```php
<?php

require_once 'DatabaseMigrator.php';

$mittenteConfig = [
    'host' => 'mittente_host',
    'user' => 'mittente_user',
    'password' => 'mittente_password',
    'database' => 'mittente_database'
];

$destinazioneConfig = [
    'host' => 'destinazione_host',
    'user' => 'destinazione_user',
    'password' => 'destinazione_password',
    'database' => 'destinazione_database',
    'port' => 3306
];

$migrator = new DatabaseMigrator($mittenteConfig, $destinazioneConfig);

// Configurazione personalizzata
$migrator->escludiTabelle(['log_attivita', 'sessioni']);
$migrator->includiTabelle(['utenti', 'ordini']);
$migrator->includiView(true);
$migrator->forzaCopia(true);

// Avvia la migrazione
$migrator->migrate();
```

---

## Metodi disponibili

### `__construct($mittenteConfig, $destinazioneConfig)`

Inizializza la connessione ai database "mittente" e "destinazione".

**Parametri:**

- **$mittenteConfig**: Array con le chiavi `host`, `user`, `password`, `database`.
- **$destinazioneConfig**: Array con le chiavi `host`, `user`, `password`, `database`, `port` (opzionale, predefinito 3306).

### `escludiTabelle(array $tables)`

Esclude le tabelle specificate dalla migrazione.

**Parametri:**

- **$tables**: Array con i nomi delle tabelle da escludere.

### `includiTabelle(array $tables)`

Include solo le tabelle specificate nella migrazione. Se vuoto, include tutte tranne quelle escluse.

**Parametri:**

- **$tables**: Array con i nomi delle tabelle da includere.

### `includiView(bool $include)`

Determina se includere le viste nella migrazione.

**Parametri:**

- **$include**: Booleano (`true` per includere, `false` per escludere).

### `forzaCopia(bool $force)`

Se `true`, le tabelle già presenti nel server di destinazione verranno sovrascritte.

**Parametri:**

- **$force**: Booleano (`true` per sovrascrivere, `false` per mantenere).

### `migrate()`

Avvia la migrazione. Stampa i log dettagliati delle azioni.

---

## Esempio di output

```
================= INIZIO MIGRAZIONE =================
  > Connessione al DB mittente riuscita.
  > Connessione al DB destinazione riuscita.
  > Disabilitazione controlli foreign key...

----- Inizio lavorazione tabella: utenti -----
  > Copio la struttura della tabella utenti
  > Copio i dati della tabella utenti
  > Lavorazione completata per la tabella utenti

----- Inizio lavorazione tabella: ordini -----
  > Copio la struttura della tabella ordini
  > Copio i dati della tabella ordini
  > Lavorazione completata per la tabella ordini

================= RESOCONTO MIGRAZIONE =================
  - Connessione al DB mittente riuscita.
  - Connessione al DB destinazione riuscita.
  - Disabilitazione controlli foreign key.
  - Inizio lavorazione tabella: utenti
  - Copio la struttura della tabella utenti
  - Copio i dati della tabella utenti
  - Lavorazione completata per la tabella utenti
  - Inizio lavorazione tabella: ordini
  - Copio la struttura della tabella ordini
  - Copio i dati della tabella ordini
  - Lavorazione completata per la tabella ordini
  - Migrazione completata.
=======================================================
```

---

## Contributi

Se vuoi contribuire, sentiti libero di inviare una **pull request** o aprire una **issue** con suggerimenti o miglioramenti.

---

## Licenza

Questo progetto è concesso in licenza sotto la licenza MIT. Consulta il file `LICENSE` per ulteriori dettagli.
