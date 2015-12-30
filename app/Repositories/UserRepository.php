<?php

namespace App\Repositories;

use DB;

class UserRepository {

  public function getClient($client_id) {
    $client = DB::collection('oauth_clients')->where('id', $client_id)->first();

    return $client;
  }

  // public function getUserFromOAuthClient($client_id) {
  //   $client = DB::collection('oauth_clients')->where('id', $client_id)->first();

  //   return DB::collection('users')->find($client['user_id']);
  // }

  // public function addUser($web_id, $first_name, $last_name) {
  //   $id = DB::collection("users")->insert([
  //     'web_id' => $data['user_id'],
  //     'first_name' => $data['first_name'],
  //     'last_name' => $data['last_name']
  //   ]);

  //   return $id;
  // }

  public function addOauthClient($owner_id, $secret) {
    $user = DB::collection('users')->where('web_id', $web_id)->first();
    $user_token = UserRepository::getNextId();

    $id = DB::collection("oauth_clients")->insert([
      'id' => $user_token,
      'secret' => $secret,
      'name' => 'Gemini',
      'owner_id' => $owner_id
    ]);

    return $user_token;
  }

  public static function getNextId() {
    $seq = DB::getCollection('user_counter')->findAndModify(
      array('_id' => 'user_id'),
      array('$inc' => array('seq' => 1)),
      null,
      array('new' => true, 'upsert' => true)
    );

    return $seq['seq'];
  }
}
