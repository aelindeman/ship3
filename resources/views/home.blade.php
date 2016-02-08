@extends ('layouts.master')
@section ('title', config('app.title', app('translator')->get('ship.app')))

@section ('content')
<header class="primary-header row">
	<h1>{{ config('app.title', app('translator')->get('ship.app')) }}</h1>
	<nav class="primary-toolbar">
		<ul>
			<li class="separator">
				<div class="input-container">
					<label class="label-before" for="graph-width">@lang('ship.header.toolbar.graph-width')</label>
					<select id="graph-width" class="input">
						@foreach ([3, 6, 12, 24, 48] as $h)
						<option value="{{ $h }}"{{ config('app.graph-width') == $h ? ' selected' : '' }}>{{ $h }} @choice('ship.time.hour', $h)</option>
						@endforeach
					</select>
				</div>
			</li><li>
				<button id="theme-toggle" data-labels="@lang('ship.header.toolbar.dark-mode.enable')|@lang('ship.header.toolbar.dark-mode.disable')">@lang('ship.header.toolbar.dark-mode.'.(config('app.dark-mode') ? 'disable' : 'enable'))</button>
			</li>
		</ul>
	</nav>
</header>

<div class="components">
	<div class="row">
	@if ($components->count() > 0)
	@foreach ($components as $name => $data)
		@if (view()->exists($name.'::main'))
			<section id="ship-{{ strtolower($name) }}-component" class="four columns component {{ strtolower($name) }}-component">
				<article>
					<header>
						<h3>@lang($name.'::component.header')</h3>
					</header>
					@include ($name.'::main', $data)
				</article>
			</section>
		@endif
	@endforeach
	@else
	<section id="ship-no-components" class="four columns offset-by-four component">
		<article>
			<header>
				<h2>@lang('ship.errors.no-components.header')</h2>
			</header>
			<p>@lang('ship.errors.no-components.description')</p>
		</article>
	</section>
	@endif
	</div>
</div>

<footer class="primary-footer row">
	<p>@lang('ship.translation-credit')</p>
	<p>@lang('ship.footer.slogan') {{ $app->version() }} - <a href="http://ael.me/ship/">ael.me/ship</a></p>
</footer>
@endsection
