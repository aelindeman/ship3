<div class="graph color-scheme-blue" data-graph="LoadAverage"></div>
<ul class="legend color-scheme-blue">
	<li class="legend-field">
		<span class="legend-title header">@lang('LoadAverage::component.load')</span>
		<span class="legend-value">
			<var style="margin-right: 0.5em;" data-key="LoadAverage.one">{{ sprintf('%0.2f', $one) }}</var>
			<var style="margin-right: 0.5em;" data-key="LoadAverage.five">{{ sprintf('%0.2f', $five) }}</var>
			<var data-key="LoadAverage.fifteen">{{ sprintf('%0.2f', $fifteen) }}</var>
		</span>
	</li>
</ul>
