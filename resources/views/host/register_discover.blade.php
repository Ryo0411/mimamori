<!DOCTYPE html>
<html lang="ja">

<head>

	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width,initial-scale=1" viewport-fit=cover'>
		<meta name=”description” content=”情報登録/あんしん見守り/ホーム” />
		<title>情報登録/あんしん見守り</title>
		<!-- script -->
		<script src="{{ asset('js/app.js') }}" defer></script>
		<!-- styles -->
		<link href="{{ asset('css/app.css') }}" rel="stylesheet">
		</link><!-- bootsutorap用のCSS -->
		<link href="{{ asset('css/style.css') }}" rel="stylesheet">
		</link>
	</head>

<body>
	<header>
		<section>
			<a href="/home_discover" class="arrow_s_b"></a>
			<h1>情報登録</h1>
			<a href="/home" class=""><img src="{{ asset('img/ico_home.svg') }}" class="btn_home" alt="TOPへ戻る"></a>
		</section>
	</header>

	<section>
		<div class="inner">
			<x-alert type="success" :session="session('exe_msg')" />
			<form method="POST" action="{{ route('userupdate') }}">
				@csrf
				<div class="input">
					<h2 class="h2_input">&#9632;性別</h2>
					<select id="pulldown" name="sex" value="{{ old('sex',Auth::user()->sex) }}">
						<option value=0>-</option>
						<option value=1>男性</option>
						<option value=2>女性</option>
					</select>
					@if ($errors->has('sex'))
					<div class="alert alert-danger">
						{{ $errors->first('sex') }}</li>
					</div>
					@endif
				</div>
				<div class="input">
					<h2 class="h2_input">&#9632;年齢</h2>
					<input name="age" type="text" class="age" value="{{ old('age',Auth::user()->age) }}"><span class="txt_input">歳</span>
					@if ($errors->has('age'))
					<div class="alert alert-danger">
						{{ $errors->first('age') }}</li>
					</div>
					@endif
				</div>
				<div class="input">
					<h2 class="h2_input">&#9632;名前</h2>
					<input name="name" type="text" value="{{ old('name',Auth::user()->name) }}">
					@if ($errors->has('name'))
					<div class="alert alert-danger">
						{{ $errors->first('name') }}</li>
					</div>
					@endif
				</div>
				<div class="btn mt2">
					<button type="submit" id="button" class="btn-red">更新</button>
				</div>
			</form>
		</div>
	</section>

	<footer class="footer">
		<div class="footer_ver">Ver. 1.0</div>
		<div class="footer_copy">Provided by Nippontect Systems Co.,Ltd</div>
	</footer>

	<!-- 性別プルダウン初期値 -->
	<script>
		var select = document.getElementById("pulldown");
		var sexnum = select.getAttribute('value');
		select.options[sexnum].selected = true;
	</script>
	<script>
		/* bootstrap alertをx秒後に消す */
		$(document).ready(function() {
			$(window).load(function() {
				window.setTimeout("$('#alertfadeout').fadeOut()", 1500);
			});
		});
	</script>

</body>

</html>