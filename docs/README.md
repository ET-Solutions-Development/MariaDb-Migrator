
# 📘 Documentazione Dettagliata - MariaDb-Migrator

Benvenuto nella documentazione ufficiale e dettagliata di **MariaDb-Migrator**. Questo file contiene informazioni approfondite sui metodi e le funzionalità principali della libreria.

---

## 📖 **Indice**

- [Introduzione](#introduzione)
- [Metodi principali](#metodi-principali)
- [Descrizione dei metodi](#descrizione-dei-metodi)
- [Esempi pratici](#esempi-pratici)
- [Output della console](#output-della-console)
- [Errori comuni](#errori-comuni)
- [Contatti](#contatti)

---

## 🛠️ **Introduzione**

MariaDb-Migrator consente di migrare dati, tabelle e viste da un database MariaDB o MySQL a un altro. Puoi personalizzare le tabelle incluse, le tabelle escluse e gestire la sovrascrittura delle tabelle già esistenti.

---

## 📋 **Metodi principali**

| **Metodo**         | **Descrizione**                                           |
|-------------------|---------------------------------------------------------|
| `__construct(array $mittenteConfig, array $destinazioneConfig)` | Inizializza la connessione ai database mittente e destinazione. |
| `escludiTabelle(array $tabelle)` | Esclude le tabelle specificate dalla migrazione.  |
| `includiTabelle(array $tabelle)` | Migra solo le tabelle specificate (ha priorità).  |
| `includiView(bool $include)`     | Specifica se includere le viste durante la migrazione. |
| `forzaCopia(bool $force)`        | Se **true**, forza la copia anche se la tabella esiste già. |
| `migrate()`                     | Avvia la migrazione.                            |
| `testConnection(string $tipo)`  | Testa la connessione al database mittente o destinazione. |
| `getExcludedTables()`            | Restituisce l'elenco delle tabelle escluse. |
| `getIncludedTables()`            | Restituisce l'elenco delle tabelle incluse. |

---

## 📘 **Descrizione dei metodi**

### 1️⃣ **`__construct(array $mittenteConfig, array $destinazioneConfig)`**

**Descrizione**: Inizializza la connessione ai database mittente e destinazione. Se la connessione non riesce, viene generato un errore.

**Parametri**
- `$mittenteConfig`: Configurazione per il database mittente (`host`, `user`, `password`, `database`, `port`).
- `$destinazioneConfig`: Configurazione per il database di destinazione (`host`, `user`, `password`, `database`, `port`).

---

### 2️⃣ **`escludiTabelle(array $tabelle)`**

**Descrizione**: Aggiunge una lista di tabelle da escludere dalla migrazione.

**Parametri**
- `$tabelle`: Array contenente i nomi delle tabelle da escludere.

---

### 3️⃣ **`includiTabelle(array $tabelle)`**

**Descrizione**: Specifica quali tabelle includere esplicitamente. Se questo metodo viene usato, verranno migrate solo le tabelle specificate.

**Parametri**
- `$tabelle`: Array contenente i nomi delle tabelle da includere.

---

### 4️⃣ **`includiView(bool $include)`**

**Descrizione**: Specifica se le viste devono essere incluse nella migrazione.

**Parametri**
- `$include`: Booleano (`true` o `false`). Se **true**, le viste saranno migrate.

---

### 5️⃣ **`forzaCopia(bool $force)`**

**Descrizione**: Se la tabella esiste nel database di destinazione, puoi scegliere se sovrascriverla o saltarla.

**Parametri**
- `$force`: Booleano (`true` o `false`). Se **true**, sovrascrive le tabelle esistenti.

---

### 6️⃣ **`migrate()`**

**Descrizione**: Avvia l'intera migrazione e stampa il resoconto alla fine.

---

### 7️⃣ **`testConnection(string $tipo)`**

**Descrizione**: Testa la connessione ai database. Può essere utilizzato per testare la connessione sia al **mittente** che al **destinazione**.

**Parametri**
- `$tipo`: Può essere `'mittente'` o `'destinazione'`.

---

### 8️⃣ **`getExcludedTables()`**

**Descrizione**: Restituisce un array contenente le tabelle che sono state escluse dalla migrazione.

---

### 9️⃣ **`getIncludedTables()`**

**Descrizione**: Restituisce un array contenente le tabelle che sono state incluse nella migrazione.

---

## 💡 **Esempi pratici**

```php
<?php

require_once 'vendor/autoload.php';

use ETSolutionsDevelopment\MariaDbMigrator\DatabaseMigrator;

$mittenteConfig = [
    'host' => '127.0.0.1',
    'user' => 'user_mittente',
    'password' => 'password_mittente',
    'database' => 'db_mittente'
];

$destinazioneConfig = [
    'host' => '127.0.0.1',
    'user' => 'user_destinazione',
    'password' => 'password_destinazione',
    'database' => 'db_destinazione',
    'port' => 3306
];

$migrator = new DatabaseMigrator($mittenteConfig, $destinazioneConfig);
$migrator->escludiTabelle(['log_attivita', 'sessioni']);
$migrator->includiTabelle(['utenti', 'ordini']);
$migrator->includiView(true);
$migrator->forzaCopia(true);
$migrator->migrate();
```

---

## 📊 **Output della console**

```
================= INIZIO MIGRAZIONE =================
 > Connessione al DB mittente riuscita.
 > Connessione al DB destinazione riuscita.
 > Inizio lavorazione tabella: utenti
   > Copio la struttura della tabella utenti
   > Copio i dati della tabella utenti
 > Lavorazione completata per la tabella utenti
 > Inizio lavorazione tabella: ordini
   > Tabella già presente, forzo la copia
 > Lavorazione completata per la tabella ordini
================= RESOCONTO MIGRAZIONE =================
 - Tabelle migrate: 2
 - Viste migrate: 1
 - Errori: 0
```

---

## ⚠️ **Errori comuni**

1. **Errore di connessione**: Controlla le credenziali nei file di configurazione.
2. **Permessi insufficienti**: Verifica i permessi del database per l'utente che esegue la migrazione.
3. **Dipendenze mancanti**: Assicurati di aver eseguito `composer install`.

---

## 📞 **Contatti**

Se hai bisogno di supporto, contattaci via:

- **Email**: [info@et-solutions-development.com](mailto:info@et-solutions-development.com)
- **GitHub Issues**: [Apri una Issue](https://github.com/ET-Solutions-Development/MariaDb-Migrator/issues)
