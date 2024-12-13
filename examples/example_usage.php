<?php

require_once __DIR__ . '/../vendor/autoload.php';

use DatabaseMigrator\DatabaseMigrator;

// Configurazione del server mittente
$mittenteConfig = [
  'host' => '127.0.0.1',
  'user' => 'user_mittente',
  'password' => 'password_mittente',
  'database' => 'db_mittente',
  'port' => 3306
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
