<!DOCTYPE html>
<html lang="ja">

<head>

	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width,initial-scale=1">
		<meta name=”description” content=”徘徊者ホーム/あんしん見守り” />
		<title>徘徊者ホーム/あんしん見守り</title>
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
		<h1>徘徊者ホーム</h1>
	</header>

	<section>
		<div class="inner">
			<h2 class="sectTit">ご家族情報</h2>
			<div class="block_txt">
				<p>{{ $exe }}</p>
			</div>
		</div>
	</section>

	<section>
		<div class="inner">
			<div class="block_50vh">
				<div class="btn">
					<button id="button" class="btn-walk" onclick="location.href='/register_walk'">情報登録</button>
				</div>
				<div class="btn" {{$status}}>
					<button id="button" class="btn-walk" onclick="location.href='/voice_walk'">声だけ登録</button>
				</div>
				<div class="btn" {{$status}}>
					<button id="button" class="btn-red" onclick="location.href='/home_walk/wanderer'">徘徊！</button>
				</div>
				<input name="voiceprint_flg" type="hidden" id="voiceprint_flg" value="{{ $exe }}"></input>
			</div>
		</div>
	</section>

	<footer class="footer">
		<div class="footer_ver">Ver. 1.0</div>
		<div class="footer_copy">Provided by Nippontect Systems Co.,Ltd</div>
	</footer>

</body>

</html>