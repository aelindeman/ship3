/*
 * ShipJS 1.0
 * Alex Lindeman
 * github.com/aelindeman/ship
 */
(function() {
	'use strict';

	/*
	 * Class constructor
	 */
	function ShipJS()
	{
		this.init();
	}

	/*
	 * Keep track of some elements to reduce number of DOM interactions.
	 */
	var els = {
		html: document.documentElement,
		graphWidth: document.getElementById('graph-width'),
		autoreloadToggle: document.getElementById('autoreload-toggle'),
		themeToggle: document.getElementById('theme-toggle')
	};

	/*
	 * Class prototype
	 */
	ShipJS.prototype = {
		autoreload: els.html.dataset.autoreload == 'on',
		chartistOptions: {
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
				showGrid: false,
			},
			axisY: {
				type: Chartist.AutoScaleAxis,
				offset: 32,
				scaleMinSpace: 0,
				showGrid: false,
			},
			chartPadding: 0,
			height: '100%',
			lineSmooth: false,
			showPoint: false,
			width: '100%'
		},
		darkMode: els.html.className.indexOf('dark-mode') > -1,
		graphWidth: els.graphWidth.value,

		/*
		 * Initializes functions that need to be called on load.
		 */
		init: function()
		{
			this.bindButtonListeners();
			this.fetchGraphs();
		},

		/*
		 * Animates graphs.
		 */
		animateGraph: function(data)
		{
			if (window.innerWidth >= 800) {
				if (data.type === 'line' || data.type === 'area') {
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
		},

		/*
		 * Registers event listeners to elements that need them.
		 */
		bindButtonListeners: function()
		{
			var context = this,
				graphTimeout;

			// width setting
			if (els.graphWidth) {
				els.graphWidth.addEventListener('change', function(event) {
					console.log('Changed graph width to ');
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
		},

		/*
		 * Creates a query string from an object.
		 * http://stackoverflow.com/a/30707423
		 */
		createQueryString: function(url, data)
		{
			return url + '?' +
				Object.keys(data).map(function(key) {
					return encodeURIComponent(key) + '=' +
						encodeURIComponent(data[key]);
				}).join('&');
		},

		/*
		 * Draws components with a fresh set of data.
		 */
		drawComponents: function(data)
		{
			var components = document.getElementsByClassName('component'),
				c = components.length;
			while (c --) {
				var component = components[c];

			}
		},

		/*
		 * Draws graphs with Chartist with a set of data.
		 */
		drawGraphs: function(data, extraOptions)
		{
			var context = this,
				graphs = document.getElementsByClassName('graph'),
				g = graphs.length,
				opts = this.chartistOptions;

			while (g --) {
				var graph = graphs[g],
					series;
				if (graph.dataset.graph && (series = data[graph.dataset.graph])) {
					Chartist.Line(graph, { series: [series] }, opts)
						.on('draw', context.animateGraph);
				}
			}
		},

		/*
		 * Fetches JSON data for graphs.
		 */
		fetchGraphs: function()
		{
			var context = this,
				xhr = new XMLHttpRequest();

			xhr.open('GET', this.createQueryString('/json/graph', {
				period: 'PT' + this.graphWidth + 'H'
			}));

			xhr.addEventListener('readystatechange', function(event) {
				if (xhr.status == 200 && xhr.readyState == XMLHttpRequest.DONE) {
					var data = JSON.parse(xhr.responseText);
					context.drawComponents(data);
					context.drawGraphs(data);
				}
			});

			xhr.send();
		},

		/*
		 * Toggles the page theme.
		 */
		toggleDarkMode: function()
		{
			els.html.className = this.darkMode ?
				els.html.className.replace(/dark-mode/, 'light-mode') :
				els.html.className.replace(/light-mode/, 'dark-mode');

			this.darkMode = !this.darkMode;
		},

		/*
		 * Toggles autoreloading.
		 */
		toggleAutoreload: function()
		{
			this.autoreload = !this.autoreload;
		},

		/*
		 * Swaps labels using the data-labels attribute.
		 * If 'to' is not specified, it acts like a plural toggle, if the
		 *   'caller' element is a numb
		 */
		toggleLabels: function(target, to)
		{
			if (to instanceof HTMLElement) {
				to = (to.value || parseInt(to.innerHTML)) != 1;
			}
			var labels = target.dataset.labels.split('|');
			target.innerHTML = labels[to ? 1 : 0];
		},

		/*
		 *
		 */
	};

	/*
	 * Binds the ShipJS object to the page.
	 */
	if (typeof window == 'object') {
		window.ShipJS = new ShipJS();
	}

})();
