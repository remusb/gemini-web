<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Repositories\ProfileRepository;

use SocialService;
use Auth;
use Input;
use Redirect;
use Session;
use Config;

class ProfilesController extends Controller {

  /**
   * The Profile Repository
   *
   * @var \App\Repositories\ProfileRepository
   */
  protected $profileRepository;

  /**
   * Create a new Home controller
   *
   * @param  \App\Repositories\ProfileRepository  $profileRepository
   */
  public function __construct(ProfileRepository $profileRepository) {
    $this->middleware('auth');

    $this->profileRepository = $profileRepository;
  }

  /**
   * GET: /profiles
   *
   * @return view(profiles.index)
   */
  public function getIndex() {
    $user = Auth::user();
    $profiles = $this->profileRepository->getFromUser($user);

    return view('profiles.index', ['profiles' => $profiles]);
  }

  /**
   * POST: /profiles
   *
   * @return redirect(/profiles)
   */
  public function postIndex() {
    $profileName = Input::get('profile_name');

    $this->profileRepository->create(Auth::user()->id, $profileName);

    return redirect('/profiles');
  }

  /**
   * DELETE: /profiles
   *
   * @return redirect(/profiles)
   */
  public function deleteIndex() {
    $profileId = Input::get('profile_id');

    $this->profileRepository->delete($profileId);
  }

  /**
   * GET: /profiles/redirect
   *
   * @return redirect(CLIENT_URL)
   */
  public function getRedirect() {
    $provider = Input::get('source');
    $profile_id = Input::get('profile');
    $service = SocialService::get($provider);

    $redirectUrl = $service->getRedirectUrl();
    $state = $service->getState();

    Session::put('profile_id', $profile_id);
    Session::put('local_state', $state);

    return redirect($redirectUrl);
  }

  /**
   * GET: /profiles/callback
   *
   * @return redirect(/profiles)
   */
  public function getCallback() {
    $provider = Input::get('source');
    $user_id = Auth::user()->id;
    $service = SocialService::get($provider);

    $params = array_merge(Input::all(), ['local_state' => Session::get('local_state'), 'state' => Input::get('state', '')]);

    $service->handleCallback($user_id, Session::get('profile_id'), $params);

    return redirect('/profiles');
  }

}
