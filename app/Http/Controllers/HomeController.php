<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Repositories\ProfileRepository;
use App\Jobs\PublishMessage;
use App\Models\Post;

use Auth;
use Input;
use Redirect;

class HomeController extends Controller {
  use DispatchesJobs;

  public function __construct(ProfileRepository $profileRepository) {
    $this->middleware('auth');

    $this->profileRepository = $profileRepository;
  }

  public function getIndex() {
    $user = Auth::user();
    $profiles = $user->profiles()->get()->keyBy('id');
    $providers = $user->providers()->get()->groupBy('profile_id')->toArray();

    return view('home.index', ['profiles' => $profiles, 'providers' => $providers]);
  }

  public function postIndex() {
    $user = Auth::user();
    $message = Input::get('message');
    $services = Input::get('services');

    foreach ($services as $providerId => $serviceEnabled) {
      $provider = \App\Models\Provider::find($providerId);
      $bIsEnabled = (bool) $serviceEnabled;

      if ($bIsEnabled === true) {
        $post = new Post;
        $post->service = $provider['service'];
        $post->message = $message;
        $post->user_id = $user->id;
        $post->provider_id = $providerId;
        $post->scheduled_at = time();

        $post->save();
        $this->dispatch(new PublishMessage($post));
      }
    }

    return redirect('/');
  }

}

?>
