<article id="LoadAverage" class="component theme-blue">
	<header>
		<h2><span>@lang('LoadAverage::component.header')</span></h2>
	</header>
	<div data-graph="LoadAverage" data-graph-yaxis-units="staticTwoDecimal" class="graph"></div>
	<section>

		<div class="field">
			<span class="label"><span class="icon series-a"></span>@lang('LoadAverage::component.load')</span>
			<span class="value">
				<var data-units="none" data-key="LoadAverage.five">{{ sprintf('%0.2f', $five) }}</var>
			</span>
		</div>

	</section>
</article>
