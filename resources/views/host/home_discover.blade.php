<!DOCTYPE html>
<html lang="ja">
<head>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width,initial-scale=1" viewport-fit="cover">
		<meta name=”description” content=”発見者ホーム/あんしん見守り”/>
    	<title>発見者ホーム/あんしん見守り</title>
		<!-- script -->
        <script src="{{ asset('js/app.js') }}" defer></script>
        <!-- styles -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet"></link><!-- bootsutorap用のCSS -->
        <link href="{{ asset('css/style.css') }}" rel="stylesheet"></link>
</head>
<body>
	<header>
		<a href="/home" class="arrow_s_b"></a>
		<h1>発見者ホーム</h1>
		
	</header>
	
	<section>
		<div class="inner">
			<div class="block_40vh">
				<div class="btn mb4">
					<button id="button" class="btn-discover" onclick="location.href='/register_discover'">情報登録</button>
				</div>
				<div class="btn">
					<button id="button" class="btn-discover" onclick="location.href='/voice_discover'">徘徊者声掛け</button>
				</div>
			</div>
		</div>
	</section>
	
	
	<footer class="footer">
		<div class="footer_ver">Ver. 1.0</div>
		<div class="footer_copy">Provided by Nippontect Systems Co.,Ltd</div>
	</footer>
    
</body>
</html>