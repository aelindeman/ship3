<div class="graph color-scheme-green" data-graph="Memory"></div>

<?php
$usedPct = $used / $total * 100;
$cachedPct = $cached / $total * 100;
$freePct = ($free - $cached) / $total  * 100;
?>
<div class="meter">
	<header class="meter-header">
		<span class="meter-header-text">@lang('Memory::component.labels.total')</span>
		<var data-key="Memory.total" data-bytes>{{ bytesize($total, 0, '') }}</var><span class="units">@lang('ship.units.bytes.abbr')</span>
	</header>
	<section class="meter-bar">
		<div class="meter-series" title="@lang('Memory::component.labels.used')" style="width: {{ $usedPct }}%">
			<span class="meter-series-label sr-only"><var data-key="Memory.usedPct">{{ $usedPct }}</var>% @lang('Memory::component.labels.used')</span>
		</div>
		<div class="meter-series" title="@lang('Memory::component.labels.cached')" style="width: {{ $cachedPct }}%">
			<span class="meter-series-label sr-only"><var data-key="Memory.cachedPct">{{ $cachedPct }}</var>% @lang('Memory::component.labels.cached')</span>
		</div>
	</section>
</div>

<ul class="legend half">
	<li class="legend-field">
		<span class="legend-item"><span class="legend-icon"></span>@lang('Memory::component.labels.used')</span>
		<span class="legend-value"><var data-key="Memory.usedPct" data-title-key="Memory.used" title="{{ bytesize($used, 0, '', 'G') }}">{{ round($usedPct) }}</var>%</span>
	</li>
	<li class="legend-field">
		<span class="legend-item"><span class="legend-icon"></span>@lang('Memory::component.labels.cached')</span>
		<span class="legend-value"><var data-key="Memory.cachedPct" data-title-key="Memory.cached" title="{{ bytesize($cached, 0, '', 'G') }}">{{ round($cachedPct) }}</var>%</span>
	</li>
	<li class="legend-field">
		<span class="legend-item"><span class="legend-icon for-remainder"></span>@lang('Memory::component.labels.free')</span>
		<span class="legend-value"><var data-key="Memory.freePct" data-title-key="Memory.free" title="{{ bytesize($free, 0, '', 'G') }}">{{ round($freePct) }}</var>%</span>
	</li>
</ul>
