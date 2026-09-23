(() => {
  'use strict';

  const formatPkr = (n) =>
    '₨ ' + Math.round(Number(n) || 0).toLocaleString('en-PK');

  /* ---------- Custom cake builder ---------- */
  const builder = document.querySelector('[data-cake-builder]');
  if (builder) {
    const baseEl = builder.querySelector('[data-sum-base]');
    const extrasEl = builder.querySelector('[data-sum-extras]');
    const totalEl = builder.querySelector('[data-sum-total]');

    const calc = () => {
      let base = 0;
      let extras = 0;

      const size = builder.querySelector('input[name="size"]:checked');
      if (size) base = Number(size.dataset.price || 0);

      builder.querySelectorAll('select[data-builder-price]').forEach((select) => {
        const opt = select.selectedOptions[0];
        extras += Number(opt?.dataset.price || 0);
      });

      builder.querySelectorAll('input[type="checkbox"][data-builder-price]:checked').forEach((input) => {
        extras += Number(input.dataset.price || 0);
      });

      if (baseEl) baseEl.textContent = formatPkr(base);
      if (extrasEl) extrasEl.textContent = formatPkr(extras);
      if (totalEl) totalEl.textContent = formatPkr(base + extras);
    };

    builder.addEventListener('change', calc);
    calc();

    const upload = builder.querySelector('[data-builder-upload]');
    const preview = builder.querySelector('[data-upload-preview]');
    const label = builder.querySelector('[data-upload-label]');
    upload?.addEventListener('change', () => {
      const file = upload.files?.[0];
      if (!file || !preview) return;
      const url = URL.createObjectURL(file);
      const img = preview.querySelector('img');
      if (img) img.src = url;
      preview.hidden = false;
      if (label) label.textContent = file.name;
    });

    /* Icing colour: presets + native color picker */
    const colorRoot = builder.querySelector('[data-icing-colors]');
    const colorHexField = builder.querySelector('[data-color-hex-field]');
    const colorPicker = builder.querySelector('[data-color-picker]');
    const customRadio = builder.querySelector('[data-color-custom-radio]');
    const customPreview = builder.querySelector('[data-color-custom-preview]');
    const customSwatch = customRadio?.closest('.color-swatch--custom');

    const setIcingHex = (hex, { custom = false } = {}) => {
      if (colorHexField) colorHexField.value = hex;
      if (customPreview) customPreview.style.setProperty('--swatch', hex);
      customSwatch?.classList.toggle('is-active', custom);
    };

    colorRoot?.addEventListener('change', (event) => {
      const target = event.target;
      if (!(target instanceof HTMLInputElement)) return;

      if (target.matches('input[name="color"]') && target.value !== 'custom') {
        setIcingHex(target.dataset.colorHex || '#f7f1e8', { custom: false });
        return;
      }

      if (target === customRadio || target === colorPicker) {
        const hex = colorPicker?.value || '#c45c6a';
        if (customRadio) customRadio.checked = true;
        setIcingHex(hex, { custom: true });
      }
    });

    colorPicker?.addEventListener('input', () => {
      if (customRadio) customRadio.checked = true;
      setIcingHex(colorPicker.value, { custom: true });
    });
  }

  /* ---------- Product gallery ---------- */
  document.querySelectorAll('[data-gallery]').forEach((gallery) => {
    const main = gallery.querySelector('[data-gallery-main]');
    const thumbs = gallery.querySelectorAll('[data-gallery-thumb]');
    thumbs.forEach((thumb) => {
      thumb.addEventListener('click', () => {
        const src = thumb.getAttribute('data-src');
        if (main && src) {
          main.src = src;
        }
        thumbs.forEach((t) => t.classList.toggle('is-active', t === thumb));
      });
    });
  });
  /* ---------- Checkout payment instructions ---------- */
  const payRoot = document.querySelector('[data-checkout-payment]');
  if (payRoot) {
    const syncPayPanels = () => {
      const selected = payRoot.querySelector('input[name="payment_method"]:checked');
      const id = selected?.value || '';
      payRoot.querySelectorAll('[data-pay-panel]').forEach((panel) => {
        panel.hidden = panel.getAttribute('data-pay-panel') !== id;
      });
    };
    payRoot.addEventListener('change', syncPayPanels);
    syncPayPanels();
  }

  /* ---------- Homepage hero slider ---------- */
  const hero = document.querySelector('[data-hero-slider]');
  if (hero) {
    const slides = Array.from(hero.querySelectorAll('[data-hero-slide]'));
    const dots = Array.from(hero.querySelectorAll('[data-hero-dot]'));
    const prev = hero.querySelector('[data-hero-prev]');
    const next = hero.querySelector('[data-hero-next]');
    const intervalMs = Math.max(4000, Number(hero.getAttribute('data-hero-interval') || 4500));
    let index = 0;
    let timer = null;
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const show = (nextIndex) => {
      if (slides.length < 2) return;
      index = (nextIndex + slides.length) % slides.length;
      slides.forEach((slide, i) => {
        const active = i === index;
        slide.classList.toggle('is-active', active);
        slide.setAttribute('aria-hidden', active ? 'false' : 'true');
      });
      dots.forEach((dot, i) => {
        const active = i === index;
        dot.classList.toggle('is-active', active);
        if (active) dot.setAttribute('aria-current', 'true');
        else dot.removeAttribute('aria-current');
      });
    };

    const stop = () => {
      if (timer) {
        window.clearInterval(timer);
        timer = null;
      }
    };

    const start = () => {
      if (reduceMotion || slides.length < 2) return;
      stop();
      timer = window.setInterval(() => show(index + 1), intervalMs);
    };

    prev?.addEventListener('click', () => {
      show(index - 1);
      start();
    });
    next?.addEventListener('click', () => {
      show(index + 1);
      start();
    });
    dots.forEach((dot) => {
      dot.addEventListener('click', () => {
        show(Number(dot.getAttribute('data-hero-dot') || 0));
        start();
      });
    });

    hero.addEventListener('mouseenter', stop);
    hero.addEventListener('mouseleave', start);
    hero.addEventListener('focusin', stop);
    hero.addEventListener('focusout', start);

    document.addEventListener('visibilitychange', () => {
      if (document.hidden) stop();
      else start();
    });

    start();
  }
})();
