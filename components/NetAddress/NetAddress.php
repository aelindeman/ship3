<?php

namespace App\Components;
use App\Behaviors\Cacheable;
use App\Models\Component;

class NetAddress extends Component implements Cacheable
{
	const IPv4_CACHE_KEY = 'components.NetAddress.IPv4';
	const IPv6_CACHE_KEY = 'components.NetAddress.IPv6';

	protected $table = false;

	public static function flush()
	{
		app('cache')->forget(static::IPv4_CACHE_KEY);
		app('cache')->forget(static::IPv6_CACHE_KEY);
		return app('cache')->flush();
	}

	public static function fetch()
	{
		$expires = config('components.NetAddress.config.cache', 1440);
		$ipv4 = null; $ipv6 = null;

		// get IPv4 address
		if (config('components.NetAddress.config.ipv4', true)) {
			try {
				if (app('cache')->has(static::IPv4_CACHE_KEY)) {
					// use cache if it's there
					$ipv4 = app('cache')->get(static::IPv4_CACHE_KEY);
				} else {
					// cache might be out of data or doesn't exist
					$ipv4 = trim(file_get_contents('http://ipv4.icanhazip.com'));
					app('cache')->put(static::IPv4_CACHE_KEY, $ipv4, $expires);
				}
			} catch (\Exception $e) {
				// problem connecting
				app('cache')->put(static::IPv4_CACHE_KEY, null, $expires);
				app('log')->info('NetAddress: could not fetch IPv4 address: '.$e->getMessage());
			}
		}

		// get IPv6 address
		if (config('components.NetAddress.config.ipv6', true)) {
			try {
				if (app('cache')->has(static::IPv6_CACHE_KEY)) {
					// use cache if it's there
					$ipv6 = app('cache')->get(static::IPv6_CACHE_KEY);
				} else {
					// cache might be out of date or doesn't exist
					$ipv6 = trim(file_get_contents('http://ipv6.icanhazip.com'));
					app('cache')->put(static::IPv6_CACHE_KEY, $ipv6, $expires);
				}
			} catch (\Exception $e) {
				// problem connecting
				app('cache')->put(static::IPv6_CACHE_KEY, null, $expires);
				app('log')->info('NetAddress: could not fetch IPv6 address: '.$e->getMessage());
			}
		}

		$browser = isset($_SERVER['REMOTE_ADDR']) ?
			$_SERVER['REMOTE_ADDR'] : null;

		$local = isset($_SERVER['SERVER_ADDR']) ?
			$_SERVER['SERVER_ADDR'] : null;

		return [$browser, $local, $ipv4, $ipv6];
	}

	public static function parse($input = null)
	{
		return array_combine([
			'browser',
			'local',
			'ipv4',
			'ipv6'
		], $input);
	}
}
