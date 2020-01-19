<h1>Nothing</h1>
<?php
require './../include.php';

use App\Database\Main as DB;
use App\Debug\Debugger as Debugger;
use App\Database\Insert as Insert;
use App\Database\Update as Update;
use App\Database\Select as Select;
use App\Model\Test as Test;
use App\Web\Web as Web;

Web::page('Home', function(){
  echo 'test';
});
