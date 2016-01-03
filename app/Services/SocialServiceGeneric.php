<?php

namespace App\Services;

use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Token\AccessToken;
use App\Repositories\ProviderRepository;

abstract class SocialServiceGeneric implements ISocialService {
  const SERVICE_CODE = null;
  const TOKEN_TTL = 5184000; // 60 days

  /**
   * The Provider Repository
   *
   * @var ProviderRepository
   */
  protected $providerRepository;

  /**
   * The client token
   *
   * @var string|AccessToken;
   */
  protected $token;

  /**
   * @var GenericProvider
   */
  protected $client;

  /**
   * Create a new OAuth 2 service
   *
   * @param  ProviderRepository  $providerRepository
   */
  function __construct(ProviderRepository $providerRepository) {
    $this->providerRepository = $providerRepository;

    $this->client = null;
  }

  /**
   * Prepare the OAuth client before sending a request
   *
   * @param  string  $provider_id
   * @throws \Exception
   */
  protected function prepareRequest($provider_id) {
    $userProvider = $this->providerRepository->get($provider_id);

    if (empty($userProvider['token'])) {
      throw new \Exception('Invalid access token');
    }

    $this->token = $userProvider['token'];
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
   * Build and return the client URL for authentication
   *
   * @return string
   */
  public function getRedirectUrl() {
    return $this->client->getAuthorizationUrl();
  }

  /**
   * Get the OAuth 2 state
   *
   * @return string
   */
  public function getState() {
    return $this->client->getState();
  }

  /**
   * Process data returned as callback from the client
   *
   * @param  string  $user_id
   * @param  string  $profile_id
   * @param  array  $data
   * @return \App\Models\Provider
   * @throws \Exception
   */
  public function handleCallback($user_id, $profile_id, $data = []) {
    if (empty($data['state']) || ($data['state'] !== $data['local_state'])) {
      throw new \Exception('Invalid state');
    }

    // Try to get an access token using the authorization code grant.
    $this->token = $this->client->getAccessToken('authorization_code', [
      'code' => $data['code']
    ]);
  }

  /**
   * Publish a message to the client
   *
   * @param  string  $provider_id
   * @param  array  $params
   * @param  array  $opts = []
   * @return mixed
   */
  abstract public function post($provider_id, $params, $opts = []);

  /**
   * Publish a text message to the client
   *
   * @param  string  $provider_id
   * @param  string  $message
   * @param  array  $opts = []
   * @return mixed
   */
  abstract public function postMessage($provider_id, $message, $opts = []);

  /**
   * Publish a link to the client
   *
   * @param  string  $provider_id
   * @param  string  $link
   * @param  array  $opts = []
   * @return mixed
   */
  abstract public function postLink($provider_id, $link, $opts = []);

  /**
   * Publish an image to the client. By default this is a stub to allow clients which don't support image posting
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
   * Publish a video to the client. By default this is a stub to allow clients which don't support image posting
   *
   * @param  string  $provider_id
   * @param  string  $video
   * @param  array  $opts = []
   * @return mixed
   */
  public function postVideo($provider_id, $video, $opts = []) {
    return true;
  }
}
