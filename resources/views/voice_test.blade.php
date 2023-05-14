<!DOCTYPE html>
<html lang="ja">

<head>

	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width,initial-scale=1" viewport-fit="cover">
		<meta name=”description” content=”声だけ登録/ホーム” />
		<!-- ポップアップ用UI -->
		<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/remodal/1.0.5/remodal.min.css">
		<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/remodal/1.0.5/remodal-default-theme.min.css">
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/remodal/1.0.5/remodal.min.js"></script>
		<title>声だけ登録/あんしん見守り</title>
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
			<a href="/home" class=" arrow_s_b"></a>
			<h1>動作チェック</h1>
			<a href="/home" class=""><img src="{{ asset('img/ico_home.svg') }}" class="btn_home" alt="TOPへ戻る"></a>
		</section>
	</header>

	<section>
		<div class="inner">
			<div>
				<x-alert type="success" :session="session('exe_msg')" />
				<form method="POST" action="{{ route('voiceupdate') }}">
					@csrf
					<div class="block_txt mb2" id="exe_result">
						<p>音声録音に対応しているかチェックを行います。<br>マイクのマークをタップしてください。</p>
					</div>

					<div class="block_rec" id="voiceprint_btn">
						<a id="exe_recording" class="btn_rec">
							<img id="rec_img" src="{{ asset('img/rec_on.png') }}" class="img_rec" alt="録音">
						</a>
					</div>
					<div class="btn_dl">
						<a id="enrollmentDownload" class="soundsample"></a>
					</div>

					<div class="announce" id="result"></div>
					@if ($errors->has('audio_file'))
					<div class="alert alert-danger">
						{{ "音声を録音してください。" }}</li>
					</div>
					@endif

					<div class="available">マイク使用可能環境<br>Chrome 53以降<br>Edge 79以降<br>Safari 11以降<br>Chrome Android 53以降<br>Safari on iOS 11以降</div>
					<!-- Chrome 53以降
					Edge 79以降
					Safari 11以降
					Chrome Android 53以降
					Safari on iOS 11以降 -->

				</form>
			</div>

		</div>
		<!-- マイクERROR -->
		<div id="recognition-result" title="タイトル" class="remodal" data-remodal-id="modal_me">
			<h4>ERROR</h4>
			<div class="popup_inner">
				<p style="font-size: 16px;">マイクが使用できませんでした、端末の設定を確認してください。</p>
				<button data-remodal-action="close" class="remodal-confirm" id="micerr">OK</button>
				<button data-remodal-action="cancel" class="remodal-cancel" onclick="location.href='/home'">TOPへ</button>
			</div>
		</div>
		</div>
	</section>

	<div id="text-recording" title="タイトル" class="remodal" data-remodal-id="modal_d" data-remodal-options="closeOnOutsideClick: false">
		<h3>音声録音チェック</h3>
		<div class="popup_inner">
			<p id="text_pop" style="font-size: 18px;"></p>
			<div class="btn_popup" style="margin-top: 10px">
				<button id="stop-recording" data-remodal-action="close" class="remodal-confirm">録音終了</button>
			</div>
		</div>
	</div>

	<footer class="footer">
		<div class="footer_ver">Ver. 2.1</div>
		<div class="footer_copy">Provided by Nippontect Systems Co.,Ltd</div>
	</footer>

	<script>
		/* bootstrap alertをx秒後に消す */
		if (document.getElementById("alertfadeout") != null) {
			location.href = '#modal_e';
		}
	</script>

	<!-- Speech SDK reference sdk. -->
	<!-- 音声データ学習用 -->
	<script src="{{ asset('js/voice_test.js') }}"></script>

</body>

</html>