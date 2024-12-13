<?php

namespace DatabaseMigrator;

use PDO;
use PDOException;
use Exception;

class DatabaseMigrator
{
  const TABLE_TYPE_BASE = 'BASE TABLE';
  const TABLE_TYPE_VIEW = 'VIEW';

  private PDO $connServerMittente;
  private PDO $connServerDestinazione;
  private array $excludeTables = [];
  private array $consentTables = [];
  private bool $includeViews = true;
  private bool $forceCopy = false;
  private int $totalTables = 0;
  private int $currentTableCount = 0;
  private array $migrationLog = [];

  public function __construct(array $mittenteConfig, array $destinazioneConfig)
  {
    $this->log("\n================= INIZIO MIGRAZIONE =================\n");

    try {
      $this->log("Connessione al DB mittente...");
      $this->connServerMittente = new PDO(
        'mysql:host=' . $mittenteConfig['host'] . ';dbname=' . $mittenteConfig['database'],
        $mittenteConfig['user'],
        $mittenteConfig['password']
      );
      $this->connServerMittente->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $this->log("Connessione al DB mittente riuscita.");
    } catch (PDOException $e) {
      die("Errore connessione server mittente: " . $e->getMessage());
    }

    try {
      $this->log("Connessione al DB destinazione...");
      $this->connServerDestinazione = new PDO(
        'mysql:host=' . $destinazioneConfig['host'] . ';dbname=' . $destinazioneConfig['database'],
        $destinazioneConfig['user'],
        $destinazioneConfig['password']
      );
      $this->connServerDestinazione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $this->log("Connessione al DB destinazione riuscita.");
    } catch (PDOException $e) {
      die("Errore connessione server destinazione: " . $e->getMessage());
    }
  }

  public function escludiTabelle(array $tables): void
  {
    $this->excludeTables = $tables;
  }

  public function includiTabelle(array $tables): void
  {
    $this->consentTables = $tables;
  }

  public function includiView(bool $include): void
  {
    $this->includeViews = $include;
  }

  public function forzaCopia(bool $force): void
  {
    $this->forceCopy = $force;
  }

  public function migrate(): void
  {
    $this->log("Disabilitazione controlli foreign key...");
    $this->connServerDestinazione->exec("SET foreign_key_checks = 0");

    $this->copyTables();

    if ($this->includeViews) {
      $this->copyViews();
    }

    $this->connServerDestinazione->exec("SET foreign_key_checks = 1");

    $this->log("Migrazione completata.");
    $this->printSummary();

    $this->connServerMittente = null;
    $this->connServerDestinazione = null;
  }

  private function copyTables(): void
  {
    $stmt = $this->connServerMittente->query("SHOW FULL TABLES WHERE Table_type = '" . self::TABLE_TYPE_BASE . "'");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

    $tablesToCopy = array_filter($tables, function ($tableName) {
      return (empty($this->consentTables) && !in_array($tableName, $this->excludeTables))
        || (!empty($this->consentTables) && in_array($tableName, $this->consentTables));
    });

    $this->totalTables = count($tablesToCopy);

    foreach ($tablesToCopy as $tableName) {
      $this->currentTableCount++;
      $this->log("\n----- Inizio lavorazione tabella: $tableName -----\n");

      if (!$this->forceCopy && $this->tableExists($tableName)) {
        $this->log("Tabella $tableName già presente. Skip.");
        continue;
      }

      $this->log("Elimino la tabella $tableName se esiste.");
      $this->connServerDestinazione->exec("DROP TABLE IF EXISTS $tableName");

      $createTableSQL = $this->connServerMittente->query("SHOW CREATE TABLE $tableName")->fetch(PDO::FETCH_ASSOC)['Create Table'];
      $this->connServerDestinazione->exec($createTableSQL);

      $this->copyTableData($tableName);
    }
  }

  private function copyViews(): void
  {
    $stmt = $this->connServerMittente->query("SHOW FULL TABLES WHERE Table_type = '" . self::TABLE_TYPE_VIEW . "'");
    $views = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

    foreach ($views as $viewName) {
      $this->log("Copia della vista $viewName...");

      $createViewSQL = $this->connServerMittente->query("SHOW CREATE VIEW $viewName")->fetch(PDO::FETCH_ASSOC)['Create View'];
      $this->connServerDestinazione->exec("DROP VIEW IF EXISTS $viewName");
      $this->connServerDestinazione->exec($createViewSQL);
    }
  }

  private function copyTableData(string $tableName): void
  {
    $this->log("Copio i dati della tabella $tableName");

    $stmt = $this->connServerMittente->query("SELECT * FROM $tableName");
    $batchData = [];
    $batchSize = 100;

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $batchData[] = "('" . implode("', '", array_map([$this->connServerDestinazione, 'quote'], $row)) . "')";
      if (count($batchData) >= $batchSize) {
        $this->insertBatch($tableName, $batchData);
        $batchData = [];
      }
    }

    if (!empty($batchData)) {
      $this->insertBatch($tableName, $batchData);
    }
  }

  private function insertBatch(string $tableName, array $batchData): void
  {
    $columns = implode(", ", array_keys($this->connServerMittente->query("SELECT * FROM $tableName LIMIT 1")->fetch(PDO::FETCH_ASSOC)));
    $sql = "INSERT INTO $tableName ($columns) VALUES " . implode(", ", $batchData);
    $this->connServerDestinazione->exec($sql);
  }

  private function tableExists(string $tableName): bool
  {
    $stmt = $this->connServerDestinazione->query("SHOW TABLES LIKE '$tableName'");
    return $stmt && $stmt->rowCount() > 0;
  }

  private function log(string $message): void
  {
    $logMessage = "[" . date('Y-m-d H:i:s') . "] " . $message . "\n";
    echo $logMessage;
    file_put_contents('migration.log', $logMessage, FILE_APPEND);
    $this->migrationLog[] = $message;
  }

  private function printSummary(): void
  {
    $totalTablesCopied = count(array_filter($this->migrationLog, fn($log) => str_contains($log, 'Copio i dati della tabella')));
    $totalViewsCopied = count(array_filter($this->migrationLog, fn($log) => str_contains($log, 'Copia della vista')));

    echo "\n================= RESOCONTO MIGRAZIONE =================\n";
    echo "Totale tabelle copiate: $totalTablesCopied\n";
    echo "Totale viste copiate: $totalViewsCopied\n";

    foreach ($this->migrationLog as $logEntry) {
      echo "  - $logEntry\n";
    }
    echo "=======================================================\n";
  }
}
