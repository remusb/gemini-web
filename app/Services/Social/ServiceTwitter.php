<?php

namespace App\Services\Social;

use App\Services\ISocialService;
use App\Repositories\ProviderRepository;
use Abraham\TwitterOAuth\TwitterOAuth;

use Config;

class ServiceTwitter implements ISocialService {
  const SERVICE_CODE = 'twitter';
  const TOKEN_TTL = 5184000; // 60 days

  /**
   * The Provider Repository
   *
   * @var \App\Repositories\ProviderRepository
   */
  protected $providerRepository;

  /**
   * @var TwitterOAuth
   */
  protected $client;

  /**
   * Create a new Twitter service
   *
   * @param  ProviderRepository  $providerRepository
   */
  function __construct(ProviderRepository $providerRepository) {
    $this->providerRepository = $providerRepository;

    $this->client = new TwitterOAuth(Config::get('services.twitter.client_id'), Config::get('services.twitter.client_secret'));
  }

  /**
   * Scopes for using the client
   *
   * @return array
   */
  public static function scopes() {
    return [];
  }

  /**
   * Get the OAuth 2 state
   *
   * @return string
   */
  public function getState() {
    return '';
  }

  /**
   * Generate a request token from Twitter OAuth
   *
   * @return string
   * @throws \Exception
   */
  protected function getRequestToken() {
    $request_token = $this->client->oauth('oauth/request_token', array('oauth_callback' => Config::get('services.twitter.redirect')));

    if ($request_token['oauth_callback_confirmed'] !== "true") {
      throw new \Exception("Callback url not confirmed");
    }

    return $request_token;
  }

  /**
   * Generate an access token (oauth token and oauth secret) from Twitter OAuth
   *
   * @param string $oauth_token
   * @param string $oauth_verifier
   * @return array
   * @throws \Exception
   */
  protected function getAccessToken($oauth_token, $oauth_verifier) {
    $data_token = $this->client->oauth('oauth/access_token', array('oauth_verifier' => $oauth_verifier, 'oauth_token' => $oauth_token));

    if (!array_key_exists('oauth_token', $data_token) || !array_key_exists('oauth_token_secret', $data_token)) {
      throw new \Exception("OAuth token not confirmed");
    }

    return $data_token;
  }

  /**
   * Prepare the Twitter client before sending a request
   * Reads the oauth token and secret from the Provider for Twitter authentication
   *
   * @param  string  $provider_id
   * @throws \Exception
   */
  protected function prepareRequest($provider_id) {
    $userProvider = $this->providerRepository->get($provider_id);

    if (empty($userProvider['oauth_token']) || empty($userProvider['oauth_token_secret'])) {
      throw new \Exception('Invalid access token');
    }

    $this->client->setOauthToken($userProvider['oauth_token'], $userProvider['oauth_token_secret']);
  }

  /**
   * Build and return the client URL for authentication
   *
   * @return string
   */
  public function getRedirectUrl() {
    $token = $this->getRequestToken();
    $redirectUrl = $this->client->url('oauth/authenticate', array('oauth_token' => $token['oauth_token']));

    return $redirectUrl;
  }

  /**
   * Process data returned as callback from the client
   *
   * @param  string  $user_id
   * @param  string  $profile_id
   * @param  array  $data = []
   * @return \App\Models\Provider
   * @throws \Exception
   */
  public function handleCallback($user_id, $profile_id, $data = []) {
    if (empty($data['oauth_token']) || empty ($data['oauth_verifier'])) {
      throw new \Exception('Token validation failed');
    }
    
    $request_token = $data['oauth_token'];
    $request_token_verifier = $data['oauth_verifier'];
    $access_token = $this->getAccessToken($request_token, $request_token_verifier);

    $provider = $this->providerRepository->create($user_id, $profile_id, ServiceTwitter::SERVICE_CODE, 
      ['request_token' => $request_token, 'request_token_verifier' => $request_token_verifier, 
      'oauth_token' => $access_token['oauth_token'], 'oauth_token_secret' => $access_token['oauth_token_secret'], 
      'expires_at' => ServiceTwitter::TOKEN_TTL + time()]
    );

    $userInfo = $this->getUserInfo($provider['_id']);

    return $this->providerRepository->create($user_id, $profile_id, ServiceTwitter::SERVICE_CODE, 
      ['vendor_id' => (string) $userInfo->id, 'name' => $userInfo->name, 'profile_image_url' => $userInfo->profile_image_url]
    );
  }

  /**
   * Publish a message to the client
   *
   * @param  string  $provider_id
   * @param  array  $params
   * @param  array  $opts = []
   * @return mixed
   */
  public function post($provider_id, $params, $opts = []) {
    $this->prepareRequest($provider_id);

    $response = $this->client->post("statuses/update", $params);

    return $response->id_str;
  }

  /**
   * Publish a text message to the client
   *
   * @param  string  $provider_id
   * @param  string  $message
   * @param  array  $opts = []
   * @return mixed
   */
  public function postMessage($provider_id, $message, $opts = []) {
    $params = [ 'status' => $message ];

    return $this->post($provider_id, $params, $opts);
  }

  /**
   * Publish a link to the client
   *
   * @param  string  $provider_id
   * @param  string  $link
   * @param  array  $opts = []
   * @return mixed
   */
  public function postLink($provider_id, $link, $opts = []) {
    $params = [ 'status' => $link ];

    return $this->post($provider_id, $params, $opts);
  }

  /**
   * Publish an image to the client
   *
   * @param  string  $provider_id
   * @param  string  $image
   * @param  array  $opts = []
   * @return mixed
   */
  public function postImage($provider_id, $image, $opts = []) {
    return true;
  }

  /**
   * Publish a video to the client
   *
   * @param  string  $provider_id
   * @param  string  $video
   * @param  array  $opts = []
   * @return mixed
   */
  public function postVideo($provider_id, $video, $opts = []) {
    return true;
  }

  public function getUserInfo($provider_id) {
    $this->prepareRequest($provider_id);

    $response = $this->client->get("account/verify_credentials", array("include_entities" => false, "skip_status" => true, "include_email" => true));

    return $response;
  }
}
