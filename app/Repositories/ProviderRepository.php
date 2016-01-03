<?php

namespace App\Repositories;

use App;
use DB;
use App\Models\Provider;

class ProviderRepository {

  /**
   * Create a new Provider
   *
   * @param  string  $user_id
   * @param  string  $profile_id
   * @param  string  $service_code
   * @param  array  $data
   * @return \App\Models\Provider
   */
  public function create($user_id, $profile_id, $service_code, $data) {
    $data = array_merge($data, ['user_id' => $user_id, 'profile_id' => $profile_id, 'service' => $service_code]);

    DB::collection('providers')->where('user_id', $user_id)->where('profile_id', $profile_id)->where('service', $service_code)
      ->update($data, array('upsert' => true));

    $provider = DB::collection('providers')->where('user_id', $user_id)
      ->where('profile_id', $profile_id)
      ->where('service', $service_code)
      ->first();

    return $provider;
  }

  /**
   * Reads all the Providers associated to a specific User and groups them by their Profile
   *
   * @param  \App\User  $user
   * @return array
   */
  public function getFromUserByProfile($user) {
    return $user->providers()->get()->groupBy('profile_id');
  }

  /**
   * Reads a Provider
   *
   * @param  string  $provider_id
   * @return \App\Models\Provider
   */
  public function get($provider_id) {
    return \App\Models\Provider::find($provider_id);
  }

}

?>
