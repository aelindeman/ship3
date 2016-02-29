@extends ('layouts.master')
@section ('title', config('ship.title', app('translator')->get('ship.app')))

@section ('content')
<header class="primary-header">
	<h1>{{ config('ship.title', app('translator')->get('ship.app')) }}</h1>
	<nav class="primary-toolbar">
		<ul>
@if($components->count() > 0 and $components->has('Info'))
	@if (view()->exists('Info::uptime'))
			<li class="separator break">
				@include ('Info::uptime', $components->get('Info'))
			</li>
	@endif
@endif
			<li class="separator">
				<div class="input-container">
					<label class="label-before" for="time-period">@lang('ship.header.toolbar.time-period')</label>
					<select id="time-period" class="input">
@foreach ([15, 30, 60] as $m)
						<option value="PT{{ $m }}M"{{ config('ship.period') == 'PT'.$m.'M' ? ' selected' : '' }}>@lang('ship.time.relative.previous', ['value' => $m, 'units' => app('translator')->choice('ship.time.minute', $m)])</option>
@endforeach
@foreach ([2, 3, 6, 12, 24] as $h)
						<option value="PT{{ $h }}H"{{ config('ship.period') == 'PT'.$h.'H' ? ' selected' : '' }}>@lang('ship.time.relative.previous', ['value' => $h, 'units' => app('translator')->choice('ship.time.hour', $h)])</option>
@endforeach
					</select>
				</div>
			</li><li>
				<button id="theme-toggle" data-labels="@lang('ship.header.toolbar.dark-mode.enable')|@lang('ship.header.toolbar.dark-mode.disable')">@lang('ship.header.toolbar.dark-mode.'.(config('ship.dark-mode') ? 'disable' : 'enable'))</button>
			</li>
		</ul>
	</nav>
</header>

<div class="grid">
@if ($components->count() > 0)
	@foreach ($components->sortBy('order') as $name => $data)
		@if (view()->exists($name.'::main'))
			@include ($name.'::main', $data)
		@endif
	@endforeach
@else
	<article class="component">
		<header>
			<h2>@lang('ship.errors.no-components.header')</h2>
		</header>
		<p>@lang('ship.errors.no-components.description')</p>
	</article>
@endif
</div>

<footer class="primary-footer">
	<p>@lang('ship.translation-credit')</p>
	<p>@lang('ship.footer.slogan') {{ $app->version() }} - <a href="http://ael.me/ship/">ael.me/ship</a></p>
</footer>

<div id="loading-indicator">@lang('ship.loading')</div>
@endsection

@section ('meta')
<meta name="format-detection" content="telephone=no">
@endsection

@section ('styles')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/chartist/0.9.5/chartist.min.css">
@endsection

@section ('scripts')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/chartist/0.9.5/chartist.min.js"></script>
<script type="text/javascript" src="{{ url('ship.min.js') }}"></script>
<script type="text/javascript" src="{{ url('init') }}"></script>
@endsection
