<?php

namespace App\Models;

use Jenssegers\Mongodb\Model as Eloquent;

class Post extends Eloquent
{
  public function user()
  {
    return $this->belongsTo('App\User');
  }

  public function provider()
  {
    return $this->belongsTo('App\Models\Provider');
  }
}
