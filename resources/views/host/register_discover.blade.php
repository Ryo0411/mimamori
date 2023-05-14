<!DOCTYPE html>
<html lang="ja">

<head>

	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width,initial-scale=1" viewport-fit=cover'>
		<meta name=”description” content=”情報登録/あんしん見守り/ホーム” />
		<!-- ポップアップ用UI -->
		<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/remodal/1.0.5/remodal.min.css">
		<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/remodal/1.0.5/remodal-default-theme.min.css">
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/remodal/1.0.5/remodal.min.js"></script>
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
					<input name="age" type="text" class="age" id="age" value="{{ old('age',Auth::user()->age) }}"><span class="txt_input">歳</span>
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
			<!-- 登録ポップアップ -->
			<div id="recognition-result" title="タイトル" class="remodal" data-remodal-id="modal_d">
				<h4>完了！</h4>
				<div class="popup_inner">
					<p id="result_pop">登録情報を更新しました。</p>
					<div class="btn_popup">
						<button data-remodal-action="close" class="remodal-confirm" id="okbtn">OK</button>
						<button data-remodal-action="cancel" class="remodal-cancel" onclick="location.href='/home'">TOPへ</button>
					</div>
				</div>
			</div>
		</div>
	</section>

	<footer class=" footer">
		<div class="footer_ver">Ver. 2.1</div>
		<div class="footer_copy">Provided by Nippontect Systems Co.,Ltd</div>
	</footer>

	<script>
		/* bootstrap alertをx秒後に消す */
		if (document.getElementById("alertfadeout") != null) {
			location.href = '#modal_d';
		}

		document.getElementById("okbtn").onclick = function() {
			window.setTimeout("$('#alertfadeout').fadeOut()", 1500);
		};

		// 更新ボタンの文字変更
		var age = document.getElementById("age");
		var button = document.getElementById("button");
		if (age.value == "") {
			button.innerText = "登録";
		};
	</script>
	<!-- 性別プルダウン初期値 -->
	<script>
		var select = document.getElementById("pulldown");
		var sexnum = select.getAttribute('value');
		select.options[sexnum].selected = true;
	</script>
</body>

</html>