<?php
namespace App\Database;

use App\Debug\Debugger as Debugger;

interface QueryBuilder {
  public function build();
}

/* MAIN CLASS, USED FOR EASE OF INITILIAZATION & ONE LINERS */
class Main {
  public static array $types = ['o' => 'OR', 'a' => 'AND'];
  public static array $onTypes = ['full' => "FULL OUTER JOIN", 'right' => "RIGHT JOIN", 'left' => 'LEFT JOIN', 'inner' => "INNER JOIN"];


  public static array $selectIdentifiers = ['#getCount' => 'Count(*)', '#getDistinct' => 'DISTINCT'];
  /* Database Connection Info */
  public const ADDRESS = '127.0.0.1';
  public const DATABASE = 'Vertineer';
  public const USERNAME = 'root';
  public const PASSWORD = '';


  public static function select( string $table, $all = false ) {
    return new Select($table, $all);
  }

  public static function update( string $table ) {
    return new Update($table);
  }

  public function insert ( string $table ) {
    return new Insert($table);
  }
}


/* EXECUTOR: MEANT FOR EXECUTION OF QUERIES */
class Executor {


  public static \PDO $pdo;
  public static string $queryString;

  public array $binds;
  public $query;

  public static function query ( string $query ) {
    self::$pdo = new \PDO('mysql:dbname='.Main::DATABASE.';host='.Main::ADDRESS, Main::USERNAME, Main::PASSWORD);
    self::$queryString = $query;
    return new static;
  }

  public function execute ( $binds = null ) {
    $this->query = self::$pdo->prepare(self::$queryString);
    $this->query->execute($binds);
    return $this->query;
  }
}

// This removes repition in backend.
// stringify('Column1', 'Value1') -> '`Column1` = "Value1"'
// stringiy('Table.Column1', 'Value1') -> '`Table`.`Column1` = "Value1"'
function stringify ($column, $value) {
  $query = "";
  if(isset(Main::$types[$value])) {
    $query .= " ".Main::$types[$value]." ";
  } else {

    if(strpos($column, '.'))
    {
      //ArrayDebugger::debug(explode('.', $column));
      $columnArray = explode('.', $column);
      $query .= "`".$columnArray[0]."`.`".$columnArray[1]."` ";
    } else {
      $query .= "`".$column."` ";
    }

    $query .= " = " . $value;
  }
  return $query;
}


// If it is an associative array
function isAssoc(array $arr) {
    if (array() === $arr) return false;
    return array_keys($arr) !== range(0, count($arr) - 1);
}
