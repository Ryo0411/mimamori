<!DOCTYPE html>
<html lang="ja">

<head>

	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width,initial-scale=1">
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
			<a href="/home_walk" class="arrow_s_b"></a>
			<h1>声だけ登録</h1>
		</section>
	</header>

	<section>
		<div class="inner">
			<div class="block_70vh">
			<x-alert type="success" :session="session('exe_msg')" />
			<form method="POST" action="{{ route('voiceupdate') }}">
				@csrf
				<div class="block_txt mb2">
					<p>マッチング精度向上のため声紋を登録します。</p>
				</div>

				<input name="profile_id" type="hidden" id="profile_id" value="{{ @$wanderer_list->profile_id }}"></input>
				<input name="voiceprint_flg" type="hidden" id="voiceprint_flg" value="{{ @$wanderer_list->voiceprint_flg }}"></input>
				<input name="audio_file" type="hidden" id="audio_file" value=""></input>

				<div class="block_rec" id="voiceprint_btn">
					<a id="exe_recording" class="btn_rec">
						<img id="rec_img" src="{{ asset('img/rec_on.png') }}" class="img_rec" alt="録音">
					</a>
				</div>
				<div class="btn_dl">
					<a id="enrollmentDownload" class="soundsample"></a>
				</div>

				<div class="btn mt2">
					<button type="submit" id="btn_regist" class="btn-red">登録</button>
				</div>

				<div class="announce" id="result">録音ボタンをタップして、<br>本日の日付を答えてください。</div>
                @if ($errors->has('audio_file'))
                <div class="alert alert-danger">
                    {{ 音声を録音してください。 }}</li>
                </div>
                @endif
			</form>
		</div>
	</div>
	</section>



	<footer class="footer">
		<div class="footer_ver">Ver. 1.0</div>
		<div class="footer_copy">Provided by Nippontect Systems Co.,Ltd</div>
	</footer>

	<!-- Speech SDK reference sdk. -->
	<!-- <script src="{{ asset('js/SpeechSDK/microsoft.cognitiveservices.speech.sdk.bundle.js') }}"></script> -->
	<!-- 音声データ学習用 -->
	<script src="{{ asset('js/recorder.js') }}"></script>
	<script src="{{ asset('js/recording.js') }}"></script>

</body>

</html>
