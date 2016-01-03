<?php

namespace App\Services\Social;

use App\Repositories\ProviderRepository;
use App\Services\ISocialService;
use App\Services\SocialServiceGeneric;
use League\OAuth2\Client\Provider\Google;

use App;
use Config;

class ServiceGoogle extends SocialServiceGeneric implements ISocialService {
  /**
   * @var string Service Code
   */
  const SERVICE_CODE = 'google';

  /**
   * @var int Default token expiration time
   */
  const TOKEN_TTL = 5184000; // 60 days

  /**
   * @var Google client service
   */
  protected $client;

  /**
   * Create a new Google service
   *
   * @param  ProviderRepository  $providerRepository
   */
  function __construct(ProviderRepository $providerRepository) {
    parent::__construct($providerRepository);

    $this->client = new Google([
      'clientId' => Config::get('services.google.client_id'),
      'clientSecret' => Config::get('services.google.client_secret'),
      'redirectUri' => Config::get('services.google.redirect')
    ]);
  }

  /**
   * Scopes for using the client
   *
   * @return array
   */
  public static function scopes() {
    return ['email', 'profile'];
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

    return $this->providerRepository->create($user_id, $profile_id, ServiceGoogle::SERVICE_CODE, 
      ['token' => $this->token->getToken(), 'expires_at' => ServiceGoogle::TOKEN_TTL + time(), 'vendor_id' => $user['id'],
      'email' => $user['emails'][0]['value'], 'avatar' => $user['image']['url'], 'first_name' => $user['name']['givenName'],
      'last_name' => $user['name']['familyName']]
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
    // TODO: Implement this
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
    // TODO: Implement this
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
    // TODO: Implement this
    return true;
  }
}
