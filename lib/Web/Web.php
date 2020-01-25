<?php

namespace App\Web;

class Web {

  static $header = 'Header.php';
  static $footer = 'Footer.php';
  public static function page ( string $pageTitle, $function ) {
    include self::$header;
    $function->__invoke();
    include self::$footer;
  }

  public static function get ( &$getVar ) {
    if(empty($_GET)) {
      $getVar = false;
    } else {
      $getVar = [];
      foreach($_GET as $name => $val) {
        $getVar[$name] = $val;
      }
      $getVar = (object) $getVar;
    }

    return true;
  }
}
