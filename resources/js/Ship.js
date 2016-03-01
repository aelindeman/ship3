/*
 * ShipJS 1.0
 * Alex Lindeman
 * github.com/aelindeman/ship
 */
(function() {
	'use strict';

	var autoreload, chartistOptions, config, darkMode, drawCallbacks, lang,
		selfUpdate, timePeriod, uptimeAnimation;

	/*
	 * Class constructor
	 */
	function ShipJS()
	{
		if (!window.Chartist) {
			console.error('Chartist is required');
			return;
		}

		return this;
	}

	// keep track of some elements to reduce number of DOM interactions
	var els = {
		html: document.documentElement,
		autoreloadToggle: document.getElementById('autoreload-toggle'),
		loadingIndicator: document.getElementById('loading-indicator'),
		timePeriod: document.getElementById('time-period'),
		themeToggle: document.getElementById('theme-toggle'),
		uptime: document.getElementById('uptime')
	};

	/*
	 * Initializes variables and functions that need to be called on load.
	 */
	ShipJS.prototype.init = function(data)
	{
		// register default settings
		this.autoreload = els.html.dataset.autoreload == 'on';
		this.chartistOptions = {
			axisX: {
				type: Chartist.FixedScaleAxis,
				divisor: 5,
				labelInterpolationFnc: function(label) {
					var d = new Date(label * 1000),
						date = (d.getMonth() + 1) + '/' + d.getDate(),
						time = ('00' + d.getHours()).slice(-2) + ':' + ('00' + d.getMinutes()).slice(-2);
					return date + '<br />' + time;
				},
				offset: 28,
				scaleMinSpace: 0,
				showGrid: false,
			},
			axisY: {
				type: Chartist.AutoScaleAxis,
				offset: 48,
				scaleMinSpace: 0,
				showGrid: false,
			},
			chartPadding: {
				top: 12,
				left: 0,
				bottom: 0,
				right: 2
			},
			height: '100%',
			lineSmooth: false,
			showPoint: false,
			width: '100%'
		};
		this.config = null;
		this.darkMode = els.html.className.indexOf('dark-mode') > -1;
		this.drawCallbacks = {
			drawComponents: true,
			drawGraphs: true
		};
		this.lang = null;
		this.selfUpdate = {
			callback: null,
			interval: 60 * 1000
		};
		this.timePeriod = els.timePeriod.value;
		this.uptimeAnimation = {
			callback: null,
			interval: 1000
		};

		// merge in language settings
		if (data.lang) {
			this.lang = data.lang;
		}

		// register listeners and stuff
		this.bindButtonListeners();
		this.bindSelfUpdate();

		this.animateUptime();
		this.fetchGraphs();

		return this;
	};

	/*
	 * Animates graphs.
	 */
	ShipJS.prototype.animateGraph = function(data)
	{
		if (window.innerWidth >= 800) {
			if (data.type == 'line' || data.type == 'area') {
				var padding = data.chartRect.padding.bottom + data.chartRect.padding.top;
				data.element.animate({
					opacity: {
						begin: 250 * data.index + 125,
						dur: 750,
						from: 0,
						to: 1,
						easing: Chartist.Svg.Easing.easeOutQuint
					},
					d: {
						begin: 250 * data.index,
						dur: 1000,
						from: data.path.clone().scale(1, 0).translate(0, data.chartRect.height() + padding).stringify(),
						to: data.path.clone().stringify(),
						easing: Chartist.Svg.Easing.easeOutQuint
					}
				});
			}
		}
	};

	/*
	 * Animates the uptime.
	 */
	ShipJS.prototype.animateUptime = function()
	{
		var context = this;
		clearTimeout(this.uptimeAnimation.callback);
		this.uptimeAnimation.callback = setInterval(function() {
			els.uptime.innerHTML = context.fetchUptime(els.uptime.dataset.format);
		}, this.uptimeAnimation.interval);
	};

	/*
	 * Registers event listeners to elements that need them.
	 */
	ShipJS.prototype.bindButtonListeners = function()
	{
		var context = this;

		// width setting
		if (els.timePeriod) {
			els.timePeriod.addEventListener('change', function(event) {
				context.timePeriod = els.timePeriod.value;
				if (window.history.replaceState) {
					window.history.replaceState(null, null, context.setQueryParam('period', els.timePeriod.value));
				}
				context.fetchGraphs();
				context.fetchComponents();
			});
		}

		// autoreload toggle
		if (els.autoreloadToggle) {
			els.autoreloadToggle.addEventListener('click', function(event){
				context.toggleAutoreload();
				context.toggleLabels(event.target, context.autoreload);
			});
		}

		// theme toggle
		if (els.themeToggle) {
			els.themeToggle.addEventListener('click', function(event) {
				context.toggleDarkMode(event.target);
				context.toggleLabels(event.target, context.darkMode);
			});
		}
	};

	/*
	 * Registers the auto-update functions.
	 */
	ShipJS.prototype.bindSelfUpdate = function()
	{
		var context = this;
		clearInterval(this.selfUpdate.callback);

		if (this.autoreload) {
			this.selfUpdate.callback = setInterval(function() {
				context.fetchGraphs();
				context.fetchComponents();
			}, this.selfUpdate.interval);
		}
	};

	/*
	 * Creates a query string from an object.
	 * http://stackoverflow.com/a/30707423
	 */
	ShipJS.prototype.createQueryString = function(url, data)
	{
		return url + '?' +
			Object.keys(data).map(function(key) {
				return encodeURIComponent(key) + '=' +
					encodeURIComponent(data[key]);
			}).join('&');
	};

	/*
	 * Callback fired when drawComponents and drawGraphs complete.
	 */
	ShipJS.prototype.drawCallback = function(caller, context)
	{
		context.drawCallbacks[caller] = true;

		var done = Object.keys(context.drawCallbacks)
			.reduce(function(prev, curr) {
				return context.drawCallbacks[prev] & context.drawCallbacks[curr];
			});

		if (done) {
			context.setLoadingIndicator(context.tr('ship.done'), false);
		}
	};

	/*
	 * Draws components with a fresh set of data.
	 */
	ShipJS.prototype.drawComponents = function(data, onComplete)
	{
		var keys = document.querySelectorAll('[data-key]'),
			k = keys.length,
			raw = document.querySelectorAll('[data-raw-key]'),
			r = raw.length,
			meters = document.querySelectorAll('[data-meter-series-key]'),
			m = meters.length;

		function get(key) {
			return key.split('.').reduce(function(prev, curr) {
				return prev ? prev[curr] : undefined;
			}, data || self);
		}

		// update text
		while (k --) {
			var kel = keys[k],
				kkey = kel.dataset.key,
				kvalue = get(kkey);

			if (kel.dataset.units) {
				kvalue = this.valueTransform(kel.dataset.units, kvalue);
			}

			kel.innerHTML = kvalue || '&mdash;';
		}

		// update raw
		while (r --) {
			var rel = raw[r],
				rkey = rel.dataset.rawKey,
				rvalue = get(rkey);

			rel.dataset.raw = rvalue;
		}

		// update meters
		while (m --) {
			var mel = meters[m],
				mkey = mel.dataset.meterSeriesKey,
				mvalue = get(mkey);

			mvalue = this.valueTransform('percent', mvalue);
			mel.style.width = mvalue;
		}

		onComplete('drawComponents', this);
	};

	/*
	 * Draws graphs with Chartist with a set of data.
	 */
	ShipJS.prototype.drawGraphs = function(data, onComplete)
	{
		var context = this,
			graphs = document.getElementsByClassName('graph'),
			g = graphs.length;

		function axisLabelInterpolate_kb(value) {
			return context.valueTransform('kb', value);
		}

		function axisLabelInterpolate_kbPerSecond(value) {
			return context.valueTransform('kb/s', value);
		}

		function axisLabelInterpolate_staticTwoDecimal(value) {
			return value.toFixed(2);
		}

		while (g --) {
			var opts = this.chartistOptions,
				graph = graphs[g],
				series;

			if (graph.dataset.graph && (series = data[graph.dataset.graph])) {
				if (graph.dataset.graphYaxisUnits) {
					switch (graph.dataset.graphYaxisUnits) {
						case 'kb':
							opts.axisY.labelInterpolationFnc = axisLabelInterpolate_kb;
							break;
						case 'kb/s':
							opts.axisY.labelInterpolationFnc = axisLabelInterpolate_kbPerSecond;
							break;
						case 'staticTwoDecimal':
							opts.axisY.labelInterpolationFnc = axisLabelInterpolate_staticTwoDecimal;
							break;
						default:
							opts.axisY.labelInterpolationFnc = Chartist.noop;
							break;
					}
				} else {
					opts.axisY.labelInterpolationFnc = Chartist.noop;
				}

				Chartist.Line(graph, { series: series }, opts)
					.on('draw', context.animateGraph);
			}
		}

		onComplete('drawGraphs', this);
	};

	/*
	 * Fetches JSON data for components.
	 */
	ShipJS.prototype.fetchComponents = function()
	{
		var context = this,
			xhr = new XMLHttpRequest();

		context.drawCallbacks.drawComponents = false;

		xhr.open('GET', this.createQueryString('/json', {
			period: this.timePeriod
		}));

		xhr.addEventListener('readystatechange', function(event) {
			if (xhr.status == 200 && xhr.readyState == XMLHttpRequest.DONE) {
				var data = JSON.parse(xhr.responseText);
				context.drawComponents(data, context.drawCallback);
			}
		});

		context.setLoadingIndicator(context.tr('ship.loading'), xhr.readyState);
		xhr.send();
	};

	/*
	 * Fetches JSON data for graphs.
	 */
	ShipJS.prototype.fetchGraphs = function()
	{
		var context = this,
			xhr = new XMLHttpRequest();

		context.drawCallbacks.drawGraphs = false;

		xhr.open('GET', this.createQueryString('/json/graph', {
			period: this.timePeriod
		}));

		xhr.addEventListener('readystatechange', function(event) {
			if (xhr.status == 200 && xhr.readyState == XMLHttpRequest.DONE) {
				var data = JSON.parse(xhr.responseText);
				context.drawGraphs(data, context.drawCallback);
			}
		});

		context.setLoadingIndicator(context.tr('ship.loading'), xhr.readyState);
		xhr.send();
	};

	/*
	 * Renders uptime for a given format.
	 */
	ShipJS.prototype.fetchUptime = function(format)
	{
		var context = this,
			uptime = ++ els.uptime.dataset.raw,
			replace = {
				'@s': ('00' + Math.floor(uptime % 60)).slice(-2),
				'@m': ('00' + Math.floor((uptime / 60) % 60)).slice(-2),
				'@h': Math.floor((uptime / 3600) % 24),
				'@d': Math.floor(uptime / 86400),
				'@M': Math.round(uptime / 60),
				'@H': (uptime / 3600).toFixed(1),
				'@G': Math.floor(uptime / 60),
				'@D': (uptime / 86400).toFixed(2),
				'_m': context.tr('ship.time.minute').substring(0, 1),
				'_h': context.tr('ship.time.hour').substring(0, 1),
				'_d': context.tr('ship.time.day').substring(0, 1),
				'_M': context.tr('ship.time.minute').substring(0, 1),
				'_H': context.tr('ship.time.hour').substring(0, 1),
				'_D': context.tr('ship.time.day').substring(0, 1),
			},
			text = format;

		for (var token in replace) {
			text = text.replace(new RegExp(token, 'g'), replace[token]);
		}

		return text;
	};

	/*
	 * Sets text and class on the loading indicator.
	 */
	ShipJS.prototype.setLoadingIndicator = function(text, status)
	{
		els.loadingIndicator.innerHTML = text || this.lang.ship.loading;
		els.loadingIndicator.className = status ? 'status-' + status : 'status-0';
	};

	/*
	 * Sets or modifies a URL query parameter.
	 */
	ShipJS.prototype.setQueryParam = function(name, value, url)
	{
		url = url || window.location.toString();
		var re = new RegExp('([?&])' + name + '=.*(&|#|$)', 'i');

		if (url.match(re)) {
			return url.replace(re, '$1' + name + '=' + value + '$2');
		} else {
			var hash = '';
			if (url.indexOf('#') !== -1) {
				hash = url.replace(/.*#/, '#');
				url = url.replace(/#.*/, '');
			}
			var separator = url.indexOf('?') !== -1 ? '&' : '?';
			return url += separator + name + '=' + value + hash;
		}
	};

	/*
	 * Toggles the page theme.
	 */
	ShipJS.prototype.toggleDarkMode = function()
	{
		els.html.className = this.darkMode ?
			els.html.className.replace(/dark-mode/, 'light-mode') :
			els.html.className.replace(/light-mode/, 'dark-mode');

		this.darkMode = !this.darkMode;
	};

	/*
	 * Toggles autoreloading.
	 */
	ShipJS.prototype.toggleAutoreload = function()
	{
		this.autoreload = !this.autoreload;

		if (this.autoreload) {
			this.bindSelfUpdate();
		} else {
			clearInterval(this.selfUpdate.callback);
		}
	};

	/*
	 * Swaps labels using the data-labels attribute.
	 * If 'to' is not specified, it acts like a plural toggle, if the
	 *   'caller' element is a numb
	 */
	ShipJS.prototype.toggleLabels = function(target, to)
	{
		if (to instanceof HTMLElement) {
			to = (to.value || parseInt(to.innerHTML)) != 1;
		}
		var labels = target.dataset.labels.split('|');
		target.innerHTML = labels[to ? 1 : 0];
	};

	/*
	 * Safely gets a translation key.
	 */
	ShipJS.prototype.tr = function(key, backup)
	{
		if (this.lang) {
			return key.split('.').reduce(function(prev, curr) {
				return prev ? prev[curr] : undefined;
			}, this.lang || self);
		} else if (backup) {
			return backup;
		} else {
			return key;
		}
	};

	/*
	 * Gets a translation and pluralizes it based on a value.
	 * Currently only supports basic one/many translations!
	 */
	ShipJS.prototype.trChoice = function(key, count, backup)
	{
		if (this.lang) {
			return this.tr(key).split('|')[+(count != 1)];
		} else if (backup) {
			return backup.split('|')[+(count != 1)];
		} else {
			return key;
		}
	};

	/*
	 * Various value transform functions.
	 */
	ShipJS.prototype.valueTransform = function(which, value)
	{
		var context = this;

		var transforms = {

			hours: function(value)
			{
				return value + ' ' + context.trChoice('ship.time.hour', value);
			},

			kb: function(value)
			{
				var sizes = ['Y', 'Z', 'E', 'P', 'T', 'G', 'M', 'k'],
					unit = sizes.length;

				while (unit -- && value > 1024) {
					value /= 1024;
				}

				var decimals = value < 10 ? 2 : (value < 100 ? 1 : 0);
				return +value.toFixed(decimals) + sizes[unit];
			},

			'kb/s': function(value)
			{
				return this.kb(value) + '/s';
			},

			lcfirst: function(value)
			{
				return value.charAt(0).toLowerCase() + value.slice(1);
			},

			minutes: function(value)
			{
				return value + ' ' + context.trChoice('ship.time.minute', value);
			},

			percent: function(value)
			{
				return value + '%';
			},

			relativeDateDiff: function(value)
			{
				var date = Date.parse(value);
			},

			seconds: function(value)
			{
				return value + ' ' + context.trChoice('ship.time.second', value);
			}

		};

		// return either value passed through the specified transform,
		// or all transform functions
		return which ?
			((which in transforms) ? transforms[which](value) : undefined) :
			transforms;
	};

	/*
	 * Binds the ShipJS object to the page.
	 */
	if (typeof window == 'object') {
		window.ShipJS = new ShipJS();
	}

})();
