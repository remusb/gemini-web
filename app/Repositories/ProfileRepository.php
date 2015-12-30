<?php

namespace App\Repositories;

use DB;
use App\Models\Profile;

class ProfileRepository {

  public function addProfile($user_id, $name) {
    $profile = new Profile;

    $profile->name = $name;
    $profile->user_id = $user_id;

    $profile->save();
  }

  public function getForUser($user_id) {
    return DB::collection('profiles')->where('user_id', $user_id)->get();
  }

  public function getProfile($profile_id) {
    return Profile::find($profile_id);
  }

  public function deleteProfile($profile_id) {
    $profile = Profile::find($profile_id);
    $profile->providers()->delete();

    return $profile->delete();
  }

}
