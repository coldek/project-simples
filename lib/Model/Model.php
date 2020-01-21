<?php

namespace App\Model;


use App\Database\Main as DB;
use App\Debug\Debugger as Debugger;


abstract class Model {
  public static string $table;
  public static string $className;
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
    self::$table = strtolower($reflection->getShortName()) . "s";
    self::$className = $reflection->getNamespaceName() . "\\" . $reflection->getShortName();
  }

  public function __construct ( int $id ) {
    self::init();

    self::get($id);
  }

  public static function findOrFail ( array $where, array $binds = null) {

    self::init();
    $result = DB::select(self::$table)->where($where)->columns('id');

    $fetch = $result->build($binds)->fetch(\PDO::FETCH_ASSOC);
    return ($fetch) ? new self::$className($fetch['id']): false;
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
