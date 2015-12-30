<?php

namespace App\Repositories\Social;

use App\Repositories\ISocialRepository;
use DB;

class RepositoryFacebook implements ISocialRepository {
  const SERVICE_CODE = 'facebook';

  public function saveToken($user_id, $profile_id, $data) {
    $data = array_merge($data, ['user_id' => $user_id, 'profile_id' => $profile_id, 'service' => RepositoryFacebook::SERVICE_CODE]);

    DB::collection('providers')->where('user_id', $user_id)->where('profile_id', $profile_id)->where('service', RepositoryFacebook::SERVICE_CODE)
      ->update($data, array('upsert' => true));

    $provider = DB::collection('providers')->where('user_id', $user_id)->where('profile_id', $profile_id)->where('service', RepositoryFacebook::SERVICE_CODE)->first();
    return $provider['_id'];
  }

  public function getUserProvider($user_id, $profile_id) {
    return DB::collection('providers')->where('user_id', $user_id)->where('service', RepositoryFacebook::SERVICE_CODE)->where('profile_id', $profile_id)->first();
  }

  public function getProvider($provider_id) {
    return \App\Models\Provider::find($provider_id);
  }

}