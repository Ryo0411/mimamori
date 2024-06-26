<!DOCTYPE html>
<html lang="ja">

<head>

	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width,initial-scale=1" viewport-fit="cover">
		<meta name=”description” content=”発見者ホーム/あんしん見守り” />
		<title>発見者ホーム/あんしん見守り</title>
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
		<a href="/home" class="arrow_s_b"></a>
		<h1>発見者ホーム</h1>
		<a href="/home" class=""><img src="{{ asset('img/ico_home.svg') }}" class="btn_home" alt="TOPへ戻る"></a>
	</header>

	<section>
		<div class="inner">
			<div class="block_40vh">
				<div class="btn mb4">
					<button id="button" class="btn-discover" onclick="location.href='/register_discover'">情報登録</button>
				</div>
				<div class="btn">
					<button id="voicebutton" class="btn-discover" onclick="location.href='/voice_discover'">迷子者声掛け</button>
				</div>
				<input name="age" type="hidden" class="age" id="age" value="{{ old('age',Auth::user()->age) }}">
			</div>
		</div>
	</section>


	<footer class="footer">
		<div class="footer_ver">Ver. 2.1</div>
		<div class="footer_copy">Provided by Nippontect Systems Co.,Ltd</div>
	</footer>

	<script>
		// 更新ボタンの文字変更
		var age = document.getElementById("age");
		var voicebutton = document.getElementById("voicebutton");
		if (age.value == "") {
			voicebutton.style.display = "none";
		};
	</script>

</body>

</html>