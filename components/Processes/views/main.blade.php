<article id="Processes" class="component">
	<header>
		<h2><span>@lang('Processes::component.header')</span></h2>
	</header>
	<section>

		<h3>@lang('Processes::component.cpu')</h3>
		<ul class="field-list">
@foreach ($cpu as $i => $process)
			<li class="field">
				<span class="label mono">
					<var data-key="Processes.cpu.{{ $i }}.name">{{ $process['name'] }}</var>
				</span>
				<span class="value">
					<var data-units="percent" data-key="Processes.cpu.{{ $i }}.cpu">{{ $process['cpu'] }}%</var>
				</span>
			</li>
@endforeach
		</ul>

		<h3>@lang('Processes::component.memory')</h3>
		<ul class="field-list">
@foreach ($memory as $i => $process)
			<li class="field">
				<span class="label mono">
					<var data-key="Processes.memory.{{ $i }}.name">{{ $process['name'] }}</var>
				</span>
				<span class="value">
					<var data-units="percent" data-key="Processes.memory.{{ $i }}.memory">{{ $process['memory'] }}%</var>
				</span>
			</li>
@endforeach
		</ul>

	</section>
</article>
