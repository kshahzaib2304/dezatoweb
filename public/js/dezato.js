(() => {
    "use strict";

    const doc = document;
    const body = doc.body;

    /* ---------- Cake baking page loader (first visit only) ---------- */
    const bakeLoader = doc.getElementById("bake-loader");
    const skipBakeLoader =
        doc.documentElement.classList.contains("skip-bake-loader");

    if (bakeLoader && skipBakeLoader) {
        bakeLoader.remove();
    } else if (bakeLoader) {
        const reduceMotion = window.matchMedia(
            "(prefers-reduced-motion: reduce)",
        ).matches;
        // Brand moment on first open only - never block later navigations.
        const minMs = reduceMotion ? 0 : 280;
        const started = performance.now();
        body.classList.add("is-baking");

        const finish = () => {
            try {
                sessionStorage.setItem("dezato.loaderSeen", "1");
            } catch (e) {}

            const wait = Math.max(0, minMs - (performance.now() - started));
            window.setTimeout(() => {
                bakeLoader.classList.add("is-done");
                bakeLoader.setAttribute("aria-busy", "false");
                body.classList.remove("is-baking");
                doc.documentElement.classList.add("skip-bake-loader");
                window.setTimeout(() => bakeLoader.remove(), 400);
            }, wait);
        };

        if (doc.readyState === "interactive" || doc.readyState === "complete") {
            finish();
        } else {
            doc.addEventListener("DOMContentLoaded", finish, { once: true });
        }
    }

    /* ---------- Mobile drawer ---------- */
    const drawer = doc.getElementById("nav-drawer");
    const overlay = doc.getElementById("nav-overlay");
    const openBtns = doc.querySelectorAll("[data-nav-open]");
    const closeBtns = doc.querySelectorAll("[data-nav-close]");

    const setNav = (open) => {
        if (!drawer) return;
        drawer.classList.toggle("is-open", open);
        overlay?.classList.toggle("is-open", open);
        body.classList.toggle("nav-locked", open);
        drawer.setAttribute("aria-hidden", open ? "false" : "true");
        openBtns.forEach((btn) =>
            btn.setAttribute("aria-expanded", open ? "true" : "false"),
        );
        if (open) {
            drawer.querySelector("a, button")?.focus({ preventScroll: true });
        }
    };

    openBtns.forEach((btn) =>
        btn.addEventListener("click", () => setNav(true)),
    );
    closeBtns.forEach((btn) =>
        btn.addEventListener("click", () => setNav(false)),
    );
    overlay?.addEventListener("click", () => setNav(false));

    drawer?.querySelectorAll("a").forEach((link) => {
        link.addEventListener("click", () => setNav(false));
    });

    /* ---------- Accordion in drawer ---------- */
    doc.querySelectorAll("[data-accordion]").forEach((btn) => {
        btn.addEventListener("click", () => {
            const panel = btn.nextElementSibling;
            const expanded = btn.getAttribute("aria-expanded") === "true";
            btn.setAttribute("aria-expanded", expanded ? "false" : "true");
            panel?.classList.toggle("is-open", !expanded);
        });
    });

    /* ---------- Sticky header shadow ---------- */
    const header = doc.querySelector(".site-header");
    const onScroll = () => {
        if (!header) return;
        header.classList.toggle("is-scrolled", window.scrollY > 8);
    };
    onScroll();
    window.addEventListener("scroll", onScroll, { passive: true });

    /* ---------- Reveal on scroll ---------- */
    const reveals = doc.querySelectorAll("[data-reveal]");
    // After the first visit, show content immediately - staggered fades feel like "loading" on every click.
    if (
        skipBakeLoader ||
        window.matchMedia("(prefers-reduced-motion: reduce)").matches
    ) {
        reveals.forEach((el) => el.classList.add("is-visible"));
    } else if (reveals.length && "IntersectionObserver" in window) {
        const io = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add("is-visible");
                        io.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.12, rootMargin: "0px 0px -40px 0px" },
        );
        reveals.forEach((el) => io.observe(el));
    } else {
        reveals.forEach((el) => el.classList.add("is-visible"));
    }

    /* ---------- Active chip into view (menu) ---------- */
    const activeChip = doc.querySelector(".chip-row .chip.is-active");
    if (activeChip && typeof activeChip.scrollIntoView === "function") {
        activeChip.scrollIntoView({
            inline: "center",
            block: "nearest",
            behavior: "auto",
        });
    }

    /* ---------- Fulfillment chooser (Delivery / Pick-Up) ---------- */
    const fulfillment = doc.querySelector("[data-fulfillment]");
    const SEEN_KEY = "dezato.fulfillmentSeen";

    const markFulfillmentSeen = () => {
        try {
            localStorage.setItem(SEEN_KEY, "1");
        } catch (e) {}
    };

    const hasSeenFulfillment = () => {
        try {
            return localStorage.getItem(SEEN_KEY) === "1";
        } catch (e) {
            return false;
        }
    };

    if (fulfillment) {
        const methodInput = doc.getElementById("fulfillment-method");
        const locationInput = fulfillment.querySelector("[data-fulfillment-location]");
        const addressInput = fulfillment.querySelector("[data-fulfillment-address]");
        const areaSelect = fulfillment.querySelector("[data-fulfillment-area]");
        const submitBtn = fulfillment.querySelector("[data-fulfillment-submit]");
        const locateBtn = fulfillment.querySelector("[data-fulfillment-locate]");
        const locateHint = fulfillment.querySelector("[data-fulfillment-locate-hint]");
        const tabs = [...fulfillment.querySelectorAll("[data-fulfillment-tab]")];

        const syncFromArea = () => {
            const option = areaSelect?.selectedOptions?.[0];
            const hasArea = Boolean(option && option.value);

            if (locationInput) {
                locationInput.value = hasArea
                    ? option.getAttribute("data-location-id") || ""
                    : "";
            }

            if (addressInput) {
                const method = methodInput?.value || "delivery";
                addressInput.value =
                    method === "delivery" && hasArea
                        ? option.getAttribute("data-label") || option.textContent.trim()
                        : "";
            }

            if (submitBtn) {
                submitBtn.disabled = !hasArea;
            }
        };

        const setMethod = (method) => {
            const next =
                method === "pickup" || method === "delivery" ? method : "delivery";

            if (methodInput) methodInput.value = next;

            tabs.forEach((tab) => {
                const active = tab.getAttribute("data-fulfillment-tab") === next;
                tab.classList.toggle("is-active", active);
                tab.setAttribute("aria-selected", active ? "true" : "false");
            });

            syncFromArea();
        };

        tabs.forEach((tab) => {
            tab.addEventListener("click", () => {
                setMethod(tab.getAttribute("data-fulfillment-tab") || "delivery");
            });
        });

        areaSelect?.addEventListener("change", syncFromArea);

        locateBtn?.addEventListener("click", () => {
            if (!navigator.geolocation) {
                if (locateHint) {
                    locateHint.hidden = false;
                    locateHint.textContent =
                        "Location is not available on this device. Pick your area below.";
                }
                return;
            }

            locateBtn.disabled = true;
            if (locateHint) {
                locateHint.hidden = false;
                locateHint.textContent = "Finding your area…";
            }

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    locateBtn.disabled = false;
                    const { latitude, longitude } = position.coords;
                    // Nearest counter: DHA Phase 6 ≈ 24.814, 67.064 · Gizri ≈ 24.814, 67.051
                    const dha =
                        (latitude - 24.814) ** 2 + (longitude - 67.064) ** 2;
                    const gizri =
                        (latitude - 24.814) ** 2 + (longitude - 67.051) ** 2;
                    const storeId = dha <= gizri ? "dha-phase-6" : "gizri";
                    const match = [...(areaSelect?.options || [])].find(
                        (option) =>
                            option.value &&
                            option.getAttribute("data-location-id") === storeId,
                    );

                    if (match && areaSelect) {
                        areaSelect.value = match.value;
                        syncFromArea();
                    }

                    if (locateHint) {
                        locateHint.textContent =
                            "Karachi detected. Confirm your neighbourhood below.";
                    }
                },
                () => {
                    locateBtn.disabled = false;
                    if (locateHint) {
                        locateHint.hidden = false;
                        locateHint.textContent =
                            "Could not read your location. Pick your area from the list.";
                    }
                },
                { enableHighAccuracy: false, timeout: 8000, maximumAge: 60000 },
            );
        });

        setMethod(methodInput?.value || "delivery");
        fulfillment._setMethod = setMethod;
    }

    /* ---------- Horizontal rails ---------- */
    doc.querySelectorAll("[data-rail]").forEach((rail) => {
        const viewport = rail.querySelector("[data-rail-viewport]");
        const prev = rail.querySelector("[data-rail-prev]");
        const next = rail.querySelector("[data-rail-next]");
        if (!viewport) return;

        const scrollByCard = (dir) => {
            const amount = Math.max(200, viewport.clientWidth * 0.7) * dir;
            viewport.scrollBy({ left: amount, behavior: "smooth" });
        };

        prev?.addEventListener("click", () => scrollByCard(-1));
        next?.addEventListener("click", () => scrollByCard(1));
    });

    /* ---------- Fulfillment modal (first visit / order gate) ---------- */
    const fulfillmentModal = doc.querySelector("[data-fulfillment-modal]");
    const dismissForm = doc.getElementById("fulfillment-dismiss-form");

    function setFulfillmentModal(open) {
        if (!fulfillmentModal) return;
        fulfillmentModal.hidden = !open;
        body.classList.toggle("modal-locked", open);
        if (open) {
            setNav(false);
            fulfillmentModal
                .querySelector(".fulfillment-modal__dialog")
                ?.focus({ preventScroll: true });
        }
    }

    /** Persist "welcome seen" without a full page reload. */
    function dismissWelcomeQuietly() {
        markFulfillmentSeen();
        setFulfillmentModal(false);
        if (!dismissForm) return;

        fetch(dismissForm.action, {
            method: "POST",
            body: new FormData(dismissForm),
            headers: {
                Accept: "application/json",
                "X-Requested-With": "XMLHttpRequest",
            },
            credentials: "same-origin",
        }).catch(() => {
            // Best-effort; UI already closed.
        });
    }

    doc.addEventListener("keydown", (e) => {
        if (e.key !== "Escape") return;
        if (fulfillmentModal && !fulfillmentModal.hidden) {
            dismissWelcomeQuietly();
            return;
        }
        setNav(false);
    });

    dismissForm?.addEventListener("submit", (event) => {
        event.preventDefault();
        dismissWelcomeQuietly();
    });

    doc.querySelectorAll("[data-fulfillment-open]").forEach((el) => {
        el.addEventListener("click", (event) => {
            const method = el.getAttribute("data-fulfillment-method");
            if (
                el.tagName === "A" &&
                (el.getAttribute("href") || "").includes("fulfillment=1")
            ) {
                event.preventDefault();
            }
            if (method && fulfillmentModal) {
                const root =
                    fulfillmentModal.querySelector("[data-fulfillment]");
                root?._setMethod?.(method);
            }
            setFulfillmentModal(true);
        });
    });

    fulfillmentModal
        ?.querySelectorAll("[data-fulfillment-dismiss]")
        .forEach((el) => {
            el.addEventListener("click", (event) => {
                event.preventDefault();
                dismissWelcomeQuietly();
            });
        });

    fulfillment?.querySelector("[data-fulfillment-form]")?.addEventListener(
        "submit",
        () => {
            markFulfillmentSeen();
        },
    );

    if (fulfillmentModal?.getAttribute("data-auto-open") === "1") {
        const forced = fulfillmentModal.getAttribute("data-force-open") === "1";
        if (forced || !hasSeenFulfillment()) {
            setFulfillmentModal(true);
        } else {
            dismissWelcomeQuietly();
        }
    }
})();
