<h4>@lang('Processes::component.cpu')</h4>
<ul class="legend">
@foreach ($cpu as $i => $process)
	<li class="legend-field">
		<span class="legend-title"><var data-key="Processes.cpu.{{ $i }}">{{ $process['name'] }}</var></span>
		<span class="legend-subitem"><var data-key="Processes.cpu.{{ $i }}">{{ $process['user'] }}</var></span>
		<span class="legend-value"><var data-key="Processes.cpu.{{ $i }}">{{ $process['cpu'] }}</var>%</span>
	</li>
@endforeach
</ul>

<h4>@lang('Processes::component.memory')</h4>
<ul class="legend">
@foreach ($memory as $i => $process)
	<li class="legend-field">
		<span class="legend-title"><var data-key="Processes.memory.{{ $i }}">{{ $process['name'] }}</var></span>
		<span class="legend-subitem"><var data-key="Processes.memory.{{ $i }}">{{ $process['user'] }}</var></span>
		<span class="legend-value"><var data-key="Processes.memory.{{ $i }}">{{ $process['memory'] }}</var>%</span>
	</li>
@endforeach
</ul>
