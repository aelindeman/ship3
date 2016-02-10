<div class="graph color-scheme-purple" data-graph="NetTraffic"></div>
<ul class="legend half color-scheme-purple">
	<li class="legend-field">
		<span class="legend-icon"></span>
		<span class="legend-title">@lang('NetTraffic::component.transmit')</span>
		<span class="legend-value"><var data-key="NetTraffic.tx">{{ bytesize($tx) }}</var></span>
	</li>
	<li class="legend-field">
		<span class="legend-icon"></span>
		<span class="legend-title">@lang('NetTraffic::component.recieve')</span>
		<span class="legend-value"><var data-key="NetTraffic.rx">{{ bytesize($rx) }}</var></span>
	</li>
</ul>
