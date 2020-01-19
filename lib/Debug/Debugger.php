<?php

namespace App\Debug;

class Debugger {


  public static function array ( array $debug ) {
    echo '<pre>';
    print_r($debug);
    echo '</pre>';
  }
}
