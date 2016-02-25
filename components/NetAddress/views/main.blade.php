<article id="NetAddress" class="component">
	<header>
		<h2><span>@lang('NetAddress::component.header')</span></h2>
	</header>
	<section>

		<div class="field">
			<span class="label">@lang('NetAddress::component.browser')</span>
			<span class="value mono">
				<var data-key="NetAddress.browser">{{ $browser or '&mdash;' }}</var>
			</span>
		</div>

		<div class="field">
			<span class="label">@lang('NetAddress::component.local')</span>
			<span class="value mono">
				<var data-key="NetAddress.local">{{ $local or '&mdash;' }}</var>
			</span>
		</div>

		<div class="field">
			<span class="label">@lang('NetAddress::component.remote') IPv4</span>
			<span class="value mono">
				<var data-key="NetAddress.ipv4">{{ $ipv4 or '&mdash;' }}</var>
			</span>
		</div>

		<div class="field">
			<span class="label">@lang('NetAddress::component.remote') IPv6</span>
			<span class="value mono">
				<var data-key="NetAddress.ipv6">{{ $ipv6 or '&mdash;' }}</var>
			</span>
		</div>
	</section>
</article>
