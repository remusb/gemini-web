<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Repositories\ProfileRepository;
use App\Repositories\ProviderRepository;
use App\Repositories\PostRepository;
use App\Jobs\PublishMessage;
use App\Models\Post;

use Auth;
use Input;
use Redirect;

class HomeController extends Controller {
  use DispatchesJobs;

  /**
   * The Profile Repository
   *
   * @var \App\Repositories\ProfileRepository
   */
  protected $profileRepository;

  /**
   * The Provider Repository
   *
   * @var \App\Repositories\ProviderRepository
   */
  protected $providerRepository;

  /**
   * The Post Repository
   *
   * @var \App\Repositories\PostRepository
   */
  protected $postRepository;

  /**
   * Create a new Home controller
   *
   * @param  \App\Repositories\ProfileRepository  $profileRepository
   * @param  \App\Repositories\ProviderRepository  $providerRepository
   * @param  \App\Repositories\PostRepository  $postRepository
   * @return void
   */
  public function __construct(ProfileRepository $profileRepository, ProviderRepository $providerRepository, PostRepository $postRepository) {
    $this->middleware('auth');

    $this->profileRepository = $profileRepository;
    $this->providerRepository = $providerRepository;
    $this->postRepository = $postRepository;
  }

  /**
   * GET: /
   *
   * @return view(home.index)
   */
  public function getIndex() {
    $user = Auth::user();
    $profiles = $this->profileRepository->getFromUser($user);
    $providers = $this->providerRepository->getFromUserByProfile($user);

    return view('home.index', ['profiles' => $profiles, 'providers' => $providers]);
  }

  /**
   * POST: /
   *
   * @return redirect(/)
   */
  public function postIndex() {
    $user = Auth::user();
    $message = Input::get('message');
    $services = Input::get('services');

    foreach ($services as $providerId => $serviceEnabled) {
      $bIsEnabled = (bool) $serviceEnabled;

      if ($bIsEnabled === true) {
        $provider = $this->providerRepository->get($providerId);
        $post = $this->postRepository->createMessage($provider, $message);

        $this->dispatch(new PublishMessage($post));
      }
    }

    return redirect('/');
  }

}

?>
