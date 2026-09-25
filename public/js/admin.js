(() => {
    const shell = document.querySelector('[data-admin-shell]');
    if (!shell) {
        return;
    }

    const sidebar = document.getElementById('admin-sidebar');
    const backdrop = shell.querySelector('[data-admin-backdrop]');
    const openButton = shell.querySelector('[data-admin-open]');
    const desktop = window.matchMedia('(min-width: 1024px)');
    let lastFocus = null;

    const isDesktop = () => desktop.matches;

    const focusable = () => {
        if (!sidebar) {
            return [];
        }

        return [...sidebar.querySelectorAll('a[href], button:not([disabled])')];
    };

    const setOpen = (open) => {
        const active = open && !isDesktop();
        shell.classList.toggle('is-nav-open', active);
        document.body.classList.toggle('admin-nav-locked', active);

        if (openButton) {
            openButton.setAttribute('aria-expanded', active ? 'true' : 'false');
            openButton.setAttribute('aria-label', active ? 'Close menu' : 'Open menu');
        }

        backdrop?.setAttribute('aria-hidden', active ? 'false' : 'true');

        if (!sidebar) {
            return;
        }

        if (isDesktop()) {
            sidebar.removeAttribute('inert');
            sidebar.removeAttribute('aria-hidden');
            return;
        }

        if (active) {
            sidebar.removeAttribute('inert');
            sidebar.setAttribute('aria-hidden', 'false');
            return;
        }

        sidebar.setAttribute('inert', '');
        sidebar.setAttribute('aria-hidden', 'true');
    };

    const close = (restoreFocus) => {
        setOpen(false);
        if (restoreFocus && lastFocus && typeof lastFocus.focus === 'function') {
            lastFocus.focus();
        }
    };

    openButton?.addEventListener('click', () => {
        const willOpen = !shell.classList.contains('is-nav-open');
        if (!willOpen) {
            close(true);
            return;
        }

        lastFocus = openButton;
        setOpen(true);
        sidebar?.querySelector('[data-admin-close]')?.focus();
    });

    shell.querySelectorAll('[data-admin-close]').forEach((element) => {
        element.addEventListener('click', () => close(true));
    });

    sidebar?.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => setOpen(false));
    });

    document.addEventListener('keydown', (event) => {
        if (!shell.classList.contains('is-nav-open')) {
            return;
        }

        if (event.key === 'Escape') {
            close(true);
            return;
        }

        if (event.key !== 'Tab') {
            return;
        }

        const items = focusable();
        if (items.length === 0) {
            return;
        }

        const first = items[0];
        const last = items[items.length - 1];

        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    });

    const onBreakpoint = () => setOpen(false);
    if (typeof desktop.addEventListener === 'function') {
        desktop.addEventListener('change', onBreakpoint);
    } else if (typeof desktop.addListener === 'function') {
        desktop.addListener(onBreakpoint);
    }

    setOpen(false);
})();
