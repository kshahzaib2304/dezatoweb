(() => {
    "use strict";

    const root = document.querySelector("[data-menu-listing]");
    if (!root) return;

    const grid = root.querySelector("[data-menu-grid]");
    const status = root.querySelector("[data-menu-status]");
    const moreWrap = root.querySelector("[data-menu-more]");
    const loadBtn = root.querySelector("[data-menu-load-more]");
    const sentinel = root.querySelector("[data-menu-sentinel]");
    const pager = root.querySelector("[data-menu-pager]");

    // Progressive loading is page-1 only. Deeper ?page=n URLs stay classic
    // paginated pages for crawlers, Back button, and shared links.
    const startPage = Number(root.dataset.currentPage || 1);
    if (!grid || !loadBtn || startPage > 1) return;

    let currentPage = startPage;
    let lastPage = Number(root.dataset.lastPage || 1);
    let total = Number(root.dataset.total || 0);
    let busy = false;
    let io = null;

    const prefersReducedMotion = window.matchMedia(
        "(prefers-reduced-motion: reduce)",
    ).matches;

    const hasMore = () => currentPage < lastPage;

    const syncControls = () => {
        const next = hasMore();
        if (moreWrap) moreWrap.hidden = !next;
        loadBtn.disabled = !next || busy;
        loadBtn.setAttribute("aria-busy", busy ? "true" : "false");

        if (pager) {
            pager.hidden = false;
        }

        if (!next) {
            io?.disconnect();
            io = null;
        }
    };

    const updateUrl = (pageUrl) => {
        if (!pageUrl || !window.history?.replaceState) return;
        try {
            const next = new URL(pageUrl, window.location.origin);
            next.searchParams.delete("partial");
            window.history.replaceState({ menuPage: currentPage }, "", next);
        } catch (e) {}
    };

    const announceShown = (shown) => {
        if (!status) return;
        if (total < 1) {
            status.textContent = "No treats match these filters.";
            return;
        }
        status.textContent = `Showing 1 - ${shown} of ${total}`;
    };

    const revealCards = (nodes) => {
        nodes.forEach((node) => {
            if (!(node instanceof HTMLElement)) return;
            if (prefersReducedMotion || node.hasAttribute("data-reveal")) {
                node.classList.add("is-visible");
            }
        });
    };

    const fetchPage = async (url) => {
        const target = new URL(url, window.location.origin);
        target.searchParams.set("partial", "1");

        const response = await fetch(target.toString(), {
            headers: {
                Accept: "application/json",
                "X-Requested-With": "XMLHttpRequest",
                "X-Menu-Partial": "1",
            },
            credentials: "same-origin",
        });

        if (!response.ok) {
            throw new Error("Menu page failed to load");
        }

        return response.json();
    };

    const loadNext = async () => {
        if (busy || !hasMore()) return;

        const nextUrl = loadBtn.getAttribute("data-next-url");
        if (!nextUrl) return;

        busy = true;
        syncControls();
        loadBtn.textContent = "Loading…";

        try {
            const payload = await fetchPage(nextUrl);
            const template = document.createElement("template");
            template.innerHTML = String(payload.html || "").trim();
            const cards = [...template.content.children];
            grid.append(...cards);
            revealCards(cards);

            currentPage = Number(payload.page || currentPage + 1);
            lastPage = Number(payload.last_page || lastPage);
            total = Number(payload.total || total);
            root.dataset.currentPage = String(currentPage);
            root.dataset.lastPage = String(lastPage);
            root.dataset.total = String(total);

            if (payload.next_url) {
                loadBtn.setAttribute("data-next-url", payload.next_url);
            } else {
                loadBtn.removeAttribute("data-next-url");
            }

            const shown = grid.querySelectorAll(".product-card").length;
            announceShown(shown);
            updateUrl(nextUrl);
        } catch (error) {
            window.location.href = nextUrl;
            return;
        } finally {
            busy = false;
            loadBtn.textContent = "Load more treats";
            syncControls();
        }
    };

    loadBtn.addEventListener("click", () => {
        loadNext();
    });

    if (sentinel && "IntersectionObserver" in window && hasMore()) {
        io = new IntersectionObserver(
            (entries) => {
                if (entries.some((entry) => entry.isIntersecting)) {
                    loadNext();
                }
            },
            {
                root: null,
                rootMargin: "280px 0px",
                threshold: 0,
            },
        );
        io.observe(sentinel);
    }

    syncControls();
})();
