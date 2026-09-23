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
})();
