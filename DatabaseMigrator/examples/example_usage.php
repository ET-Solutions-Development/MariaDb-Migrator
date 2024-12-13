<?php
require_once 'src/DatabaseMigrator.php';

// Configurazione per il server mittente
$mittenteConfig = [
  'host' => 'mittente_host',
  'user' => 'mittente_user',
  'password' => 'mittente_password',
  'database' => 'mittente_database',
  'port' => 3306
];

// Configurazione per il server destinazione
$destinazioneConfig = [
  'host' => 'destinazione_host',
  'user' => 'destinazione_user',
  'password' => 'destinazione_password',
  'database' => 'destinazione_database',
  'port' => 3306
];

// Creazione di un'istanza del migratore
$migrator = new DatabaseMigrator($mittenteConfig, $destinazioneConfig);

// Configurazione personalizzata
$migrator->escludiTabelle(['log_attivita', 'sessioni']);
$migrator->includiTabelle(['utenti', 'ordini']);
$migrator->includiView(true);
$migrator->forzaCopia(true);

// Avvia la migrazione
$migrator->migrate();
