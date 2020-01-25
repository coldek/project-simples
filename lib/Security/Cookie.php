<?php
namespace App\Security;
class Cookie {

  public static function create () {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ=;:[]/?\\><,.!@#$%^&*()_+-`~|';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < 150; $i++) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
  }
}
