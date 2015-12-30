<?php

namespace App\Services;

use App;

class SocialServiceFactory {
  private $provider = [];

  public static function get($service) {
    $serviceClass = 'Service' . ucfirst($service);
    return App::make($serviceClass);
  }
}

?>
