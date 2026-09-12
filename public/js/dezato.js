(() => {
  'use strict';

  const doc = document;
  const body = doc.body;

  /* ---------- Cake baking page loader ---------- */
  const bakeLoader = doc.getElementById('bake-loader');
  if (bakeLoader) {
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const minMs = reduceMotion ? 0 : 1200;
    const started = performance.now();
    body.classList.add('is-baking');

    const finish = () => {
      const wait = Math.max(0, minMs - (performance.now() - started));
      window.setTimeout(() => {
        bakeLoader.classList.add('is-done');
        bakeLoader.setAttribute('aria-busy', 'false');
        body.classList.remove('is-baking');
        window.setTimeout(() => bakeLoader.remove(), 500);
      }, wait);
    };

    if (doc.readyState === 'complete') {
      finish();
    } else {
      window.addEventListener('load', finish, { once: true });
    }
  }

  /* ---------- Mobile drawer ---------- */
  const drawer = doc.getElementById('nav-drawer');
  const overlay = doc.getElementById('nav-overlay');
  const openBtns = doc.querySelectorAll('[data-nav-open]');
  const closeBtns = doc.querySelectorAll('[data-nav-close]');

  const setNav = (open) => {
    if (!drawer) return;
    drawer.classList.toggle('is-open', open);
    overlay?.classList.toggle('is-open', open);
    body.classList.toggle('nav-locked', open);
    drawer.setAttribute('aria-hidden', open ? 'false' : 'true');
    openBtns.forEach((btn) => btn.setAttribute('aria-expanded', open ? 'true' : 'false'));
    if (open) {
      drawer.querySelector('a, button')?.focus({ preventScroll: true });
    }
  };

  openBtns.forEach((btn) => btn.addEventListener('click', () => setNav(true)));
  closeBtns.forEach((btn) => btn.addEventListener('click', () => setNav(false)));
  overlay?.addEventListener('click', () => setNav(false));

  drawer?.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', () => setNav(false));
  });

  doc.addEventListener('keydown', (e) => {
    if (e.key !== 'Escape') return;
    const modal = doc.querySelector('[data-fulfillment-modal]');
    if (modal && !modal.hidden) {
      const dismissForm = doc.getElementById('fulfillment-dismiss-form');
      if (dismissForm) {
        dismissForm.requestSubmit();
      } else {
        setFulfillmentModal(false);
      }
      return;
    }
    setNav(false);
  });

  /* ---------- Accordion in drawer ---------- */
  doc.querySelectorAll('[data-accordion]').forEach((btn) => {
    btn.addEventListener('click', () => {
      const panel = btn.nextElementSibling;
      const expanded = btn.getAttribute('aria-expanded') === 'true';
      btn.setAttribute('aria-expanded', expanded ? 'false' : 'true');
      panel?.classList.toggle('is-open', !expanded);
    });
  });

  /* ---------- Sticky header shadow ---------- */
  const header = doc.querySelector('.site-header');
  const onScroll = () => {
    if (!header) return;
    header.classList.toggle('is-scrolled', window.scrollY > 8);
  };
  onScroll();
  window.addEventListener('scroll', onScroll, { passive: true });

  /* ---------- Reveal on scroll ---------- */
  const reveals = doc.querySelectorAll('[data-reveal]');
  if (reveals.length && 'IntersectionObserver' in window) {
    const io = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-visible');
            io.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.12, rootMargin: '0px 0px -40px 0px' }
    );
    reveals.forEach((el) => io.observe(el));
  } else {
    reveals.forEach((el) => el.classList.add('is-visible'));
  }

  /* ---------- Active chip into view (menu) ---------- */
  const activeChip = doc.querySelector('.chip-row .chip.is-active');
  if (activeChip && typeof activeChip.scrollIntoView === 'function') {
    activeChip.scrollIntoView({ inline: 'center', block: 'nearest', behavior: 'auto' });
  }

  /* ---------- Fulfillment gate (pickup / delivery / shipping) ---------- */
  const fulfillment = doc.querySelector('[data-fulfillment]');
  if (fulfillment) {
    const methodInput = doc.getElementById('fulfillment-method');
    const tabs = [...fulfillment.querySelectorAll('[data-fulfillment-tab]')];
    const deliveryPanel = fulfillment.querySelector('[data-fulfillment-panel="delivery"]');
    const shippingPanel = fulfillment.querySelector('[data-fulfillment-panel="shipping"]');
    const storeSection = fulfillment.querySelector('[data-store-section]');
    const deliveryAddress = fulfillment.querySelector('[data-delivery-address]');
    const shippingAddress = fulfillment.querySelector('[data-shipping-address]');
    const shippingFields = [...fulfillment.querySelectorAll('[data-shipping-field]')];
    const searchInput = fulfillment.querySelector('[data-store-search]');
    const options = [...fulfillment.querySelectorAll('[data-store-option]')];
    const radios = [...fulfillment.querySelectorAll('[data-store-radio]')];
    const empty = fulfillment.querySelector('[data-store-empty]');

    const setEnabled = (el, enabled) => {
      if (!el) return;
      el.disabled = !enabled;
    };

    const setMethod = (method) => {
      if (methodInput) methodInput.value = method;

      tabs.forEach((tab) => {
        const active = tab.getAttribute('data-fulfillment-tab') === method;
        tab.classList.toggle('is-active', active);
        tab.setAttribute('aria-selected', active ? 'true' : 'false');
      });

      if (deliveryPanel) deliveryPanel.hidden = method !== 'delivery';
      if (shippingPanel) shippingPanel.hidden = method !== 'shipping';
      if (storeSection) storeSection.hidden = method === 'shipping';

      setEnabled(deliveryAddress, method === 'delivery');
      setEnabled(shippingAddress, method === 'shipping');
      shippingFields.forEach((field) => setEnabled(field, method === 'shipping'));

      if (deliveryAddress) deliveryAddress.required = method === 'delivery';
      if (shippingAddress) shippingAddress.required = method === 'shipping';
      shippingFields.forEach((field) => {
        field.required = method === 'shipping';
      });

      radios.forEach((radio) => {
        radio.required = method !== 'shipping';
        if (method === 'shipping') radio.checked = false;
      });

      filterStores();
    };

    const filterStores = () => {
      const method = methodInput?.value || 'pickup';
      if (method === 'shipping') {
        if (empty) empty.hidden = true;
        return;
      }

      const query = (searchInput?.value || '').trim().toLowerCase();
      let visible = 0;

      options.forEach((option) => {
        const delivers = option.getAttribute('data-delivers') === '1';
        const name = option.getAttribute('data-store-name') || '';
        const matchesQuery = !query || name.includes(query);
        const matchesMethod = method === 'pickup' || delivers;
        const show = matchesQuery && matchesMethod;
        option.hidden = !show;
        if (!show) {
          const radio = option.querySelector('input[type="radio"]');
          if (radio?.checked) radio.checked = false;
        } else {
          visible += 1;
        }
      });

      if (empty) empty.hidden = visible > 0;
    };

    tabs.forEach((tab) => {
      tab.addEventListener('click', () => {
        setMethod(tab.getAttribute('data-fulfillment-tab') || 'pickup');
      });
    });

    searchInput?.addEventListener('input', filterStores);
    setMethod(methodInput?.value || 'pickup');

    fulfillment._setMethod = setMethod;
  }

  /* ---------- Horizontal rails ---------- */
  doc.querySelectorAll('[data-rail]').forEach((rail) => {
    const viewport = rail.querySelector('[data-rail-viewport]');
    const prev = rail.querySelector('[data-rail-prev]');
    const next = rail.querySelector('[data-rail-next]');
    if (!viewport) return;

    const scrollByCard = (dir) => {
      const amount = Math.max(200, viewport.clientWidth * 0.7) * dir;
      viewport.scrollBy({ left: amount, behavior: 'smooth' });
    };

    prev?.addEventListener('click', () => scrollByCard(-1));
    next?.addEventListener('click', () => scrollByCard(1));
  });

  /* ---------- Fulfillment modal (first visit / order gate) ---------- */
  const fulfillmentModal = doc.querySelector('[data-fulfillment-modal]');

  function setFulfillmentModal(open) {
    if (!fulfillmentModal) return;
    fulfillmentModal.hidden = !open;
    body.classList.toggle('modal-locked', open);
    if (open) {
      setNav(false);
      fulfillmentModal.querySelector('.fulfillment-modal__dialog')?.focus({ preventScroll: true });
    }
  }

  doc.querySelectorAll('[data-fulfillment-open]').forEach((el) => {
    el.addEventListener('click', (event) => {
      const method = el.getAttribute('data-fulfillment-method');
      if (el.tagName === 'A' && (el.getAttribute('href') || '').includes('fulfillment=1')) {
        event.preventDefault();
      }
      if (method && fulfillmentModal) {
        const root = fulfillmentModal.querySelector('[data-fulfillment]');
        root?._setMethod?.(method);
      }
      setFulfillmentModal(true);
    });
  });

  fulfillmentModal?.querySelectorAll('[data-fulfillment-dismiss]').forEach((el) => {
    el.addEventListener('click', () => {
      const dismissForm = doc.getElementById('fulfillment-dismiss-form');
      if (dismissForm) {
        dismissForm.requestSubmit();
        return;
      }
      setFulfillmentModal(false);
    });
  });

  if (fulfillmentModal?.getAttribute('data-auto-open') === '1') {
    setFulfillmentModal(true);
  }
})();
