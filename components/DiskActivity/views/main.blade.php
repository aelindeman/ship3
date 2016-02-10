<div class="graph color-scheme-red" data-graph="DiskActivity"></div>
<ul class="legend half color-scheme-red">
	<li class="legend-field">
		<span class="legend-name"><span class="legend-icon"></span>@lang('DiskActivity::component.read')</span>
		<span class="legend-value"><var data-key="DiskActivity.read">{{ bytesize($read) }}</var></span>
	</li>
	<li class="legend-field">
		<span class="legend-name"><span class="legend-icon"></span>@lang('DiskActivity::component.write')</span>
		<span class="legend-value"><var data-key="DiskActivity.write">{{ bytesize($write) }}</var></span>
	</li>
</ul>
