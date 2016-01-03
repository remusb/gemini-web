<?php

namespace App\Services\Social;

use App\Repositories\ProviderRepository;
use App\Services\ISocialService;
use App\Services\SocialServiceGeneric;
use League\OAuth2\Client\Provider\LinkedIn;

use App;
use Config;

class ServiceLinkedin extends SocialServiceGeneric implements ISocialService {
  /**
   * @var string Service Code
   */
  const SERVICE_CODE = 'linkedin';

  /**
   * @var int Default token expiration time
   */
  const TOKEN_TTL = 5184000; // 60 days

  /**
   * @var LinkedIn client service
   */
  protected $client;

  /**
   * Create a new LinkedIn service
   *
   * @param  ProviderRepository  $providerRepository
   */
  function __construct(ProviderRepository $providerRepository) {
    parent::__construct($providerRepository);

    $this->client = new LinkedIn([
      'clientId'          => Config::get('services.linkedin.client_id'),
      'clientSecret'      => Config::get('services.linkedin.client_secret'),
      'redirectUri'       => Config::get('services.linkedin.redirect')
    ]);
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

    return $this->providerRepository->create($user_id, $profile_id, ServiceLinkedin::SERVICE_CODE, 
      ['token' => $this->token->getToken(), 'expires_at' => $this->token->getExpires(), 'vendor_id' => $user['id'],
      'email' => $user['emailAddress'], 'avatar' => $user['pictureUrl'], 'first_name' => $user['firstName'],
      'last_name' => $user['lastName']]
    );
  }

  /**
   * Publish a message to the client
   *
   * @param  string  $provider_id
   * @param  array  $params
   * @param  array  $opts = []
   * @return mixed
   * @throws \Exception
   */
  public function post($provider_id, $params, $opts = []) {
    $this->prepareRequest($provider_id);

    $body = array_merge($params, [
      'visibility' => [
        'code' => 'anyone'
      ]
    ]);

    $options['body'] = json_encode($body);
    $options['headers']['content-type'] = 'application/json';
    $options['headers']['x-li-format'] = 'json';

    $request = $this->client->getAuthenticatedRequest(
      'POST',
      'https://api.linkedin.com/v1/people/~/shares?format=json',
      $this->token,
      $options
    );

    $response = $this->client->getResponse($request);
    if (empty($response['updateKey']) || empty($response['updateUrl'])) {
      throw new \Exception('We encountered an issue');
    }

    return $response;
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
    $params = [
      'content' => [
        'title' => $opts['title'],
        'description' => $message,
        'submitted-url' => empty($opts['submitted-url']) ? '' : $opts['submitted-url'],
        'submitted-image-url' => empty($opts['submitted-image-url']) ? '' : $opts['submitted-image-url']
      ]
    ];

    return $this->post($provider_id, $params);
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
    $params = [
      'comment' => $link
    ];

    return $this->post($provider_id, $params);
  }

}
