<?php


namespace App\Database;
require_once('Main.php');
use App\Database\Executor as Executor;



class Insert {
  public static string $table;

  public array $binds;
  public array $values;

  public function __construct ( string $table ) {
    self::$table = $table;
  }

  public function bind ( string $column, string $value ) {
    $this->binds[':'.$column] = $value;
    return $this;
  }

  public function build ( array $binds = null ) {
    if($binds == null)
      $binds = $this->binds;

    $query = "INSERT INTO `".self::$table."` (`".implode('`, `', array_keys($this->values))."`) VALUES (".implode(', ', array_values($this->values)).")";

    Executor::query($query)->execute($binds);
  }
}
