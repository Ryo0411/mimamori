<!DOCTYPE html>
<html lang="ja">

<head>

	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width,initial-scale=1">
		<meta name=”description” content=”徘徊者声掛け/あんしん見守り” />
		<!-- ポップアップ用UI -->
		<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/remodal/1.0.5/remodal.min.css">
		<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/remodal/1.0.5/remodal-default-theme.min.css">
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/remodal/1.0.5/remodal.min.js"></script>
		<title>徘徊者声掛け/あんしん見守り</title>
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
		<a href="/home_discover" class="arrow_s_b"></a>
		<h1>徘徊者声掛け</h1>
	</header>

	<section>
		<input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
		<div class="inner">
			<div class="block_70vh">
				<div class="block_rec" id="voiceprint_flg">
					<a id="exe_recording" class="btn_rec">
						<img id="rec_img" src="{{ asset('img/rec_on.png') }}" class="img_rec" alt="録音">
					</a>
				</div>

				<div class="announce" id="result">録音ボタンをタップして、<br>本日の日付を答えてください。</div>

				<div class="input">
					<h2 class="h2_input">&#9632;性別</h2>
					<select id="pulldown" name="sex">
						<option value=0>-</option>
						<option id="sex_men" value=1>男性</option>
						<option id="sex_wom" value=2>女性</option>
					</select>
				</div>

				<div class="block_txt mt2" id="exe_result">
					<p>貴方の声を認識し、<br>どなたか特定します。</p>
				</div>
			</div>
		</div>
	</section>

	<!-- 読み上げテキスト -->
	<div id="text-recording" title="タイトル" class="remodal" data-remodal-id="modal_r" data-remodal-options="closeOnOutsideClick: false">
		<h4>本日の日付を答えてください</h4>
		<div class="popup_inner">
			<p id="text_pop" style="font-size: 16px;">録音完了後の録音完了ボタンを<br />タップしてください。</p>
			<div class="btn_popup" style="margin-top: 10px">
				<button id="stop-recording" data-remodal-action="close" class="remodal-confirm">録音終了</button>
			</div>
		</div>
	</div>
	<!-- 認識結果 -->
	<div id="recognition-result" title="タイトル" class="remodal" data-remodal-id="modal_d">
		<h4>音声認識結果</h4>
		<div class="popup_inner">
			<p id="result_pop"></p>
			<p id="probability"></p>
			<div class="btn_popup">
				<button data-remodal-action="close" class="remodal-confirm">OK</button>
			</div>
		</div>
	</div>
	<!-- ERROR結果 -->
	<div id="recognition-result" title="タイトル" class="remodal" data-remodal-id="modal_e">
		<h4>ERROR</h4>
		<div class="popup_inner">
			<p id="errorresult"></p>

			<button data-remodal-action="close" class="remodal-confirm">OK</button>
		</div>
	</div>

	<footer class="footer">
		<div class="footer_ver">Ver. 1.0</div>
		<div class="footer_copy">Provided by Nippontect Systems Co.,Ltd</div>
	</footer>

	<!-- Speech SDK reference sdk. -->
	<!-- <script src="{{ asset('js/SpeechSDK/microsoft.cognitiveservices.speech.sdk.bundle.js') }}"></script> -->
	<!-- 音声データ学習用 -->
	<script src="{{ asset('js/recorder.js') }}"></script>
	<script src="{{ asset('js/recognition.js') }}"></script>
	<script>
		let rec_img_elm = document.getElementById("rec_img");
		let pulldown_elm = document.getElementById('pulldown');
		pulldown_elm.selectedIndex = 0;

		if (pulldown_elm.value == 0) {
			document.getElementById('result').innerHTML = "<p>性別を選択してください。</p>";
			document.getElementById('exe_result').innerHTML = "<p>性別を選択して、<br>音声認識を始めてください。</p>";
			rec_img_elm.src = "./img/rec_off.png";
		}
		pulldown_elm.addEventListener('change', function() {
			if (pulldown_elm.selectedIndex != 0) {
				document.getElementById('result').innerHTML = "録音ボタンをタップして、<br>本日の日付を答えてください。";
				document.getElementById('exe_result').innerHTML = "<p>貴方の声を認識し、<br>どなたか特定します。</p>";
				rec_img_elm.src = "../../img/rec_on.png";
			} else {
				document.getElementById('result').innerHTML = "<p>性別を選択してください。</p>";
				document.getElementById('exe_result').innerHTML = "<p>性別を選択して、<br>音声認識を始めてください。</p>";
				rec_img_elm.src = "./img/rec_off.png";
			}
		});
	</script>
</body>

</html>