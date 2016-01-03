@extends('master')

@section('page-title', 'Publish')

@section('header-css')
  @parent

  <link rel="stylesheet" href="/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
@endsection

@section('footer-js')
  @parent

  <!-- Bootstrap WYSIHTML5 -->
  <script src="/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
  <script>
    $(function () {
      //bootstrap WYSIHTML5 - text editor
      $(".textarea").wysihtml5();

      $('.publish-service').click(function() {
        var $this = $(this),
            $valueField = $this.next();

        $this.toggleClass('off');
        $valueField.val($valueField.val() == "0" ? "1" : "0");
      });

      $('.publish-now').click(function() {
        $('form.publish').submit();
      });
    });
  </script>
@endsection

@section('content')
  <!-- Main content -->
  <section class="content">

  <form class="publish" action="/" method="post">
    {!! csrf_field() !!}

    @foreach ($providers as $profileId => $services)
    <div class="row text-center">
      <span class="lead">{{ $profiles[$profileId]['name'] }}</span>

      <?php foreach ($services as $service): ?>
        <?php if (in_array($service['service'], ['google', 'instagram'])) continue; ?>
        <a class="off btn btn-social-icon btn-{{ $service['service'] }} publish-service" href="#"><i class="fa fa-{{ $service['service'] }}"></i></a>
        <input type="hidden" name="services[{{ $service['_id'] }}]" value="0" />
      <?php endforeach ?>
    </div><!-- /.row -->
    @endforeach

    <div class="row">
      <div class="form-group">
        <label>Message</label>
        <textarea name="message" class="form-control" rows="3" placeholder="Place your message here"></textarea>
      </div>
    </div>

    <div class="row">
      <div class="btn-group">
        <button type="button" class="btn btn-info publish-now">publish</button>
        <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
          <span class="caret"></span>
          <span class="sr-only">Toggle Dropdown</span>
        </button>
        <ul class="dropdown-menu" role="menu">
          <li><a class="publish-now" href="#">publish now</a></li>
          <li><a class="publish-queue" href="#">add to queue</a></li>
          <li><a class="schedule" href="#">schedule</a></li>
        </ul>
      </div>
    </div>
  </form>

  </section><!-- /.content -->
@endsection
