@extends ('layouts.master')
@section ('title', config('app.title', app('translator')->get('ship.app')))

@section ('content')
<header class="primary-header">
	<h1>{{ config('app.title', app('translator')->get('ship.app')) }}</h1>
	<nav class="primary-toolbar">
		<ul>
			<li class="separator">
				<div class="input-container">
					<label class="label-before" for="graph-width">@lang('ship.header.toolbar.graph-width')</label>
					<select id="graph-width" class="input">
@foreach ([15, 30, 45, 60] as $m)
						<option value="{{ $m }}M"{{ config('app.graph-width') == $m.'M' ? ' selected' : '' }}>{{ $m }} @choice('ship.time.minute', $m)</option>
@endforeach
@foreach ([2, 3, 6, 12, 24] as $h)
						<option value="{{ $h }}H"{{ config('app.graph-width') == $h.'H' ? ' selected' : '' }}>{{ $h }} @choice('ship.time.hour', $h)</option>
@endforeach
					</select>
				</div>
			</li><li>
				<button id="theme-toggle" data-labels="@lang('ship.header.toolbar.dark-mode.enable')|@lang('ship.header.toolbar.dark-mode.disable')">@lang('ship.header.toolbar.dark-mode.'.(config('app.dark-mode') ? 'disable' : 'enable'))</button>
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
@endsection

@section ('scripts')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/chartist/0.9.5/chartist.min.js"></script>
<script type="text/javascript" src="{{ url('ship.min.js') }}"></script>
@endsection

@section ('styles')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/chartist/0.9.5/chartist.min.css">
@endsection
