@extends('master')

@section('page-title', 'Profiles')

@section('footer-js')
  @parent

  <!-- Bootstrap WYSIHTML5 -->
  <script>
    $(function () {
      $('.delete-profile').click(function() {
        var $row = $(this).parent(),
          profileId = $row.data('profile');

        $.ajax({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          method: "delete",
          url: "/profiles",
          data: { profile_id: profileId }
        })
        .done(function( msg ) {
          $row.remove();
        });
      });
    });
  </script>
@endsection

@section('content')
  <!-- Main content -->
  <section class="content">

  @foreach ($profiles as $profileId => $profile)
  <div class="row text-center" data-profile="{{ $profileId }}">
    <span class="lead">{{ $profile['name'] }}</span>
    <a class="btn btn-social-icon btn-facebook register-profile" href="/profiles/redirect?source=facebook&amp;profile={{ $profile['_id'] }}"><i class="fa fa-facebook"></i></a>
    <a class="btn btn-social-icon btn-twitter register-profile" href="/profiles/redirect?source=twitter&amp;profile={{ $profile['_id'] }}"><i class="fa fa-twitter"></i></a>
    <a class="btn btn-social-icon btn-google register-profile" href="/profiles/redirect?source=google&amp;profile={{ $profile['_id'] }}"><i class="fa fa-google-plus"></i></a>
    <a class="btn btn-social-icon btn-linkedin register-profile" href="/profiles/redirect?source=linkedin&amp;profile={{ $profile['_id'] }}"><i class="fa fa-linkedin"></i></a>
    <a class="btn btn-social-icon btn-instagram register-profile" href="/profiles/redirect?source=instagram&amp;profile={{ $profile['_id'] }}"><i class="fa fa-instagram"></i></a>
    <a class="delete-profile" href="#"><i class="fa fa-fw fa-remove"></i></a>
  </div><!-- /.row -->
  @endforeach

  @if (count($profiles) > 0)
  <div class="row">
    <div class="col-md-6 col-sm-6 col-xs-12">
      <hr />
    </div>
  </div>
  @endif

  <div class="row">
    <form action="/profiles" method="post">
      {!! csrf_field() !!}

      <div class="input-group margin">
        <div class="input-group-btn">
          <button type="submit" class="btn btn-danger">+ add profile</button>
        </div><!-- /btn-group -->
        <input type="text" class="form-control" name="profile_name" placeholder="profile name">
      </div>
    </form>
  </div>

  </section><!-- /.content -->
@endsection
