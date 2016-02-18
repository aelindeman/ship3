<article id="Memory" class="component">
	<header>
		<h2><span>@lang('Memory::component.header')</span></h2>
	</header>
	<section>

		<div data-graph="Memory" class="graph"></div>

		<div class="field">
			<span class="label"><span class="icon series-a"></span>@lang('Memory::component.labels.used')</span>
			<span class="value">
				<var data-units="%" data-key="Memory.usedPct">{{ round($used / $total * 100) }}%</var>
				<span> of </span>
				<var data-units="kB" data-key="Memory.total">{{ bytesize($total) }}</var>
			</span>
		</div>

	</section>
</article>
