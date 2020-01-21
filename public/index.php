<?php
require './../include.php';

use App\Database\Main as DB;
use App\Web\Web as Web;
use App\Model\User as User;

Web::page('Home', function(){
  //var_dump(User::get(1));
  User::findOrFail(['id' => 1]);
});
