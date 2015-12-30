<?php

namespace App\Services\Social;

use App\Services\ISocialService;
use App\Repositories\ISocialRepository;

use Socialite;
use Config;
use Session;
use App;

class ServiceFacebook implements ISocialService {
  const TOKEN_TTL = 5184000; // 60 days

  protected $serviceRepository;
  protected $service;

  function __construct(ISocialRepository $repository) {
    $this->serviceRepository = $repository;

    $this->service = new \Facebook\Facebook([
      'app_id' => Config::get('services.facebook.client_id'),
      'app_secret' => Config::get('services.facebook.client_secret'),
      'default_graph_version' => Config::get('services.facebook.default_graph_version')
    ]);
  }

  protected function prepareRequest($provider_id) {
    $userProvider = $this->serviceRepository->getProvider($provider_id);

    if (empty($userProvider['token'])) {
      throw new \Exception('Invalid access token');
    }

    $this->service->setDefaultAccessToken($userProvider['token']);
  }

  public static function scopes() {
    return ['publish_actions', 'publish_pages', 'manage_pages', 'user_posts', 'user_about_me', 'user_likes', 'user_friends'];
  }

  public function saveToken($user_id) {
    Config::set('services.facebook.redirect', Config::get('services.facebook.redirect') . '/' . $user_id);
    Session::set('state', $this->request->input('state'));

    $user = Socialite::driver('facebook')->user();

    $this->serviceRepository->saveToken($user_id, ['token' => $user->token, 'expires_at' => ServiceFacebook::TOKEN_TTL + time()]);

    return true;
  }

  public function getRedirectUrl() {
    return Socialite::driver('facebook')->scopes(ServiceFacebook::scopes())->redirect()->getTargetUrl();
  }

  public function handleCallback($user_id, $profile_id, $data = []) {
    $user = Socialite::driver('facebook')->user();

    $this->serviceRepository->saveToken($user_id, $profile_id, ['token' => $user->token, 'expires_at' => ServiceFacebook::TOKEN_TTL + time(),
      'vendor_id' => $user->id, 'email' => $user->email, 'avatar' => $user->avatar, 'first_name' => $user->user['first_name'],
      'last_name' => $user->user['last_name'], 'gender' => $user->user['gender']]);
  }

  public function publish($provider_id, $message) {
    $this->prepareRequest($provider_id);

    $response = $this->service->post('/me/feed', ['message' => $message]);
    $graphObject = $response->getDecodedBody();

    return $graphObject['id'];
  }
}
