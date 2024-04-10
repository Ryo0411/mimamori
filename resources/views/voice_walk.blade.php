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
			<a href="/home_walk" class="arrow_s_b"></a>
			<h1>声だけ登録</h1>
			<a href="/home" class=""><img src="{{ asset('img/ico_home.svg') }}" class="btn_home" alt="TOPへ戻る"></a>
		</section>
	</header>

	<section>
		<div class="inner">
			<div>
				<x-alert id="success" type="success" :session="session('exe_msg')" />
				<x-alert id="danger" type="danger" :session="session('err_msg')" />
				<form method="POST" action="{{ route('voiceupdate') }}">
					@csrf
					<div class="block_txt mb2" id="exe_result">
						<p>マッチング精度向上のため声紋を登録します。</p>
					</div>

					<input name="profile_id" type="hidden" id="profile_id" value="{{ @$wanderer_list->profile_id }}"></input>
					<input name="voiceprint_flg" type="hidden" id="voiceprint_flg" value="{{ @$wanderer_list->voiceprint_flg }}"></input>
					<input name="audio_file" type="hidden" id="audio_file" value=""></input>
					<input name="audio_base64" type="hidden" id="audio_base64" value=""></input>
					<input name="voice_length" type="hidden" id="voice_length" value="{{ $voice_length }}"></input>

					<div class="block_rec" id="voiceprint_btn">
						<a id="exe_recording" class="btn_rec">
							<img id="rec_img" src="{{ asset('img/rec_on.png') }}" class="img_rec" alt="録音">
						</a>
					</div>
					<div class="btn_dl">
						<a id="enrollmentDownload" class="soundsample"></a>
					</div>

					<div class="announce" id="result">録音ボタンをタップして、<br>ご自身の生年月日を読み上げて<br>音声を録音してください。</div>
					@if ($errors->has('audio_file'))
					<div class="alert alert-danger">
						{{ "音声を録音してください。" }}</li>
					</div>
					@endif

					<div>
						<div style="margin-top: 20px"><label for="file" style="width: 100%; font-size: 16px">推奨学習</label></div>
						<progress id="file" max="120000" value="{{ $voice_length }}" style="width: 100%; height: 30px"> 0% </progress>
					</div>

					<div class="btn mt2">
						<button type="submit" id="btn_regist" class="btn-red" style="display: none;">登録</button>
					</div>

				</form>
			</div>
			<!-- ロード画面用 -->
			<div id="loading" class="is-hide">
				<div class="cv-spinner">
					<span class="spinner"></span>
				</div>
			</div>
			<!-- 登録完了ポップアップ -->
			<div id="recognition-result" title="タイトル" class="remodal" data-remodal-id="modal_e">
				<h4>完了！</h4>
				<div class="popup_inner">
					<p id="result_pop">音声を追加学習しました。</p>
					<div class="btn_popup">
						<button data-remodal-action="close" class="remodal-confirm" id="okbtn">OK</button>
						<button data-remodal-action="cancel" class="remodal-cancel" onclick="location.href='/home'">TOPへ</button>
					</div>
				</div>
			</div>
			<!-- マイクERROR -->
			<div id="recognition-micerror" title="タイトル" class="remodal" data-remodal-id="modal_me">
				<h4>ERROR</h4>
				<div class="popup_inner">
					<p style="font-size: 16px;">音声データがありませんでした。</p>
					<button data-remodal-action="close" class="remodal-confirm" id="micerr">OK</button>
				</div>
			</div>
			<!-- ERROR結果 -->
			<div id="recognition-error" title="タイトル" class="remodal" data-remodal-id="modal_reset" data-remodal-options="closeOnOutsideClick: false, closeOnEscape: false">
				<h4 style="color:red;">ERROR</h4>
				<div class="popup_inner">
					<p style="font-size: 16px; font-weight:bold">音声学習に問題が発生しました。<br>回復処理をしますので、暫くお待ちください。</p>
				</div>
			</div>
			<!-- 復元処理完了ポップアップ -->
			<div id="recognition-ok" title="タイトル" class="remodal" data-remodal-id="modal_ok">
				<h4>ERROR</h4>
				<div class="popup_inner">
					<p style="font-size: 16px;">復元処理が完了しました。</p>
					<button data-remodal-action="close" class="remodal-confirm" id="micerr">OK</button>
				</div>
			</div>
			<!-- 登録ポップアップ -->
			<div id="recognition-result" title="タイトル" class="remodal" data-remodal-id="modal_confaudio">
				<h4>登録確認</h4>
				<div class="popup_inner">
					<a id="sampleDownload" class="soundsample"></a>
					<p id="result_pop">▶を押すと音声が再生されます。<br>音声に問題がなければ「登録」<br>を押してください。</p>
					<div class="btn_popup">
						<button data-remodal-action="close" class="remodal-confirm" id="remodal-confirm">登録</button>
						<button data-remodal-action="confirm" class="remodal-cancel">キャンセル</button>
					</div>
				</div>
			</div>
		</div>
	</section>

	<div id="text-recording" title="タイトル" class="remodal" data-remodal-id="modal_d" data-remodal-options="closeOnOutsideClick: false">
		<h3>下記のように生年月日をお答えください。</h3>
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
		if (document.getElementById("alertfadeout") != null) {
			console.log(document.getElementById("alertfadeout").textContent);
			if (document.getElementById("alertfadeout").textContent == "音声の追加学習を行いました！") {
				location.href = '#modal_e';
				// location.href = '#modal_reset';

			} else if (document.getElementById("alertfadeout").textContent == "復元処理が完了しました!") {
				location.href = '#modal_ok';
			} else {
				location.href = '#modal_reset';
			}
		}

		if (window.location.hash === '#modal_reset') {
			window.onload = function() {
				// ページが読み込まれた後に実行する処理を記述する
				var form = document.createElement('form');
				form.action = "{{ route('userreset') }}";
				form.method = 'GET';
				document.body.appendChild(form);
				form.submit();
			};
		}

		/* bootstrap alertをx秒後に消す */
		document.getElementById("okbtn").onclick = function() {
			window.setTimeout("$('#alertfadeout').fadeOut()", 1500);
		};

		function butotnClick() {
			showLoading();
		}

		function showLoading() {
			document.getElementById('loading').classList.remove('is-hide')
		}

		function hideLoading() {
			document.getElementById('loading').classList.add('is-hide')
		}

		let button = document.getElementById('btn_regist');
		button.onclick = butotnClick;

		// 音声登録確認用ポップアップ
		document.getElementById('remodal-confirm').addEventListener('click', function() {
			document.getElementById('btn_regist').click();
		});
	</script>

	<!-- Speech SDK reference sdk. -->
	<!-- <script src="{{ asset('js/SpeechSDK/microsoft.cognitiveservices.speech.sdk.bundle.js') }}"></script> -->
	<!-- 音声データ学習用 -->
	<script src="{{ asset('js/recorder.js') }}"></script>
	<script src="{{ asset('js/recording.js') }}"></script>

</body>

</html>