<article id="Memory" class="component theme-green">
	<header>
		<h2><span>@lang('Memory::component.header')</span></h2>
	</header>
	<div data-graph="Memory" data-graph-yaxis-units="kb" class="graph"></div>
	<section>

		<div class="field">
			<span class="label"><span class="icon series-a"></span>@lang('Memory::component.labels.used')</span>
			<span class="value">
				<var data-units="percent" data-key="Memory.usedPct">{{ round($used / $total * 100) }}%</var>
				<span> of </span>
				<var data-units="kb" data-key="Memory.total">{{ bytesize($total) }}</var>
			</span>
		</div>

	</section>
</article>
