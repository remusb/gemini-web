<?php

namespace App\Models;

use Jenssegers\Mongodb\Model as Eloquent;

class Provider extends Eloquent {

  public function profile()
  {
    return $this->belongsTo('App\Models\Profile');
  }

  public function user()
  {
    return $this->belongsTo('App\User');
  }

}
