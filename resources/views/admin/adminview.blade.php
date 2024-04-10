<!DOCTYPE html>
<html lang="ja">

<head>

	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width,initial-scale=1" viewport-fit="cover">
		<meta name=”description” content=”あんしん見守り” />
		<!-- ポップアップ用UI -->
		<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/remodal/1.0.5/remodal.min.css">
		<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/remodal/1.0.5/remodal-default-theme.min.css">
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/remodal/1.0.5/remodal.min.js"></script>
		<title>あんしん見守り</title>
		<!-- script -->
		<script src="{{ asset('js/app.js') }}" defer></script>
		<!-- styles -->
		<link href="{{ asset('css/app.css') }}" rel="stylesheet">
		</link><!-- bootsutorap用のCSS -->
		<link href="{{ asset('css/style.css') }}" rel="stylesheet">
		</link>
		<script src="{{ asset('js/fv.js') }}"></script>
	</head>

<body class="discoverer">
	<wrapper class="fv">
		<header>
			<a href="/admin/home" class="arrow_s_b"></a>
			<h1>発見者一覧</h1>
		</header>

		<section>
			<div class="inner">
				<!--<div class="block_50vh">-->
				<div>
					<table class="table">
						<thead>
							<tr class="ttl">
								<th>ID</th>
								<th>迷子者名</th>
								<th>緊急連絡先</th>
								<th>発見者名</th>
								<th>発見者日時</th>
								<th></th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							@foreach ($wanderer_lists as $wanderer_list)
							<tr>
								<td>{{ $wanderer_list->id }}</td>
								<td>{{ $wanderer_list->wanderer_name }}</td>
								<td>{{ $wanderer_list->emergency_tel }}</td>
								<td>{{ $wanderer_list->discover_name }}</td>
								<td>{{ $wanderer_list->wanderer_time }}</td>
								<td><a href="/admin/discover/{{ $wanderer_list->id }}"><button type="button" class="btn btn-primary">発見</button></a></td>
								<td><a href="/admin/email/{{ $wanderer_list->id }}"><button type="button" class="btn btn-primary">Mail送信</button></a></td>
								<!-- <form method="post" action="/discover/{{ $wanderer_list->id }}">
									@csrf
									<input id="discover_button" class="btn btn-primary" type="submit" value="発見">
								</form> -->
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</section>

		<!-- メール送信ERROR -->
		<div id="recognition-result" title="タイトル" class="remodal" data-remodal-id="modal_me">
			<h4>失敗</h4>
			<div class="popup_inner">
				<p style="font-size: 16px;">メール登録が済んでいないユーザです。</p>
				<button data-remodal-action="cancel" class="remodal-cancel">OK</button>
			</div>
		</div>
		<!-- メール送信完了 -->
		<div id="recognition-result" title="タイトル" class="remodal" data-remodal-id="modal_ok">
			<h4>完了！</h4>
			<div class="popup_inner">
				<p id="result_pop">メールの送信が完了しました。</p>
				<div class="btn_popup">
					<button data-remodal-action="close" class="remodal-confirm" id="okbtn">OK</button>
				</div>
			</div>
		</div>

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