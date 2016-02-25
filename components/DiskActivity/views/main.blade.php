<article id="DiskActivity" class="component theme-red">
	<header>
		<h2><span>@lang('DiskActivity::component.header')</span></h2>
	</header>
	<div data-graph="DiskActivity" data-graph-yaxis-units="kb/s" class="graph"></div>
	<section>

		<div class="field">
			<span class="label"><span class="icon series-a"></span>@lang('DiskActivity::component.read')</span>
			<span class="value">
				<var data-units="kb" data-key="DiskActivity.read">{{ bytesize($read) }}</var>
			</span>
		</div>

		<div class="field">
			<span class="label"><span class="icon series-b"></span>@lang('DiskActivity::component.write')</span>
			<span class="value">
				<var data-units="kb" data-key="DiskActivity.write">{{ bytesize($write) }}</var>
			</span>
		</div>

	</section>
</article>
