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
					<var data-units="none" data-key="Processes.cpu.{{ $i }}">{{ $process['name'] }}</var>
				</span>
				<span class="value">
					<var data-units="%" data-key="Processes.cpu.{{ $i }}">{{ $process['cpu'] }}</var>%
				</span>
			</li>
@endforeach
		</ul>	

		<h3>@lang('Processes::component.memory')</h3>
		<ul class="field-list">
@foreach ($memory as $i => $process)
			<li class="field">
				<span class="label mono">
					<var data-units="none" data-key="Processes.memory.{{ $i }}">{{ $process['name'] }}</var>
				</span>
				<span class="value">
					<var data-units="%" data-key="Processes.memory.{{ $i }}">{{ $process['memory'] }}%</var>
				</span>
			</li>
@endforeach
		</ul>

	</section>
</article>
