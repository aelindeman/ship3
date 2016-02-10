<ul class="legend mono-values">
	<li class="legend-field">
		<span class="legend-name">@lang('NetAddress::component.browser')</span>
		<span class="legend-value"><var data-key="NetAddress.browser">{{ $browser or app('translator')->get('NetAddress::component.unknown') }}</var></span>
	</li>
	<li class="legend-field">
		<span class="legend-name">@lang('NetAddress::component.local')</span>
		<span class="legend-value"><var data-key="NetAddress.local">{{ $local or app('translator')->get('NetAddress::component.unknown') }}</var></span>
	</li>
	<li class="legend-field">
		<span class="legend-name">@lang('NetAddress::component.ipv4')</span>
		<span class="legend-value"><var data-key="NetAddress.ipv4">{{ $ipv4 or app('translator')->get('NetAddress::component.unavailable') }}</var></span>
	</li>
	<li class="legend-field">
		<span class="legend-name">@lang('NetAddress::component.ipv6')</span>
		<span class="legend-value"><var data-key="NetAddress.ipv6">{{ $ipv6 or app('translator')->get('NetAddress::component.unavailable') }}</var></span>
	</li>
</ul>
