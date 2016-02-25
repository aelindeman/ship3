<article id="UPS" class="component">
	<header>
		<h2><span>@lang('UPS::component.header')</span></h2>
	</header>
	<section>

		<div class="field overview">
			<span class="label">
				<strong><var data-key="UPS.status">{{ ucfirst($status) }}</var></strong>
			</span>
		</div>

		<div class="field">
			<span class="label">@lang('UPS::component.labels.loadpct')</span>
			<span class="value">
				<var data-units="percent" data-key="UPS.loadpct">{{ $loadpct }}%</var>
			</span>
			<div class="meter">
				<div class="series" data-meter-series-key="UPS.loadpct" style="width: {{ $loadpct }}%;"></div>
			</div>
		</div>

		<div class="field">
			<span class="label">@lang('UPS::component.labels.bcharge')</span>
			<span class="value">
				<var data-units="percent" data-key="UPS.bcharge">{{ $bcharge }}%</var>
			</span>
			<div class="meter">
				<div class="series" data-meter-series-key="UPS.bcharge" style="width: {{ $bcharge }}%;"></div>
			</div>
		</div>

		<div class="field">
			<span class="label">@lang('UPS::component.labels.timeleft')</span>
			<span class="value">
				<var data-units="minutes" data-key="UPS.timeleft">{{ $timeleft }} @choice('ship.time.minute', $timeleft)</var>
			</span>
		</div>

		<div class="field">
			<span class="label">@lang('UPS::component.labels.lastxfer')</span>
			<span class="value">
				<var data-units="relativeDateDiff" data-key="UPS.xonbatt" title="{{ $xonbatt }}">{{ $xonbatt ? app('carbon')->parse($xonbatt)->diffForHumans() : app('translator')->get('UPS::component.labels.no-lastxfer') }}</var>
			</span>
@if (strtotime($xonbatt) > 0 and !empty($lastxfer))
			<span class="value">
				<var data-units="lcfirst" data-key="UPS.lastxfer">{{ lcfirst($lastxfer) }}</var>
			</span>
@endif
		</div>

	</section>
</article>

