<?php

namespace App\Models;

use Jenssegers\Mongodb\Model as Eloquent;

class Profile extends Eloquent {

  public function providers()
  {
    return $this->hasMany('App\Models\Provider');
  }

  public function user()
  {
    return $this->belongsTo('App\User');
  }

}
