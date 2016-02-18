<article id="NetAddress" class="component">
	<header>
		<h2><span>@lang('NetAddress::component.header')</span></h2>
	</header>
	<section>

		<div class="field">
			<span class="label">@lang('NetAddress::component.browser')</span>
			<span class="value">
				<var data-units="ip" data-key="NetAddress.browser">{{ $browser }}</var>
			</span>
		</div>

		<div class="field">
			<span class="label">@lang('NetAddress::component.local')</span>
			<span class="value">
				<var data-units="ip" data-key="NetAddress.internal">{{ $local }}</var>
			</span>
		</div>

		<div class="field">
			<span class="label">@lang('NetAddress::component.remote') IPv4</span>
			<span class="value">
				<var data-units="ip" data-key="NetAddress.external-ipv4">{{ $ipv4 or '&mdash;' }}</var>
			</span>
		</div>

		<div class="field">
			<span class="label">@lang('NetAddress::component.remote') IPv6</span>
			<span class="value">
				<var data-units="ip" data-key="NetAddress.external-ipv6">{{ $ipv6 or '&mdash;' }}</var>
			</span>
		</div>
	</section>
</article>
