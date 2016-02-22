<article id="NetTraffic" class="component">
	<header>
		<h2><span>@lang('NetTraffic::component.header')</span></h2>
	</header>
	<div data-graph="NetTraffic" class="graph"></div>
	<section>

		<div class="field">
			<span class="label"><span class="icon series-a"></span>@lang('NetTraffic::component.transmit')</span>
			<span class="value">
				<var data-units="kB/s" data-key="NetTraffic.tx">{{ bytesize($tx) }}</var>
			</span>
		</div>

		<div class="field">
			<span class="label"><span class="icon series-b"></span>@lang('NetTraffic::component.recieve')</span>
			<span class="value">
				<var data-units="kB/s" data-key="NetTraffic.rx">{{ bytesize($rx) }}</var>
			</span>
		</div>

	</section>
</article>
