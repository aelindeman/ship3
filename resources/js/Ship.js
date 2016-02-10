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
		darkMode: els.html.className.indexOf('dark-mode') > -1,
		graphWidth: els.graphWidth.value,
		masonry: null,

		/*
		 * Initializes functions that need to be called on load.
		 */
		init: function()
		{
			this.bind();
			this.masonry = new Masonry('.components .grid' , {
				columnWidth: '.box',
				itemSelector: '.box',
				percentPosition: true,
				transitionDuration: 0
			});
		},

		/*
		 * Registers event listeners to elements that need them.
		 */
		bind: function()
		{
			var context = this;

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
		}
	};

	/*
	 * Binds the ShipJS object to the page.
	 */
	if (typeof window == 'object') {
		window.ShipJS = new ShipJS();
	}

})();
