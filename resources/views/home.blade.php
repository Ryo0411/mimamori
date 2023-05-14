<!DOCTYPE html>
<html lang="ja">

<head>

	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width,initial-scale=1" viewport-fit="cover">
		<meta name=”description” content=”あんしん見守り” />
		<title>あんしん見守り</title>
		<!-- ポップアップ用UI -->
		<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/remodal/1.0.5/remodal.min.css">
		<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/remodal/1.0.5/remodal-default-theme.min.css">
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/remodal/1.0.5/remodal.min.js"></script>
		<!-- script -->
		<script src="{{ asset('js/app.js') }}" defer></script>
		<!-- styles -->
		<link href="{{ asset('css/app.css') }}" rel="stylesheet">
		</link><!-- bootsutorap用のCSS -->
		<link href="{{ asset('css/style.css') }}" rel="stylesheet">
		</link>
		<script src="{{ asset('js/fv.js') }}"></script>
	</head>

<body>
	<wrapper class="fv">
		<header>
			<section>
				<h1><img src="{{ asset('img/logo.svg') }}" alt="あんしん見守り"></h1>
			</section>
		</header>

		<section>
			<div class="inner">
				<h2 class="sectTit">ご家族情報</h2>
				<div class="block_txt">
					<p>{{ $exe }}</p>
				</div>
				<!--<div class="block_50vh">-->
				<div>
					<!-- ログイン成功時のアラート表示 -->
					<x-alert type="success" :session="session('login_success')" />
					<div class="btn">
						<button id="button" class="btn-walk" onclick="location.href='/home_walk'">徘徊者</button>
					</div>
					<div class="btn">
						<button id="button" class="btn-discover" onclick="location.href='/home_discover'">発見者</button>
					</div>
					<div class="btn">
						<button id="button" class="btn-mic" onclick="location.href='/voice_test'">動作チェック</button>
					</div>
					<div class="btn">
						<form action="{{ route('logout') }}" method="POST">
							@csrf
							<button class="btn btn-logout">ログアウト</button>
						</form>
					</div>
				</div>
				<!-- 復元処理完了ポップアップ -->
				<div id="recognition-ok" title="タイトル" class="remodal" data-remodal-id="modal_error">
					<h4>ERROR</h4>
					<div class="popup_inner">
						<p style="font-size: 16px;">復元処理が完了しました。</p>
						<button data-remodal-action="close" class="remodal-confirm" id="micerr">OK</button>
					</div>
				</div>
			</div>
		</section>

		<section>
			<div class="inner t_c">
				<img src="{{ asset('img/fujita_logo.png') }}" class="logo_fujita" alt="FUJITA">
			</div>
		</section>

		<footer class="footer">
			<div class="footer_ver">Ver. 2.1</div>
			<div class="footer_copy">Provided by Nippontect Systems Co.,Ltd</div>
		</footer>
	</wrapper>

</body>

</html>