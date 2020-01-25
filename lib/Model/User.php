<?php
namespace App\Model;
use App\Web\Session as Sess;
class User extends Model {


  public static function findByCookie ( string $cookie, &$var ) {
    // Find by cookie. If it is found, set the $var to whatever it finds.
    if( self::findOrFail(
      ['cookie' => ':cookie'], // Bind
      $var, // Variable
      [':cookie' => $cookie] // Binds
    )) {
      $return = true;
    } else { // if a user isn't found with the requested cookie
      Sess::logOut(); // Log out if it can't find it.

      $return = false;
    }

    return $return;
  }
}
