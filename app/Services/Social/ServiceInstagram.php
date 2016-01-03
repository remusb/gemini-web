<?php

namespace App\Services\Social;

use App\Repositories\ProviderRepository;
use App\Services\ISocialService;
use App\Services\SocialServiceGeneric;
use League\OAuth2\Client\Provider\Instagram;

use App;
use Config;

class ServiceInstagram extends SocialServiceGeneric implements ISocialService {
  /**
   * @var string Service Code
   */
  const SERVICE_CODE = 'instagram';

  /**
   * @var int Default token expiration time
   */
  const TOKEN_TTL = 5184000; // 60 days

  /**
   * @var Instagram client service
   */
  protected $client;

  /**
   * Create a new Instagram service
   *
   * @param  ProviderRepository  $providerRepository
   */
  function __construct(ProviderRepository $providerRepository) {
    parent::__construct($providerRepository);

    $this->client = new Instagram([
      'clientId' => Config::get('services.instagram.client_id'),
      'clientSecret' => Config::get('services.instagram.client_secret'),
      'redirectUri' => Config::get('services.instagram.redirect')
    ]);
  }

  /**
   * Scopes for using the client
   *
   * @return array
   */
  public static function scopes() {
    return Config::get('services.instagram.scopes');
  }

  /**
   * Process data returned as callback from the client
   *
   * @param  string  $user_id
   * @param  string  $profile_id
   * @param  array  $data
   * @return \App\Models\Provider
   */
  public function handleCallback($user_id, $profile_id, $data = []) {
    parent::handleCallback($user_id, $profile_id, $data);

    $user = $this->client->getResourceOwner($this->token)->toArray();

    return $this->providerRepository->create($user_id, $profile_id, ServiceInstagram::SERVICE_CODE,
      ['token' => $this->token->getToken(), 'expires_at' => ServiceInstagram::TOKEN_TTL + time(), 'vendor_id' => $user['id'],
        'username' => $user['username'], 'avatar' => $user['profile_picture'], 'name' => $user['full_name']]
    );
  }

  /**
   * Post a message on the client
   *
   * @param  string  $provider_id
   * @param  array  $params
   * @param  array  $opts = []
   * @return mixed
   * @throws \Exception
   */
  public function post($provider_id, $params, $opts = []) {
    return true;
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
    return true;
  }

  /**
   * Publish a link message to the client
   *
   * @param  string  $provider_id
   * @param  string  $link
   * @param  array  $opts = []
   * @return mixed
   */
  public function postLink($provider_id, $link, $opts = []) {
    return true;
  }
}
