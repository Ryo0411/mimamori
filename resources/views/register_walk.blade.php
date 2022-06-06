<!DOCTYPE html>
<html lang="ja">

<head>
	<html lang="en">

	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width,initial-scale=1">
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
		<a href="/home_walk" class="arrow_s_b"></a>
		<h1>情報登録</h1>
	</header>

	<section>
		<div class="inner">
			<x-alert type="success" :session="session('exe_msg')" />
			<form method="POST" action="{{ route('registerupdate') }}">
				@csrf
				<div class="input">
					<h2 class="h2_input">&#9632;性別</h2>
					<select id="pulldown" name="sex" value="{{ @$wanderer_list->sex }}">
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
					<input name="age" type="text" class="age" value="{{ @$wanderer_list->age }}"><span class="txt_input">歳</span>
					@if ($errors->has('age'))
					<div class="alert alert-danger">
						{{ $errors->first('age') }}</li>
					</div>
					@endif
				</div>
				<div class="input">
					<h2 class="h2_input">&#9632;名前</h2>
					<input name="wanderer_name" type="text" value="{{ @$wanderer_list->wanderer_name }}">
					@if ($errors->has('wanderer_name'))
					<div class="alert alert-danger">
						{{ $errors->first('wanderer_name') }}</li>
					</div>
					@endif
				</div>
				<div class="input">
					<h2 class="h2_input">&#9632;緊急連絡先</h2>
					<input name="emergency_tel" type="text" value="{{ @$wanderer_list->emergency_tel }}">
					@if ($errors->has('emergency_tel'))
					<div class="alert alert-danger">
						{{ $errors->first('emergency_tel') }}</li>
					</div>
					@endif
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

				<div class="announce" id="result">録音ボタンをタップして、<br>本日の日付を答えてください。</div>
                @if ($errors->has('audio_file'))
                <div class="alert alert-danger">
                    {{ 音声を録音してください。 }}</li>
                </div>
                @endif

				<div class="btn mt2">
					<button type="submit" id="btn_regist" class="btn-red">登録</button>
				</div>
			</form>
		</div>
	</section>

	<footer class="footer">
		<div class="footer_ver">Ver. 1.0</div>
		<div class="footer_copy">Provided by Nippontect Systems Co.,Ltd</div>
	</footer>

	<!-- Speech SDK reference sdk. -->
	<!-- script src="{{ asset('js/SpeechSDK/microsoft.cognitiveservices.speech.sdk.bundle.js') }}"></script> -->
	<!-- profile_id作成用 -->
	<!-- <script src="{{ asset('js/numbering.js') }}"></script> -->
	<!-- 音声データ学習用 -->
	<script src="{{ asset('js/recorder.js') }}"></script>
	<script src="{{ asset('js/recording.js') }}"></script>
	<!-- Speech SDK USAGE -->
	<!-- <script type="text/javascript" src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
	<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" />
	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> -->


	<script>
        function initialize() {
            // 音声登録をしていない時のみ音声登録ボタンの表示。
            let voiceprint_flg = document.getElementById("voiceprint_flg");
            let result = document.getElementById("result");

            // 初回表示のみ空なので０を代入。
            if (voiceprint_flg.value == "") {
                voiceprint_flg.value = "0";
            };
            if (voiceprint_flg.value != "0") {
                document.querySelector('.block_rec').style.display = 'none';
                result.style.display = 'none';
            };

            // 性別プルダウン初期値
            let select = document.getElementById("pulldown");
            let sexnum = select.getAttribute('value');
            if (!sexnum) {
                sexnum = 0;
            }
            select.options[sexnum].selected = true;

        }
        initialize();
	</script>

</body>

</html>
