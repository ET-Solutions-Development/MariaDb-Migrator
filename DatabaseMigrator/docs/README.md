# DatabaseMigrator

DatabaseMigrator è uno strumento semplice e flessibile per migrare tabelle e viste tra due database MySQL. Supporta esclusione/inclusione di tabelle, gestione delle viste e sovrascrittura condizionale.

## Funzionalità principali

- Copia struttura e dati da un database sorgente (mittente) a uno di destinazione.
- Possibilità di includere o escludere tabelle specifiche.
- Opzione per includere o escludere le viste.
- Sovrascrittura condizionale delle tabelle già esistenti nel database di destinazione.
- Resoconto dettagliato della migrazione.

## Requisiti

- PHP >= 7.4
- MySQL
- Estensione `mysqli` abilitata

## Installazione

1. Clona la repository:
   ```bash
   git clone https://github.com/tuo-utente/DatabaseMigrator.git
   Spostati nella directory del progetto:
   bash
   Copia codice
   cd DatabaseMigrator
   Assicurati che PHP e MySQL siano configurati correttamente sul tuo sistema.
   ```
