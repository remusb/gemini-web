<?php

namespace App\Services;

interface ISocialService {

  /**
   * Scopes for using the client
   *
   * @return array
   */
  public static function scopes();

  /**
   * Get the OAuth 2 state
   *
   * @return string
   */
  public function getState();

  /**
   * Build and return the client URL for authentication
   *
   * @return string
   */
  public function getRedirectUrl();

  /**
   * Process data returned as callback from the client
   *
   * @param  string  $user_id
   * @param  string  $profile_id
   * @param  array  $data
   * @return \App\Models\Provider
   */
  public function handleCallback($user_id, $profile_id, $data = []);

  /**
   * Publish a message to the client
   *
   * @param  string  $provider_id
   * @param  array  $params
   * @param  array  $opts = []
   * @return mixed
   */
  public function post($provider_id, $params, $opts = []);

  /**
   * Publish a text message to the client
   *
   * @param  string  $provider_id
   * @param  string  $message
   * @param  array  $opts = []
   * @return mixed
   */
  public function postMessage($provider_id, $message, $opts = []);

  /**
   * Publish a link to the client
   *
   * @param  string  $provider_id
   * @param  string  $link
   * @param  array  $opts = []
   * @return mixed
   */
  public function postLink($provider_id, $link, $opts = []);

  /**
   * Publish an image to the client
   *
   * @param  string  $provider_id
   * @param  string  $image
   * @param  array  $opts = []
   * @return mixed
   */
  public function postImage($provider_id, $image, $opts = []);

  /**
   * Publish a video to the client
   *
   * @param  string  $provider_id
   * @param  string  $video
   * @param  array  $opts = []
   * @return mixed
   */
  public function postVideo($provider_id, $video, $opts = []);

}
