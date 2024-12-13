<?php

namespace DatabaseMigrator;

use mysqli;
use Exception;

class DatabaseMigrator
{
  private $connServerMittente;
  private $connServerDestinazione;
  private $excludeTables = [];
  private $consentTables = [];
  private $includeViews = true;
  private $forceCopy = false;
  private $totalTables;
  private $currentTableCount = 0;
  private $migrationLog = [];

  public function __construct($mittenteConfig, $destinazioneConfig)
  {
    $this->log("\n================= INIZIO MIGRAZIONE =================\n");

    try {
      $this->log("Connessione al DB mittente...");
      $this->connServerMittente = new mysqli($mittenteConfig['host'], $mittenteConfig['user'], $mittenteConfig['password'], $mittenteConfig['database']);
      if ($this->connServerMittente->connect_error) {
        throw new Exception("Errore connessione server mittente: " . $this->connServerMittente->connect_error);
      }
      $this->log("Connessione al DB mittente riuscita.");
    } catch (Exception $e) {
      die($e->getMessage());
    }

    try {
      $this->log("Connessione al DB destinazione...");
      $this->connServerDestinazione = new mysqli($destinazioneConfig['host'], $destinazioneConfig['user'], $destinazioneConfig['password'], $destinazioneConfig['database'], $destinazioneConfig['port'] ?? 3306);
      if ($this->connServerDestinazione->connect_error) {
        throw new Exception("Errore connessione server destinazione: " . $this->connServerDestinazione->connect_error);
      }
      $this->log("Connessione al DB destinazione riuscita.");
    } catch (Exception $e) {
      die($e->getMessage());
    }
  }

  public function escludiTabelle(array $tables)
  {
    $this->excludeTables = $tables;
  }

  public function includiTabelle(array $tables)
  {
    $this->consentTables = $tables;
  }

  public function includiView(bool $include)
  {
    $this->includeViews = $include;
  }

  public function forzaCopia(bool $force)
  {
    $this->forceCopy = $force;
  }

  public function migrate()
  {
    $this->log("Disabilitazione controlli foreign key...");
    $this->connServerDestinazione->query("SET foreign_key_checks = 0");

    $this->copyTables();

    if ($this->includeViews) {
      $this->copyViews();
    }

    $this->connServerDestinazione->query("SET foreign_key_checks = 1");

    $this->log("Migrazione completata.");
    $this->printSummary();

    $this->connServerMittente->close();
    $this->connServerDestinazione->close();
  }

  private function copyTables()
  {
    $result = $this->connServerMittente->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
    if ($result === false) {
      die("Errore ottenimento tabelle: " . $this->connServerMittente->error);
    }

    $tables = [];
    while ($row = $result->fetch_array()) {
      $tableName = $row[0];
      if (
        (empty($this->consentTables) && !in_array($tableName, $this->excludeTables)) ||
        (!empty($this->consentTables) && in_array($tableName, $this->consentTables))
      ) {
        $tables[] = $tableName;
      }
    }

    $this->totalTables = count($tables);
    foreach ($tables as $tableName) {
      $this->currentTableCount++;
      $this->log("\n----- Inizio lavorazione tabella: $tableName -----\n");

      if (!$this->forceCopy && $this->tableExists($tableName)) {
        $this->log("Tabella $tableName già presente. Skip.");
        continue;
      }

      $this->log("Elimino la tabella $tableName se esiste.");
      $this->connServerDestinazione->query("DROP TABLE IF EXISTS $tableName");

      $createTableResult = $this->connServerMittente->query("SHOW CREATE TABLE $tableName");
      if ($createTableResult === false) {
        $this->log("Errore ottenimento struttura tabella: $tableName");
        continue;
      }

      $createTableRow = $createTableResult->fetch_assoc();
      $createTableSQL = $createTableRow[array_keys($createTableRow)[1]];
      $this->connServerDestinazione->query($createTableSQL);

      $this->copyTableData($tableName);
    }
  }

  private function copyViews()
  {
    $result = $this->connServerMittente->query("SHOW FULL TABLES WHERE Table_type = 'VIEW'");
    while ($row = $result->fetch_array()) {
      $viewName = $row[0];
      $this->log("Copia della vista $viewName...");

      $createViewResult = $this->connServerMittente->query("SHOW CREATE VIEW $viewName");
      $createViewRow = $createViewResult->fetch_assoc();
      $createViewSQL = $createViewRow[array_keys($createViewRow)[1]];

      $this->connServerDestinazione->query("DROP VIEW IF EXISTS $viewName");
      $this->connServerDestinazione->query($createViewSQL);
    }
  }

  private function copyTableData($tableName)
  {
    $this->log("Copio i dati della tabella $tableName");
    $dataResult = $this->connServerMittente->query("SELECT * FROM $tableName");

    while ($data = $dataResult->fetch_assoc()) {
      $columns = implode(", ", array_keys($data));
      $values = array_map(function ($value) {
        return is_null($value) ? "NULL" : "'" . $this->connServerDestinazione->real_escape_string($value) . "'";
      }, array_values($data));

      $sql = "INSERT INTO $tableName ($columns) VALUES (" . implode(", ", $values) . ")";
      $this->connServerDestinazione->query($sql);
    }
  }

  private function tableExists($tableName)
  {
    $result = $this->connServerDestinazione->query("SHOW TABLES LIKE '$tableName'");
    return $result && $result->num_rows > 0;
  }

  private function log($message)
  {
    echo "  > $message\n";
    $this->migrationLog[] = $message;
  }

  private function printSummary()
  {
    echo "\n================= RESOCONTO MIGRAZIONE =================\n";
    foreach ($this->migrationLog as $logEntry) {
      echo "  - $logEntry\n";
    }
    echo "=======================================================\n";
  }
}
