<?php

return [

	/*
	 * Enable this component?
	 */
	'enabled' => env('COMPONENT_DISK', true),

	/*
	 * Disks to monitor, as an array, as their device names in /dev.
	 * Use a comma-separated list rather than an array, because .env doesn't
	 *   support settings in an array.
	 */
	'disks' => env('COMPONENT_DISK_DISKS', 'sda'),

	/*
	 * Block size of the disks.
	 * Can either be an list of blocksizes, for each disk (in the same order
	 *   as the disks setting), or an integer for all disks.
	 */
	'blocksize' => env('COMPONENT_DISK_BLOCKSIZE', 512),

];
