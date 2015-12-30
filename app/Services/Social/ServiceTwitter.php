<?php

namespace App\Services\Social;

use App\Services\ISocialService;
use App\Repositories\ISocialRepository;
use Abraham\TwitterOAuth\TwitterOAuth;

use Config;
use Session;

class ServiceTwitter implements ISocialService {
  const TOKEN_TTL = 5184000; // 60 days

  protected $serviceRepository;
  protected $service;

  function __construct(ISocialRepository $repository) {
    $this->serviceRepository = $repository;

    $this->service = new TwitterOAuth(Config::get('services.twitter.client_id'), Config::get('services.twitter.client_secret'));
  }

  public static function scopes() {
    return [];
  }

  protected function getRequestToken() {
    $request_token = $this->service->oauth('oauth/request_token', array('oauth_callback' => Config::get('services.twitter.redirect')));

    if ($request_token['oauth_callback_confirmed'] !== "true") {
      throw new \Exception("Callback url not confirmed");
    }

    return $request_token;
  }

  protected function getAccessToken($oauth_token, $oauth_verifier) {
    $data_token = $this->service->oauth('oauth/access_token', array('oauth_verifier' => $oauth_verifier, 'oauth_token' => $oauth_token));

    if (!array_key_exists('oauth_token', $data_token) || !array_key_exists('oauth_token_secret', $data_token)) {
      throw new \Exception("OAuth token not confirmed");
    }

    return $data_token;
  }

  protected function prepareRequest($provider_id) {
    $userProvider = $this->serviceRepository->getProvider($provider_id);

    if (empty($userProvider['oauth_token']) || empty($userProvider['oauth_token_secret'])) {
      throw new \Exception('Invalid access token');
    }

    $this->service->setOauthToken($userProvider['oauth_token'], $userProvider['oauth_token_secret']);
  }

  public function saveToken($user_id) {
    if (empty($this->request->input('oauth_token')) || empty ($this->request['oauth_verifier'])) {
      throw new Exception('Token validation failed');
    }
    $request_token = $this->request->input('oauth_token');
    $request_token_verifier = $this->request->input('oauth_verifier');

    $access_token = $this->getAccessToken($request_token, $request_token_verifier);

    $this->serviceRepository->saveToken($user_id, ['request_token' => $request_token, 'request_token_verifier' => $request_token_verifier, 
      'oauth_token' => $access_token['oauth_token'], 'oauth_token_secret' => $access_token['oauth_token_secret'], 
      'expires_at' => ServiceTwitter::TOKEN_TTL + time()]
    );

    return true;
  }

  public function getRedirectUrl() {
    $token = $this->getRequestToken();
    $redirectUrl = $this->service->url('oauth/authenticate', array('oauth_token' => $token['oauth_token']));

    return $redirectUrl;
  }

  public function handleCallback($user_id, $profile_id, $data = []) {
    if (empty($data['oauth_token']) || empty ($data['oauth_verifier'])) {
      throw new \Exception('Token validation failed');
    }
    
    $request_token = $data['oauth_token'];
    $request_token_verifier = $data['oauth_verifier'];
    $access_token = $this->getAccessToken($request_token, $request_token_verifier);

    $provider_id = $this->serviceRepository->saveToken($user_id, $profile_id, ['request_token' => $request_token, 'request_token_verifier' => $request_token_verifier, 
      'oauth_token' => $access_token['oauth_token'], 'oauth_token_secret' => $access_token['oauth_token_secret'], 
      'expires_at' => ServiceTwitter::TOKEN_TTL + time()]);

    $userInfo = $this->getUserInfo($provider_id);

    $this->serviceRepository->saveToken($user_id, $profile_id, ['vendor_id' => (string) $userInfo->id, 'name' => $userInfo->name, 'profile_image_url' => $userInfo->profile_image_url]);
  }

  public function publish($provider_id, $message) {
    $this->prepareRequest($provider_id);

    $response = $this->service->post("statuses/update", array("status" => $message));

    return $response->id_str;
  }

  public function getUserInfo($provider_id) {
    $this->prepareRequest($provider_id);

    $response = $this->service->get("account/verify_credentials", array("include_entities" => false, "skip_status" => true, "include_email" => true));

    return $response;
  }
}
