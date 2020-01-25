<?php
namespace App\Web;
session_start();
use App\Model\User as User;
use App\Security\Cookie as Cookie;
class Session {


  public function __construct ( int $id, bool $remember ) {
    if(User::findOrFail(['id' => $id], $user) && !self::isLoggedIn()) {
      $time = time();
      $time += ($remember == false) ? 43200: 7884000;
      $user->cookie = Cookie::create();
      setcookie('user', $user->cookie, $time);
      $user->save();
    }
  }

  public static function isLoggedIn () {
    if(isset($_COOKIE['user']) && User::findByCookie($_COOKIE['user'], $user)) {
      $return = true;
    } else { // User doesn't even have a damn cookie!
      $return = false;
    }
    // Add some additional stuff later on
    return $return;
  }

  public static function logOut () {
    if(isset($_COOKIE['user'])) {
      setcookie('user', '', 1);
    }
    return true;
  }

  public static function getUser (): User {
    if(self::isLoggedIn() && User::findByCookie($_COOKIE['user'], $user)) {
      $return = $user;
    } else {
      $return = false;
    }

    return $return;
  }
}
