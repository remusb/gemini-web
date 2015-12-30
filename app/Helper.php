<?php

namespace App;

class Helper {
  public static function set_active($route){
    return (\Request::is($route.'/*') || \Request::is($route)) ? "active" : '';
  }
}

?>
