<!DOCTYPE html>
<html lang="ja">
<head>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width,initial-scale=1">
		<meta name=”description” content=”徘徊マッチング”/>
    	<title>徘徊マッチング</title>
		<!-- script -->
        <script src="{{ asset('js/app.js') }}" defer></script>
        <!-- styles -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet"></link><!-- bootsutorap用のCSS -->
        <link href="{{ asset('css/style.css') }}" rel="stylesheet"></link>
</head>
<body>
	<header>
		<section>
			<h1><img src="{{ asset('/img/logo.svg') }}" alt="徘徊マッチング"></h1>
		</section>
	</header>
    
	
	<section>
		<div class="inner">
			<div class="block_result">
				<img src="{{ asset('/img/result_80.png') }}">
				<div class="txt_result">
				<h3>ユーザ1</h3>
				80%の確率でユーザ1と一致しました。
				</div>
			</div>
		</div>
	</section>
	
	<section>
		<div class="inner">
			<h2>1.音声サンプル登録</h2>
			<div class="btn">
				<button id="button" class="btn-common">ユーザ選択</button>
			</div>

			<div class="box_user">
				<p>ユーザを選択して、「録音開始」ボタンをタップしてください。</p>
				<ul class="list_user">
					<li>
						<button class="btn-user">1(3)</button>
					</li>
					<li>
						<button class="btn-user">2(3)</button>
					</li>
					<li>
						<button class="btn-user">3(3)</button>
					</li>
					<li>
						<button class="btn-user">4(3)</button>
					</li>
					<li>
						<button class="btn-user">5(3)</button>
					</li>
					<li>
						<button class="btn-user">6(3)</button>
					</li>
					<li>
						<button class="btn-user">7(3)</button>
					</li>
					<li>
						<button class="btn-user">8(3)</button>
					</li>
					<li>
						<button class="btn-user">9(3)</button>
					</li>
					<li>
						<button class="btn-user">10(3)</button>
					</li>
				</ul>
			</div>
		</div>
	</section>
	
	<section>
		<div class="inner">
			<h2>2.音声認識</h2>
			<div class="btn">
				<button id="button" class="btn-common">録音開始</button>
			</div>
			<div class="btn_dl">
				<a href="#">ダウンロード</a>
			</div>
		</div>
	</section>
	
	<section>
		<div class="inner">
			<h2>3.個別プロファイルリセット</h2>
			<div class="btn">
				<button id="button" class="btn-common">リセット</button>
			</div>
		</div>
	</section>
	
	<section>
		<div class="inner">
			<h2>4.全プロファイルリセット</h2>
			<div class="btn">
				<button id="button" class="btn-common">リセット</button>
			</div>
		</div>
	</section>
	
	<section>
		<div class="inner">
			<h2>5.ログ表示</h2>
			<div class="btn">
				<button id="button" class="btn-common">ログを表示</button>
			</div>
		</div>
	</section>
	
	
	<div class="footer">
		<div class="footer_ver">Ver. 1.0</div>
		<div class="footer_copy">Provided by Nippontect Systems Co.,Ltd</div>
	</div>
    
</body>
</html>
