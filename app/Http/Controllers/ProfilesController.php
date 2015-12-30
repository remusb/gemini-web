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
  protected $profileRepository;

  public function __construct(ProfileRepository $profileRepository) {
    $this->middleware('auth');

    $this->profileRepository = $profileRepository;
  }

  public function getIndex() {
    $user = Auth::user();
    $profiles = $user->profiles()->get()->keyBy('id');

    return view('profiles.index', ['profiles' => $profiles]);
  }

  public function postIndex() {
    $profileName = Input::get('profile_name');

    $this->profileRepository->addProfile(Auth::user()->id, $profileName);

    return Redirect::back()
      ->withInput();
  }

  public function deleteIndex() {
    $profileId = Input::get('profile_id');

    $this->profileRepository->deleteProfile($profileId);
  }

  public function getRedirect() {
    $provider = Input::get('source');
    $profile_id = Input::get('profile');
    $service = SocialService::get($provider);

    Session::put('profile_id', $profile_id);

    return redirect($service->getRedirectUrl($profile_id));
  }

  public function getCallback() {
    $provider = Input::get('source');
    $user_id = Auth::user()->id;
    $service = SocialService::get($provider);

    $service->handleCallback($user_id, Session::get('profile_id'), Input::all());

    return redirect('/profiles');
  }

}
