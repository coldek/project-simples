<?php
namespace App\Database;

require_once('Main.php');

use App\Debug\Debugger as Debugger;
use App\Database\Main as DB;



class Select implements QueryBuilder {
  public static string $table; // Table Name
  public array $columns; // Array of columns to fetch
  public array $where; // Array of where values

  public array $binds; // Array of binds (Optional if pulling a one-liner) [':bind' => 'value']

  public string $join; // Join Type (False if not joining tables)
  public string $joinTable; // Join on table (False if not joining tables)
  public array $on; // Join on value (False if not joining tables)

  public array $orderBy; // Order $this->orderBy = ['Column', 'ASC']
  public array $limit; // Limit of SQL Query $this->limit = [0, 30]



  public function __construct ( string $table, $all = false ) {
    self::$table = $table;
    if($all) {
      if(is_bool($all) && $all == true) {
        // Select all columns
        $this->columns = [true];
      } elseif(isset(DB::$selectIdentifiers[$all])) {
        $this->columns = [DB::$selectIdentifiers[$all]];
      }
    }
  }


  /* ONE LINER FUNCTIONS */
  // Shortcut for inserting columns into the array
  public function columns ( string ...$columns ) {
    $this->columns = [...$columns];

    return $this;
  }

  // Fill out the join table when needed
  public function join ( string $joinType, string $joinTable, string $column, string $value ) {
    $this->join = $joinType;
    $this->joinTable = $joinTable;
    $this->on = [$column, $value];

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

  public function orderBy ( string $column, string $by ) {
    $this->orderBy = [$column, $by];

    return $this;
  }

  public function bind ( string $column, string $value ) {
    $this->binds[':'.$column] = $value;
    return $this;
  }

  public function limit ( int $starting, int $ending ) {
    $this->limit = [$starting, $ending];

    return $this;
  }

  public function paginate ( int $page, int $displayAmount ) {
    if($page == 0) {
      $this->limit(0, $displayAmount);
    } else {
      $this->limit($page * $displayAmount + 1, $displayAmount);
    }

    return $this;
  }

  public function build( $binds = null ) {
    if($binds == null && isset($this->binds))
      $binds = $this->binds;
    $query = "SELECT ";

    /* COLUMNS */
    if($this->columns[0] === true) {
      $query .= "* ";
    } elseif(count($this->columns) == 1 && !isset(DB::$selectIdentifiers[$this->columns[0]])) {
      $query .= "`".$this->columns[0]."` ";
    } elseif(isset(DB::$selectIdentifiers[$this->columns[0]])) {
      $query .= DB::$selectIdentifiers[$this->columns[0]] . " ";
    } else {
      $query .= "`" . implode("`, `", $this->columns) . "` ";
    }

    $query .= "FROM `" . self::$table . "` ";
    /* JOIN */

    if(isset($this->join) && isset(DB::$onTypes[$this->join])) { // If the join type exists
      $query .= " ".DB::$onTypes[$this->join]." `" . $this->joinTable . "` ON " . stringify($this->on[0], $this->on[1]);
    }

    /* WHERE */
    if(isset($this->where)) {
      $query .= "WHERE ";
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

    /* ORDER BY */
    if(isset($this->orderBy))
    {
      $column = $this->orderBy[0];
      $by = $this->orderBy[1];

      if(strpos($column, '.'))
      {
        $columnArray = explode('.', $column);
        $column = "`".$columnArray[0]."`.`".$columnArray[1]."` ";
      } else {
        $column = "`".$column."` ";
      }
      $query .= "ORDER BY " . $column . " " . $by . " ";
    }


    /* LIMIT */
    if(isset($this->limit))
    {
      $query .= "LIMIT " . $this->limit[0].", ".$this->limit[1];
    }


    /* Execute */

    return Executor::query($query)->execute($binds);

    //return $query;
  }
}
