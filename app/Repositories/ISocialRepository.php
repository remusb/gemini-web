<?php

namespace App\Repositories;

interface ISocialRepository {
  public function saveToken($user_id, $profile_id, $data);

  public function getUserProvider($user_id, $profile_id);

  public function getProvider($provider_id);
}
