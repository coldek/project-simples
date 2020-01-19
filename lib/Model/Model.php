<?php

namespace App\Model;


use App\Database\Main as DB;
use App\Debug\Debugger as Debugger;


abstract class Model {
  public static string $table;
  public array $row;
  public static bool $exists;

  public function get ( int $id ) {
    $row = DB::select(self::$table, true)->where(['id' => $id])->build()->fetch(\PDO::FETCH_ASSOC);
    if($row) {
      self::$exists = true;
      $this->row = $row;
      foreach($row as $column => $value) {
        $this->{$column} = $value;
      }

    } else {
      self::$exists = false;
    }

  }

  public static function init () {
    $reflection = new \ReflectionClass(get_called_class());
    self::$table = $reflection->getShortName();
  }

  public function __construct ( int $id ) {
    self::init();

    self::get($id);
  }

  public static function findOrFail ( array $where, array $binds = null, string ...$columns) {

    self::init();
    $result = DB::select(self::$table)->where($where);

    if(empty($columns))
      $result->columns = [true];
    else
      $result->columns(...$columns);
      $fetch = $result->build();
    return ($fetch) ? (int) $fetch->fetch(\PDO::FETCH_ASSOC): false;
  }

  public function save () {
    $changed = [];
    $binds = [];
    foreach($this->row as $column => $value) {
      if($value != $this->{$column}) {
        $changed[$column] = ':' . $column;
        $binds[':' . $column] = $this->{$column};
      }
    }
    Debugger::array($binds);
    if(!empty($changed))
      DB::update(self::$table)->set($changed)->where(['id' => $this->id])->build($binds);
  }
}
