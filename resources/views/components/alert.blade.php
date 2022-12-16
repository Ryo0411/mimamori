<!-- ログアウトのアラート表示 -->
@if ($session)
<div id="alertfadeout" class="alert alert-{{ $type }}">
    {{ $session }}
</div>
@endif