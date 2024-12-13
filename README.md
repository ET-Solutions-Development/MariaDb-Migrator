# 📦 MariaDb-Migrator

[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![PHP Version](https://img.shields.io/badge/PHP-%5E7.4%20%7C%7C%20%5E8.0-blue.svg)](https://www.php.net/)
[![Tests](https://github.com/ET-Solutions-Development/MariaDb-Migrator/actions/workflows/test.yml/badge.svg)](https://github.com/ET-Solutions-Development/MariaDb-Migrator/actions/workflows/test.yml)

**MariaDb-Migrator** è una potente libreria PHP per migrare tabelle, dati e viste tra due database MariaDB o MySQL. La libreria consente di copiare la struttura e i dati delle tabelle, con la possibilità di personalizzare quali tabelle e viste copiare. Puoi anche scegliere se forzare la copia delle tabelle già esistenti.

## 🚀 **Funzionalità principali**

- ✅ Copia la struttura e i dati delle tabelle da un database **mittente** a un **destinazione**.
- ✅ Personalizzazione delle tabelle incluse ed escluse.
- ✅ Opzione per includere o escludere le **viste**.
- ✅ Possibilità di forzare la copia delle tabelle già esistenti nel database di destinazione.
- ✅ Log dettagliato con decorazioni per visualizzare i progressi e il resoconto della migrazione.

---

## 📚 **Installazione**

**1️⃣ Prerequisiti**

- PHP >= 7.4
- MySQL/MariaDB
- Estensione PHP **mysqli**

**2️⃣ Installa tramite Composer**

Esegui il seguente comando per installare la libreria MariaDb-Migrator:

```bash
composer require et-solutions-development/mariadb-migrator
```

---

## 🛠️ **Configurazione**

Crea uno script PHP, ad esempio **migrate.php**, con il seguente contenuto di esempio:

```php
<?php

require_once 'vendor/autoload.php';

use ETSolutionsDevelopment\MariaDbMigrator\DatabaseMigrator;

// Configurazione del server mittente
$mittenteConfig = [
    'host' => '127.0.0.1',
    'user' => 'user_mittente',
    'password' => 'password_mittente',
    'database' => 'db_mittente'
];

// Configurazione del server destinazione
$destinazioneConfig = [
    'host' => '127.0.0.1',
    'user' => 'user_destinazione',
    'password' => 'password_destinazione',
    'database' => 'db_destinazione',
    'port' => 3306
];

// Crea un'istanza del migratore
$migrator = new DatabaseMigrator($mittenteConfig, $destinazioneConfig);

// Configura la migrazione
$migrator->escludiTabelle(['log_attivita', 'sessioni']); // Esclude le tabelle "log_attivita" e "sessioni"
$migrator->includiTabelle(['utenti', 'ordini']); // Copia solo le tabelle "utenti" e "ordini"
$migrator->includiView(true); // Include le viste
$migrator->forzaCopia(true); // Forza la copia anche se la tabella esiste già

// Avvia la migrazione
$migrator->migrate();
```

---

## 📋 **Metodi principali**

| **Metodo**                       | **Descrizione**                                             |
| -------------------------------- | ----------------------------------------------------------- |
| `escludiTabelle(array $tabelle)` | Esclude le tabelle specificate dalla migrazione.            |
| `includiTabelle(array $tabelle)` | Migra solo le tabelle specificate (ha priorità).            |
| `includiView(bool $include)`     | Specifica se includere le viste durante la migrazione.      |
| `forzaCopia(bool $force)`        | Se **true**, forza la copia anche se la tabella esiste già. |
| `migrate()`                      | Avvia la migrazione.                                        |

---

## 📊 **Esempio di output**

Ecco come apparirà l'output nel terminale durante la migrazione:

```
================= INIZIO MIGRAZIONE =================
 > Connessione al DB mittente riuscita.
 > Connessione al DB destinazione riuscita.

----- Inizio lavorazione tabella: utenti -----
   > Copio la struttura della tabella utenti
   > Copio i dati della tabella utenti
 > Lavorazione completata per la tabella utenti

----- Inizio lavorazione tabella: ordini -----
   > Tabella già presente, forzo la copia
 > Lavorazione completata per la tabella ordini

================= RESOCONTO MIGRAZIONE =================
 - Tabelle migrate: 2
 - Viste migrate: 1
 - Errori: 0
```

---

## 🧪 **Esegui i test**

**MariaDb-Migrator** include test automatici per garantire la qualità e l'affidabilità.

1. Installa PHPUnit (se non è già installato):

```bash
composer install
```

2. Esegui i test unitari con il comando:

```bash
composer test
```

I test eseguiranno controlli su connessioni, gestione delle tabelle e comportamento generale.

---

## 📄 **Struttura della repository**

```
📁 MariaDb-Migrator/
├── 📁 src/                     # Contiene la classe principale
│   └── DatabaseMigrator.php    # La logica del migratore
├── 📁 examples/                # Esempi di utilizzo
│   └── example_usage.php       # Esempio completo di migrazione
├── 📁 tests/                   # Contiene i test PHPUnit
│   └── DatabaseMigratorTest.php # Test automatizzati
├── 📁 docs/                    # Documentazione aggiuntiva
│   └── README.md               # Documentazione principale
├── .gitignore                  # File .gitignore
├── composer.json               # File Composer
├── LICENSE                     # Licenza MIT
└── README.md                   # Documentazione principale
```

---

## 🧑‍💻 **Contribuire**

Hai trovato un bug o hai un'idea per migliorare il progetto? Contribuire è semplice:

1. **Forka la repository**
2. **Crea un branch** (`git checkout -b feature/il-tuo-branch`)
3. **Fai una pull request** per proporre la modifica

Siamo aperti a nuovi suggerimenti e miglioramenti!

---

## 📜 **Licenza**

Il codice è distribuito sotto la licenza [MIT](LICENSE), il che significa che puoi usarlo liberamente per progetti personali e commerciali.

---

## 📞 **Contatti**

Se hai bisogno di supporto o vuoi inviarci un feedback, contattaci tramite:

- **Email**: [info@etsolutions.cloud](mailto:info@etsolutions.cloud)
- **GitHub Issues**: [Apri una Issue](https://github.com/ET-Solutions-Development/MariaDb-Migrator/issues)

---

Grazie per aver utilizzato **MariaDb-Migrator**! ✨
