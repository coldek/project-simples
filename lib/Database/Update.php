<?php



namespace App\Database;
require_once('Main.php');
use App\Debug\Debugger as Debugger;
use App\Database\Executor as Executor;
class Update implements QueryBuilder {
  public static string $table;

  public array $binds;

  public array $updates;
  public array $where;


  public function __construct ( string $table ) {
    self::$table = $table;
  }

  public function set ( array $values ) {
    $this->updates = $values;
    return $this;
  }

  public function where ( ...$wheres ) {
    //$this->where = [...$wheres];
    foreach($wheres as $where) {
      if(is_string($where)) {
        $this->where[] = $where;
      } else {
        foreach($where as $column => $value)
          $this->where[$column] = $value;
      }
    }

    return $this;
  }

  public function bind ( string $column, string $value ) {
    $this->binds[':'.$column] = $value;
    return $this;
  }

  public function build ( array $binds = null ) {
    if($binds == null)
      $binds = $this->binds;
    $query = "UPDATE `" . self::$table . "` SET ";

    /* VALUES */
    $i = 1;
    foreach($this->updates as $column => $value) {
      $query .= stringify($column, $value);
      if($i != count($this->updates)) {
        $query .= ", ";
      }
      $i++;
    }

    /* WHERE */
    if(isset($this->where)) {
      $query .= " WHERE ";
      foreach($this->where as $column => $value) {
        /* If the value is an array, which means a column has values */
        if(is_array($value)) {
          $query .= '(';

          /* Sub Array w/ seperate columns (Column1 = Value1 OR Column2 = Value2)*/
          if(isAssoc($value)) {
            foreach($value as $subColumn => $subValue)
            {
              if(is_array($subValue)) { // If the Column in this Sub array also has multiple values
                // (Column1 = Value1 AND (Column2 = Value2.1 OR Column2 = Value2.2))
                $query .= '(';
                foreach($subValue as $subSubValue)
                  $query .= stringify($subColumn, $subSubValue);
                $query .= ')';
              } else {
                $query .= stringify($subColumn, $subValue);
              }
            }
          } else {
            /* Column with multiple values Column = Value1 OR Column = Value2*/
            foreach($value as $subValue)
              $query .= stringify($column, $subValue);
          }

          $query .= ')';
        } else {
          $query .= stringify($column, $value);
        }
      }
    }
    echo $query;
    return Executor::query($query)->execute($binds);
  }
}
