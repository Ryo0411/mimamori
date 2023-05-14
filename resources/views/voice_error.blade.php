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
			<h1>ERROR</h1>
			<a href="/home" class=""><img src="{{ asset('img/ico_home.svg') }}" class="btn_home" alt="TOPへ戻る"></a>
		</section>
	</header>

	<section>
		<div class="inner">
			<div>
				@csrf
				<div class="block_txt mb2" style="display: none;">
					<p>音声録音に対応しているかチェックを行います。<br>マイクのマークをタップしてください。</p>
				</div>


				<div class="block_txt mb2">
					<!-- <p>何らかの理由により復元処理に<br>失敗しました。<br>管理者に問い合わせを行い、<br>確認お願い致します。<br>又は徘徊者ホームからリセットボタンを押し登録情報をリセットしてください</p> -->
					<p>何らかの理由により復元処理が<br>失敗しました。<br>管理者に問い合わせを行い、<br>確認お願い致します。</p>
				</div>
			</div>

		</div>
		</div>
	</section>

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