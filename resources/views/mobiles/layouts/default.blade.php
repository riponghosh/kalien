<!DOCTYPE html>
<html lang="en">
	<head>
	    <meta charset="utf-8">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1 user-scalable=no">
  		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/simple-line-icons/2.4.1/css/simple-line-icons.css">
  		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		{!! Html::style('css/app.css'.VERSION) !!}
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tiny-slider/2.3.5/tiny-slider.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" crossorigin="anonymous">
		<title>@yield('title')</title>
		<style>
			body{
				background: #eff0f0;
				height: 100%;
				margin: 0;
				padding: 0;
				-webkit-font-smoothing: subpixel-antialiased;
				font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
			}
		</style>

	</head>

	<body style="background-color: #f3f3f3;">

		<div id="app">
			<!--like this, I need the init import for lang setting-->
			<Init
				auth="{{Auth::check()}}"
				lang="{{Cookie::get('web_language')}}"
				cur-unit="{{CLIENT_CUR_UNIT}}"
				cur-rate="{{json_encode(['USD' => 1, 'HKD' => 7.8118,'JPY' => 110.1200, 'TWD' => 30.0291],true)}}"
				@if(!Auth::guest())
				user-name="{{Auth::user()->name}}"
				user-avatar="{{storageUrl(optional(Auth::user()->avatar)->media['media_location_standard'])}}"
				@endif
			>
			</Init>
		    <Navbar>
		    </Navbar>
			<mymodal ref="login"></mymodal>
			<loader ref="preloader"></loader>

		</div>

		<div id="app2">
			@yield('content')
		</div>
		<div id="app3">
			<Cfooter></Cfooter>
		</div>
		@yield('script')
  		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<script>
            function SocialLoginCallParent(data) {
                app.fbLoginResponse(data);
            }
		</script>

	</body>
</html>