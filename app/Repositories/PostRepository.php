<?php

namespace App\Repositories;

use App;
use DB;
use App\Models\Post;

class PostRepository {

  /**
   * Create a new post with a text message
   *
   * @param  \App\Models\Provider  $provider
   * @param  string  $message
   * @return \App\Models\Post
   */
  public function createMessage($provider, $message) {
    $post = new Post;
    
    $post->service = $provider->service;
    $post->message = $message;
    $post->user_id = $provider->user_id;
    $post->provider_id = $provider->id;
    $post->scheduled_at = time();

    $post->save();

    return $post;
  }

}

?>
