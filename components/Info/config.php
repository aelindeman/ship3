<?php

return [

	/*
	 * Enable this component?
	 */
	'enabled' => env('COMPONENT_INFO', true),

	/*
	 * How should uptime be formatted?
	 *
	 * Can be a string containing any of the following tokens:
	 *   +-----+---------------------+
	 *   | Key | Description         |
	 *   +-----+---------------------+
	 *   | @s  | Seconds piece       |
	 *   | @m  | Minutes piece       |
	 *   | @h  | Hours piece         |
	 *   | @d  | Days piece          |
	 *   +-----+---------------------+
	 *   | @M  | Total minutes       |
	 *   | @H  | Total hours         |
	 *   | @G  | Total hours + days  |
	 *   | @D  | Total days          |
	 *   +-----+---------------------+
	 *   | _m  | Minute label        |
	 *   | _h  | Hour label          |
	 *   | _d  | Day label           |
	 *   | _M  | Total minutes label |
	 *   | _H  | Total hours label   |
	 *   | _D  | Total days label    |
	 *   +-----+---------------------+
	 *
	 * Example formats:
	 *   +---------------+---------------+
	 *   | Format string | Output        |
	 *   +---------------+---------------+
	 *   | @d_d @h:@m:@s | 25d 16:45:20  |
	 *   | @G_H @m_m     | 616h 45m      |
	 *   | @H_H          | 616.75h       |
	 *   | @D_D          | 25.68d        |
	 *   | @m$@s@m_dello | 45$2045dello  |
	 *   +---------------+---------------+
	 */
	'uptime-format' => env('COMPONENT_INFO_UPTIME_FORMAT', '@d_d @h:@m:@s'),

];
