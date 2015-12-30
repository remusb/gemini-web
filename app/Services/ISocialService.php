<?php

namespace App\Services;

interface ISocialService {

  public static function scopes();
 
  public function saveToken($user_id);

  public function getRedirectUrl();

  public function handleCallback($user_id, $profile_id, $data = []);

  public function publish($provider_id, $message);

}
