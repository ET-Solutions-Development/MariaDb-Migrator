<?php

namespace DatabaseMigrator\Tests;;

use PHPUnit\Framework\TestCase;
use DatabaseMigrator\DatabaseMigrator;

class DatabaseMigratorTest extends TestCase
{
  public function testConnectionToMittenteDatabase()
  {
    $mittenteConfig = [
      'host' => '127.0.0.1',
      'user' => 'root',
      'password' => '',
      'database' => 'test_mittente'
    ];

    $destinazioneConfig = [
      'host' => '127.0.0.1',
      'user' => 'root',
      'password' => '',
      'database' => 'test_destinazione',
      'port' => 3306
    ];

    $migrator = new DatabaseMigrator($mittenteConfig, $destinazioneConfig);

    // Controlla se la connessione al server mittente è valida
    $this->assertTrue($migrator->testConnection('mittente'));
  }

  public function testConnectionToDestinazioneDatabase()
  {
    $mittenteConfig = [
      'host' => '127.0.0.1',
      'user' => 'root',
      'password' => '',
      'database' => 'test_mittente'
    ];

    $destinazioneConfig = [
      'host' => '127.0.0.1',
      'user' => 'root',
      'password' => '',
      'database' => 'test_destinazione',
      'port' => 3306
    ];

    $migrator = new DatabaseMigrator($mittenteConfig, $destinazioneConfig);

    // Controlla se la connessione al server destinazione è valida
    $this->assertTrue($migrator->testConnection('destinazione'));
  }

  public function testExcludeTables()
  {
    $migrator = $this->createMigratorInstance();
    $migrator->escludiTabelle(['log_attivita', 'sessioni']);

    // Verifica che le tabelle siano state correttamente escluse
    $this->assertTrue(in_array('log_attivita', $migrator->getExcludedTables()));
    $this->assertTrue(in_array('sessioni', $migrator->getExcludedTables()));
  }

  public function testIncludeTables()
  {
    $migrator = $this->createMigratorInstance();
    $migrator->includiTabelle(['utenti', 'ordini']);

    // Verifica che le tabelle siano state correttamente incluse
    $this->assertTrue(in_array('utenti', $migrator->getIncludedTables()));
    $this->assertTrue(in_array('ordini', $migrator->getIncludedTables()));
  }

  private function createMigratorInstance()
  {
    $mittenteConfig = [
      'host' => '127.0.0.1',
      'user' => 'root',
      'password' => '',
      'database' => 'test_mittente'
    ];

    $destinazioneConfig = [
      'host' => '127.0.0.1',
      'user' => 'root',
      'password' => '',
      'database' => 'test_destinazione',
      'port' => 3306
    ];

    return new DatabaseMigrator($mittenteConfig, $destinazioneConfig);
  }
}
