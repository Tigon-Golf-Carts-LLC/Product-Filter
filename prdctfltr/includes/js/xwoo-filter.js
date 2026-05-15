/* XWoo Filter — Elementor widget behaviors. Plain JS, no dependencies. */
(function () {
	'use strict';

	function ready(fn) {
		if (document.readyState !== 'loading') {
			fn();
		} else {
			document.addEventListener('DOMContentLoaded', fn);
		}
	}

	function each(list, fn) {
		Array.prototype.forEach.call(list, fn);
	}

	function on(el, evt, sel, handler) {
		el.addEventListener(evt, function (e) {
			var t = e.target.closest(sel);
			if (t && el.contains(t)) handler.call(t, e);
		});
	}

	function getFilterSections(scope) {
		return scope.querySelectorAll('.prdctfltr_filter');
	}

	function initCollapsible(wrap) {
		if (!wrap.classList.contains('xwoo-collapsible')) return;

		var startCollapsed = wrap.classList.contains('xwoo-collapsed-default');
		var accordion = wrap.classList.contains('xwoo-accordion');
		var sections = getFilterSections(wrap);

		each(sections, function (section) {
			section.classList.add(startCollapsed ? 'xwoo-closed' : 'xwoo-open');

			// Ensure there's a chevron we can rotate. The legacy markup uses .prdctfltr-down,
			// but some preset styles omit it — add one if missing so the affordance is consistent.
			var header = section.querySelector('.pf-help-title');
			if (!header) return;
			if (!header.querySelector('.prdctfltr-down') && !header.querySelector('.xwoo-chevron')) {
				var chev = document.createElement('span');
				chev.className = 'xwoo-chevron';
				header.appendChild(chev);
			}
			header.setAttribute('role', 'button');
			header.setAttribute('tabindex', '0');
			header.setAttribute('aria-expanded', section.classList.contains('xwoo-open') ? 'true' : 'false');
		});

		function toggle(section, force) {
			var isOpen = section.classList.contains('xwoo-open');
			var willOpen = typeof force === 'boolean' ? force : !isOpen;

			if (accordion && willOpen) {
				each(sections, function (other) {
					if (other !== section) {
						other.classList.remove('xwoo-open');
						other.classList.add('xwoo-closed');
						var h = other.querySelector('.pf-help-title');
						if (h) h.setAttribute('aria-expanded', 'false');
					}
				});
			}

			section.classList.toggle('xwoo-open', willOpen);
			section.classList.toggle('xwoo-closed', !willOpen);
			var hdr = section.querySelector('.pf-help-title');
			if (hdr) hdr.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
		}

		on(wrap, 'click', '.pf-help-title', function (e) {
			// Don't hijack clicks on links/buttons/inputs that may appear inside the title.
			if (e.target.closest('a, button, input, select, textarea')) return;
			var section = this.closest('.prdctfltr_filter');
			if (section) toggle(section);
		});

		on(wrap, 'keydown', '.pf-help-title', function (e) {
			if (e.key !== 'Enter' && e.key !== ' ') return;
			e.preventDefault();
			var section = this.closest('.prdctfltr_filter');
			if (section) toggle(section);
		});
	}

	function focusable(container) {
		return container.querySelectorAll(
			'a[href], button:not([disabled]), input:not([disabled]):not([type="hidden"]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
		);
	}

	var openWrap = null;
	var lastFocused = null;

	function openDrawer(wrap) {
		if (!wrap) return;
		if (openWrap && openWrap !== wrap) closeDrawer(openWrap);

		lastFocused = document.activeElement;
		wrap.classList.add('xwoo-open');
		document.body.classList.add('xwoo-filter-locked');
		openWrap = wrap;

		var dialog = wrap.querySelector('.xwoo-filter-drawer, .xwoo-filter-inline');
		if (dialog) {
			dialog.setAttribute('aria-hidden', 'false');
			var first = focusable(dialog)[0];
			if (first) {
				try { first.focus({ preventScroll: true }); } catch (e) { first.focus(); }
			} else {
				try { dialog.focus({ preventScroll: true }); } catch (e) { dialog.focus(); }
			}
		}

		var trigger = wrap.querySelector('.xwoo-filter-trigger');
		if (trigger) trigger.setAttribute('aria-expanded', 'true');
	}

	function closeDrawer(wrap) {
		wrap = wrap || openWrap;
		if (!wrap) return;
		wrap.classList.remove('xwoo-open');
		document.body.classList.remove('xwoo-filter-locked');
		var dialog = wrap.querySelector('.xwoo-filter-drawer, .xwoo-filter-inline');
		if (dialog) dialog.setAttribute('aria-hidden', 'true');
		var trigger = wrap.querySelector('.xwoo-filter-trigger');
		if (trigger) trigger.setAttribute('aria-expanded', 'false');
		if (lastFocused && document.body.contains(lastFocused)) {
			try { lastFocused.focus({ preventScroll: true }); } catch (e) {}
		}
		if (openWrap === wrap) openWrap = null;
	}

	function trapFocus(wrap, e) {
		if (e.key !== 'Tab') return;
		var dialog = wrap.querySelector('.xwoo-filter-drawer, .xwoo-filter-inline');
		if (!dialog) return;
		var els = focusable(dialog);
		if (!els.length) return;
		var first = els[0];
		var last = els[els.length - 1];
		if (e.shiftKey && document.activeElement === first) {
			e.preventDefault();
			last.focus();
		} else if (!e.shiftKey && document.activeElement === last) {
			e.preventDefault();
			first.focus();
		}
	}

	function initDrawer(wrap) {
		var hasDrawer =
			wrap.classList.contains('xwoo-mode-drawer') ||
			wrap.classList.contains('xwoo-mode-fullscreen') ||
			wrap.classList.contains('xwoo-mobile-drawer');
		if (!hasDrawer) return;

		on(wrap, 'click', '[data-xwoo-open]', function () { openDrawer(wrap); });
		on(wrap, 'click', '[data-xwoo-close]', function () { closeDrawer(wrap); });

		on(wrap, 'click', '[data-xwoo-reset]', function () {
			var form = wrap.querySelector('form.prdctfltr_woocommerce_ordering');
			if (!form) return;
			// Uncheck everything and let the existing plugin JS pick up the change.
			each(form.querySelectorAll('input[type="checkbox"]:checked, input[type="radio"]:checked'), function (input) {
				input.checked = false;
				input.dispatchEvent(new Event('change', { bubbles: true }));
			});
			each(form.querySelectorAll('input[type="hidden"][name]'), function (input) { input.value = ''; });
		});
	}

	function activeCount(wrap) {
		var form = wrap.querySelector('form.prdctfltr_woocommerce_ordering');
		if (!form) return 0;
		return form.querySelectorAll('input[type="checkbox"]:checked, input[type="radio"]:checked').length;
	}

	function refreshCount(wrap) {
		var badge = wrap.querySelector('.xwoo-filter-trigger-count');
		if (!badge) return;
		var n = activeCount(wrap);
		if (n > 0) {
			badge.textContent = String(n);
			badge.hidden = false;
		} else {
			badge.hidden = true;
		}
	}

	function initCount(wrap) {
		var badge = wrap.querySelector('.xwoo-filter-trigger-count');
		if (!badge) return;
		refreshCount(wrap);
		wrap.addEventListener('change', function (e) {
			if (e.target && (e.target.matches('input[type="checkbox"]') || e.target.matches('input[type="radio"]'))) {
				refreshCount(wrap);
			}
		});
	}

	function init() {
		each(document.querySelectorAll('.xwoo-filter-wrap'), function (wrap) {
			initCollapsible(wrap);
			initDrawer(wrap);
			initCount(wrap);
		});

		document.addEventListener('keydown', function (e) {
			if (!openWrap) return;
			if (e.key === 'Escape') {
				closeDrawer(openWrap);
				return;
			}
			trapFocus(openWrap, e);
		});
	}

	ready(init);

	// Re-init after Elementor frontend renders in the editor preview.
	if (window.elementorFrontend) {
		window.elementorFrontend.hooks &&
			window.elementorFrontend.hooks.addAction &&
			window.elementorFrontend.hooks.addAction('frontend/element_ready/xwoo-filter.default', function ($scope) {
				var wrap = $scope[0] && $scope[0].querySelector('.xwoo-filter-wrap');
				if (!wrap) return;
				initCollapsible(wrap);
				initDrawer(wrap);
				initCount(wrap);
			});
	}
})();
