<!DOCTYPE html>
<html lang="ja">

<head>

	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width,initial-scale=1" viewport-fit="cover">
		<meta name=”description” content=”あんしん見守り” />
		<title>あんしん見守り</title>
		<!-- script -->
		<script src="{{ asset('js/app.js') }}" defer></script>
		<!-- styles -->
		<link href="{{ asset('css/app.css') }}" rel="stylesheet">
		</link><!-- bootsutorap用のCSS -->
		<link href="{{ asset('css/style.css') }}" rel="stylesheet">
		</link>
		<script src="{{ asset('js/fv.js') }}"></script>
		<script src="https://kit.fontawesome.com/d751a05ce0.js" crossorigin="anonymous"></script>
	</head>

<body>
	<header>
		<section>
			<a href="/home_walk" class="arrow_s_b"></a>
			<h1>音声一覧</h1>
			<a href="/home" class=""><img src="{{ asset('img/ico_home.svg') }}" class="btn_home" alt="TOPへ戻る"></a>
		</section>
	</header>

	<section>
		<div class="inner">
			@csrf
			<div class="block_txt mb2" id="exe_result">
				<p>雑音やデータに不具合がある場合は<br>音声データを削除して下さい。</p>
			</div>
			<div class="btn mt2">
				@foreach($voicelists as $voicelist)
				<div class="container">
					<div class="item">
						<input type="hidden" value="音声登録番号{{ $voicelist['id'] }}"></input>
						<input type="hidden" value="{{ $voicelist['speaker_id'] }}"></input>
						<audio src="data:audio/wav;base64,{{ $voicelist['speaker_audio'] }}" preload="metadata" controls=""></audio>
					</div>
					<div class="item">
						<a id="audio_delete" class="audio_delete" value="{{ $voicelist['speaker_id'] }}" href="{{ route('audioDelete', $voicelist['speaker_id']) }}" onclick="js_alert();"><i class="fa-solid fa-trash"></i><span>削除</span></a>
					</div>
				</div>
				@endforeach
			</div>
		</div>
	</section>

	<!-- ロード画面用 -->
	<div id="loading" class="is-hide">
		<div class="cv-spinner">
			<span class="spinner"></span>
		</div>
	</div>

	<footer class="footer">
		<div class="footer_ver">Ver. 1.0</div>
		<div class="footer_copy">Provided by Nippontect Systems Co.,Ltd</div>
	</footer>

	<!-- 音声削除用API -->
	<script src="{{ asset('js/deleteapi.js') }}"></script>

</body>
<script>
	function showLoading() {
		document.getElementById('loading').classList.remove('is-hide')
	}

	function hideLoading() {
		document.getElementById('loading').classList.add('is-hide')
	}

	function js_alert() {
		showLoading();
	}
</script>

</html>