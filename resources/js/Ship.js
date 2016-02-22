/*
 * ShipJS 1.0
 * Alex Lindeman
 * github.com/aelindeman/ship
 */
(function() {
	'use strict';

	var autoreload, chartistOptions, darkMode, selfUpdate, timePeriod;

	/*
	 * Class constructor
	 */
	function ShipJS()
	{
		this.init();
	}

	// keep track of some elements to reduce number of DOM interactions
	var els = {
		html: document.documentElement,
		autoreloadToggle: document.getElementById('autoreload-toggle'),
		timePeriod: document.getElementById('graph-width'),
		themeToggle: document.getElementById('theme-toggle')
	};

	/*
	 * Initializes variables and functions that need to be called on load.
	 */
	ShipJS.prototype.init = function()
	{
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
		this.darkMode = els.html.className.indexOf('dark-mode') > -1;
		this.selfUpdate = {
			callback: null,
			interval: 60 * 1000
		};
		this.timePeriod = els.timePeriod.value;

		this.bindButtonListeners();
		this.bindSelfUpdate();
		this.fetchGraphs();
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
	 * Registers event listeners to elements that need them.
	 */
	ShipJS.prototype.bindButtonListeners = function()
	{
		var context = this,
			graphTimeout;

		// width setting
		if (els.timePeriod) {
			els.timePeriod.addEventListener('change', function(event) {
				context.timePeriod = els.timePeriod.value;
				context.fetchGraphs();
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
		this.selfUpdate.callback = setInterval(function(e) {
			context.fetchGraphs();
			context.fetchComponents();
		}, this.selfUpdate.interval);
		console.log(['Bound selfUpdate', this.selfUpdate]);
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
	 * Draws components with a fresh set of data.
	 */
	ShipJS.prototype.drawComponents = function(data)
	{
		var components = document.getElementsByClassName('component'),
			c = components.length;
		while (c --) {
			var component = components[c];

		}
	};

	/*
	 * Draws graphs with Chartist with a set of data.
	 */
	ShipJS.prototype.drawGraphs = function(data)
	{
		var context = this,
			graphs = document.getElementsByClassName('graph'),
			g = graphs.length;

		function axisLabelInterpolate_kb(value) {
			var sizes = ['Y', 'Z', 'E', 'P', 'T', 'G', 'M', 'k'],
				unit = sizes.length;

			while (unit -- && value > 1024) {
				value /= 1024;
			}

			var decimals = value < 10 ? 2 : (value < 100 ? 1 : 0);
			return +value.toFixed(decimals) + sizes[unit];
		}

		function axisLabelInterpolate_kbPerSecond(value) {
			return axisLabelInterpolate_kb(value) + '/s';
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
	};

	/*
	 * Fetches JSON data for components.
	 */
	ShipJS.prototype.fetchComponents = function()
	{
		var context = this,
			xhr = new XMLHttpRequest();

		xhr.open('GET', this.createQueryString('/json', {
			period: 'PT' + this.timePeriod
		}));

		xhr.addEventListener('readystatechange', function(event) {
			if (xhr.status == 200 && xhr.readyState == XMLHttpRequest.DONE) {
				var data = JSON.parse(xhr.responseText);
				context.drawComponents(data);
			}
		});

		xhr.send();
	};

	/*
	 * Fetches JSON data for graphs.
	 */
	ShipJS.prototype.fetchGraphs = function()
	{
		var context = this,
			xhr = new XMLHttpRequest();

		xhr.open('GET', this.createQueryString('/json/graph', {
			period: 'PT' + this.timePeriod
		}));

		xhr.addEventListener('readystatechange', function(event) {
			if (xhr.status == 200 && xhr.readyState == XMLHttpRequest.DONE) {
				var data = JSON.parse(xhr.responseText);
				context.drawGraphs(data);
			}
		});

		xhr.send();
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
	 * Binds the ShipJS object to the page.
	 */
	if (typeof window == 'object') {
		window.ShipJS = ShipJS;
	}

})();

window.addEventListener('load', function() {
	window.ShipJSInstance = new ShipJS();
});
