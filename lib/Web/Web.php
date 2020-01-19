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
}
