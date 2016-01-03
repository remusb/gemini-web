<?php

namespace App\Repositories;

use App;
use App\Models\Profile;
use DB;

class ProfileRepository {

  /**
   * Create a new Profile
   *
   * @param  \App\User  $user
   * @param  string  $name
   * @return \App\Models\Profile
   */
  public function create($user, $name) {
    $profile = new Profile;

    $profile->name = $name;
    $profile->user_id = $user->id;

    $profile->save();

    return $profile;
  }

  /**
   * Delete an existing Profile and its associated Providers
   *
   * @param  string  $profile_id
   * @return mixed
   */
  public function delete($profile_id) {
    $profile = Profile::find($profile_id);
    $profile->providers()->delete();

    return $profile->delete();
  }

  /**
   * Reads all the Profiles associated to a specific User
   *
   * @param  \App\User  $user
   * @return array
   */
  public function getFromUser($user) {
    return $user->profiles()->get()->keyBy('id');
  }

}
