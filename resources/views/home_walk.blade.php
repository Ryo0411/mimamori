<!DOCTYPE html>
<html lang="ja">

<head>

	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width,initial-scale=1" viewport-fit="cover">
		<meta name=”description” content=”迷子者ホーム/あんしん見守り” />
		<!-- ポップアップ用UI -->
		<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/remodal/1.0.5/remodal.min.css">
		<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/remodal/1.0.5/remodal-default-theme.min.css">
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/remodal/1.0.5/remodal.min.js"></script>
		<title>迷子者ホーム/あんしん見守り</title>
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
		<h1>迷子者ホーム</h1>
		<a href="/home" class=""><img src="{{ asset('img/ico_home.svg') }}" class="btn_home" alt="TOPへ戻る"></a>
	</header>

	<section>
		<div class="inner">
			<h2 class="sectTit">ご家族情報</h2>
			<div class="block_txt">
				<p>{{ $exe }}</p>
			</div>

			<div>
				<!--<div class="block_50vh">-->
				<div class="btn">
					<button id="button" class="btn-walk" onclick="location.href='/register_walk'">情報登録</button>
				</div>
				<div class="btn" {{$status}}>
					<button id="button" class="btn-walk" onclick="location.href='/voice_walk'">声だけ登録</button>
				</div>
				<div class="btn" id="voice_list" {{$status}}>
					<button id="button" class="btn-walk" onclick="location.href='/voice_list'">音声一覧</button>
				</div>
				<div class="btn" id="wanderer" {{$status}}>
					<button id="exebutton" class="btn-red" onclick="location.href='/home_walk/wanderer'">捜索開始</button>
				</div>
				<input name="voiceprint_flg" type="hidden" id="voiceprint_flg" value="{{ $exe }}"></input>
				<!-- <div class="btn" {{$status}}>
					<form method="post" action="/home_walk/discover">
						@csrf
						<input id="discover_button" class="btn-red" type="submit" value="発見">
					</form>
				</div> -->
				<!-- 発見ポップアップ -->
				<div id="recognition-result" title="タイトル" class="remodal" data-remodal-id="modal_d">
					<h4>発見されました！</h4>
					<div class="popup_inner">
						<p id="result_pop">登録いただいた緊急連絡先に<br>ご連絡致します。</p>
						<div class="btn_popup">
							<button data-remodal-action="close" class="remodal-confirm">OK</button>
						</div>
					</div>
				</div>
				<!-- リセット確認ポップアップ -->
				<div id="recognition-result" title="タイトル" class="remodal" data-remodal-id="modal_reset">
					<h4>リセット確認</h4>
					<div class="popup_inner">
						<p id="result_pop">登録頂いたご家族情報を<br>全て削除してしまってよろしいですか？</p>
						<div class="btn_popup">
							<button data-remodal-action="close" class="remodal-confirm" onclick="location.href='/wanderer/reset'">削除</button>
							<button data-remodal-action="cancel" class="remodal-cancel">キャンセル</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="resetbtn" {{$status}}>
			<button id="exebutton" class="btn-red-reset" onclick="location.href='#modal_reset'">リセット</button>
		</div>
	</section>

	<footer class="footer">
		<div class="footer_ver">Ver. 2.1</div>
		<div class="footer_copy">Provided by Nippontect Systems Co.,Ltd</div>
	</footer>

</body>

<script>
	var input = document.getElementById('voiceprint_flg');
	var value = input.getAttribute('value');

	//捜索対象に選択中にボタンを表示させる。
	if ("{{ $discoverflg }}" == 1) {
		location.href = '#modal_d';
	}
	if (value == "捜索対象に選択中です。" || value == "発見されました！") {
		document.getElementById("exebutton").innerText = "捜索解除";
	}
</script>

</html>