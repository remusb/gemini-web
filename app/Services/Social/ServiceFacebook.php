<?php

namespace App\Services\Social;

use App\Repositories\ProviderRepository;
use App\Services\ISocialService;
use App\Services\SocialServiceGeneric;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Provider\Facebook;

use App;
use Config;

class ServiceFacebook extends SocialServiceGeneric implements ISocialService {
  /**
   * @var string Service Code
   */
  const SERVICE_CODE = 'facebook';

  /**
   * @var int Default token expiration time
   */
  const TOKEN_TTL = 5184000; // 60 days

  /**
   * @var Facebook client service
   */
  protected $client;

  /**
   * @var string Facebook Graph API url
   */
  protected $graphUrl;

  /**
   * The Facebook app secret proof
   *
   * @var string|AccessToken;
   */
  protected $app_secret_proof;

  /**
   * Create a new Facebook service
   *
   * @param  ProviderRepository  $providerRepository
   */
  function __construct(ProviderRepository $providerRepository) {
    parent::__construct($providerRepository);

    $this->client = new Facebook([
      'clientId' => Config::get('services.facebook.client_id'),
      'clientSecret' => Config::get('services.facebook.client_secret'),
      'redirectUri' => Config::get('services.facebook.redirect'),
      'graphApiVersion' => Config::get('services.facebook.default_graph_version')
    ]);

    $this->graphUrl = Facebook::BASE_GRAPH_URL . Config::get('services.facebook.default_graph_version');
  }

  /**
   * Scopes for using the client
   *
   * @return array
   */
  public static function scopes() {
    return ['publish_actions', 'publish_pages', 'manage_pages', 'user_posts', 'user_about_me', 'user_likes', 'user_friends'];
  }

  /**
   * Prepare the OAuth client before sending a request
   *
   * @param  string  $provider_id
   * @throws \Exception
   */
  protected function prepareRequest($provider_id) {
    parent::prepareRequest($provider_id);

    $this->app_secret_proof = hash_hmac('sha256', $this->token, Config::get('services.facebook.client_secret'));
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

    $shortToken = $this->token;
    $this->token = $this->client->getLongLivedAccessToken($shortToken);

    $user = $this->client->getResourceOwner($this->token)->toArray();

    return $this->providerRepository->create($user_id, $profile_id, ServiceFacebook::SERVICE_CODE, 
      ['token' => $this->token->getToken(), 'expires_at' => ServiceFacebook::TOKEN_TTL + time(), 'vendor_id' => $user['id'],
      'email' => $user['email'], 'avatar' => $user['picture_url'], 'first_name' => $user['first_name'],
      'last_name' => $user['last_name'], 'gender' => $user['gender']]
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
    $this->prepareRequest($provider_id);

    $options['body'] = http_build_query($params);

    $request = $this->client->getRequest(
      'POST',
      $this->graphUrl . '/me/feed?' . http_build_query(['access_token' => $this->token, 'appsecret_proof' => $this->app_secret_proof]),
      $options
    );

    $response = $this->client->getResponse($request);
    if (empty($response['id'])) {
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
      'message' => $message
    ];

    return $this->post($provider_id, $params);
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
    $params = [
      'link' => $link
    ];

    return $this->post($provider_id, $params);
  }
}
