<?php
require './../include.php';

use App\Database\Main as DB;
use App\Web\Web as Web;
use App\Model\User as User;
use App\Debug\Debugger as D;
use App\Web\Session as Sess;
use App\Security\Cookie as Cookie;
Web::page('Home', function(){
  Web::get($get);
  //echo htmlentities(Cookie::create());
  new Sess(1, false);
  var_dump(Sess::isLoggedIn());
  //var_dump(Sess::getUser()->name);
});
