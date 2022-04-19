<!DOCTYPE html>
<html lang="ja">
<head>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width,initial-scale=1">
		<meta name=”description” content=”あんしん見守り”/>
    	<title>あんしん見守り</title>
        <!-- script -->
        <script src="{{ asset('js/app.js') }}" defer></script>
        <!-- styles -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet"></link><!-- bootsutorap用のCSS -->
        <link href="{{ asset('css/style.css') }}" rel="stylesheet"></link>
    </head>
<body>
	<header>
		<section>
			<h1><img src="{{ asset('img/logo.svg') }}" alt="あんしん見守り"></h1>
		</section>
	</header>

	<section>
		<div class="inner">
			<div class="block_50vh">
                <!-- ログイン成功時のアラート表示 -->
				<x-alert type="success" :session="session('login_success')"/>
                <!-- @if (session('login_success'))
                    <div class="alert alert-success">
                        {{ session('login_success') }}
                    </div>
                @endif -->
				<div class="btn">
					<button id="button" class="btn-walk" onclick="location.href='/home_walk'">徘徊者</button>
				</div>
				<div class="btn">
					<button id="button" class="btn-discover" onclick="location.href='/home_discover'">発見者</button>
				</div>
				<div class="btn">
					<form action="{{ route('logout') }}" method="POST">
						@csrf
						<button class="btn btn-walk">ログアウト</button>
					</form>
				</div>
			</div>
		</div>
	</section>

	<section>
		<div class="inner">
			<img src="{{ asset('img/fujita_logo.png') }}" class="logo_fujita" alt="FUJITA">
		</div>
	</section>

	<footer class="footer">
		<div class="footer_ver">Ver. 1.0</div>
		<div class="footer_copy">Provided by Nippontect Systems Co.,Ltd</div>
	</footer>

</body>
</html>
