<!DOCTYPE html>
<html lang="ja">

<head>

	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width,initial-scale=1" viewport-fit="cover">
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
		<a href="/home" class=""><img src="{{ asset('img/ico_home.svg') }}" class="btn_home" alt="TOPへ戻る"></a>
	</header>

	<section>
		<input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
		<div class="inner">
			<div class="block_70vh">
				<div class="block_rec" id="voiceprint_flg" style="display: block;">
					<a id="exe_recording" class="btn_rec">
						<img id="rec_img" src="{{ asset('img/rec_on.png') }}" class="img_rec" alt="録音">
					</a>
				</div>

				<div class="announce" id="result" style="display: block;">録音ボタンをタップして、<br>ご自身の生年月日を教えてください。</div>

				<div class="input" id="sex_select" style="display: block;">
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
	<input name="latitude" type="hidden" id="latitude" value=""></input>
	<input name="longitude" type="hidden" id="longitude" value=""></input>

	<!-- ロード画面用 -->
	<div id="loading" class="is-hide">
		<div class="cv-spinner">
			<span class="spinner"></span>
		</div>
	</div>

	<!-- 読み上げテキスト -->
	<div id="text-recording" title="タイトル" class="remodal" data-remodal-id="modal_r" data-remodal-options="closeOnOutsideClick: false">
		<h4>下記のように生年月日をお答えください。</h4>
		<div class="popup_inner">
			<!-- <p id="text_pop" style="font-size: 16px;">録音完了後の録音完了ボタンを<br />タップしてください。</p> -->
			<p id="text_pop" style="font-size: 16px; font-weight: bold;">私の生年月日は○○○○年△△月××日」です。</p>
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
	<div id="recognition-result" title="タイトル" class="remodal" data-remodal-id="modal_e_relode" data-remodal-options="closeOnOutsideClick: false">
		<h4>ERROR</h4>
		<div class="popup_inner">
			<p id="errorrelode"></p>

			<button class="remodal-confirm" id="errorrelodeOK">OK</button>
		</div>
	</div>
	<!-- マイクERROR -->
	<div id="recognition-result" title="タイトル" class="remodal" data-remodal-id="modal_me">
		<h4>ERROR</h4>
		<div class="popup_inner">
			<p style="font-size: 16px;">音声データがありませんでした。</p>
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
		window.addEventListener('load', (event) => {
			// 位置情報を取得
			if (!navigator.geolocation) { //Geolocation apiがサポートされていない場合
				document.getElementById("latitude").value = ""; //緯度
				document.getElementById("longitude").value = ""; //経度
			} else {
				function success(position) {
					document.getElementById("latitude").value = position.coords.latitude; //緯度
					document.getElementById("longitude").value = position.coords.longitude; //経度
				};

				function error() {
					//エラーの場合
					document.getElementById("latitude").value = ""; //緯度
					document.getElementById("longitude").value = ""; //経度
				};
			}
			navigator.geolocation.getCurrentPosition(success, error); //成功と失敗を判断
		});

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
				document.getElementById('result').innerHTML = "録音ボタンをタップして、<br>ご自身の生年月日を教えてください。";
				document.getElementById('exe_result').innerHTML = "<p>貴方の声を認識し、<br>どなたか特定します。</p>";
				rec_img_elm.src = "../../img/rec_on.png";
			} else {
				document.getElementById('result').innerHTML = "<p>性別を選択してください。</p>";
				document.getElementById('exe_result').innerHTML = "<p>性別を選択して、<br>音声認識を始めてください。</p>";
				rec_img_elm.src = "./img/rec_off.png";
			}
		});


		// ロード画面用
		// document.getElementsByTagName('head')[0]
		// 	.insertAdjacentHTML('beforeend', insertCSS);
		// document.getElementsByTagName('body')[0]
		// 	.insertAdjacentHTML('afterbegin', insertHtml);

		// let loading = document.getElementById('loading')
		// loading.addEventListener("click", function() {
		// 	hideLoading()
		// })

		// btn.addEventListener("click", function() {
		// 	showLoading()
		// })

		function showLoading() {
			document.getElementById('loading').classList.remove('is-hide')
		}

		function hideLoading() {
			document.getElementById('loading').classList.add('is-hide')
		}

		document.getElementById('errorrelodeOK').addEventListener('click', function() {
			window.location.replace('/voice_discover');
		});
	</script>
	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBIO-InW0VdxktB4luJ62EoyZVZJlcfb7A" async defer></script>
</body>

</html>