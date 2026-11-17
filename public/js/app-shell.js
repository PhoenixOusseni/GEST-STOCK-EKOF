document.addEventListener('DOMContentLoaded', function () {
    var root = document.documentElement;
    var toggle = document.getElementById('sidebarToggle');
    var collapseBtn = document.getElementById('sidebarCollapse');
    var backdrop = document.getElementById('sidebarBackdrop');
    var desktop = function () { return window.innerWidth >= 992; };

    function setCollapsed(collapsed) {
        root.classList.toggle('sidebar-collapsed', collapsed);
        try {
            localStorage.setItem('mp-sidebar', collapsed ? 'collapsed' : 'expanded');
        } catch (e) {}
    }

    function setMobile(open) {
        root.classList.toggle('sidebar-mobile-open', open);
        if (backdrop) backdrop.hidden = !open;
    }

    function onToggle() {
        if (desktop()) {
            setCollapsed(!root.classList.contains('sidebar-collapsed'));
        } else {
            setMobile(!root.classList.contains('sidebar-mobile-open'));
        }
    }

    if (toggle) toggle.addEventListener('click', onToggle);
    if (collapseBtn) collapseBtn.addEventListener('click', onToggle);
    if (backdrop) backdrop.addEventListener('click', function () { setMobile(false); });

    document.querySelectorAll('#appSidebar a.nav-link').forEach(function (link) {
        link.addEventListener('click', function () {
            if (!desktop()) setMobile(false);
        });
    });

    document.querySelectorAll('.nav-group-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (desktop() && root.classList.contains('sidebar-collapsed')) return;
            var group = btn.closest('.nav-group');
            var open = group.classList.toggle('is-open');
            btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    });

    var input = document.getElementById('shellSearch');
    var panel = document.getElementById('shellSearchResults');
    var links = Array.prototype.map.call(
        document.querySelectorAll('#appSidebar a.nav-link'),
        function (anchor) {
            return {
                href: anchor.getAttribute('href'),
                label: (anchor.querySelector('.nav-label') || anchor).textContent.trim(),
                section: anchor.getAttribute('data-section') || 'Menu',
                icon: (anchor.querySelector('i') || {}).className || 'bi bi-arrow-right'
            };
        }
    );
    var activeIndex = -1;

    function hideResults() {
        if (!panel) return;
        panel.hidden = true;
        panel.innerHTML = '';
        activeIndex = -1;
    }

    function renderResults(query) {
        var q = query.trim().toLowerCase();
        if (!q) {
            hideResults();
            return;
        }
        var matches = links.filter(function (item) {
            return (item.label + ' ' + item.section).toLowerCase().indexOf(q) !== -1;
        }).slice(0, 8);

        panel.hidden = false;
        if (!matches.length) {
            panel.innerHTML = '<div class="search-empty">Aucun menu ne correspond.</div>';
            return;
        }

        panel.innerHTML = matches.map(function (item, index) {
            return '<a href="' + item.href + '" data-index="' + index + '">' +
                '<i class="' + item.icon + '"></i>' +
                '<span><strong>' + item.label + '</strong><small>' + item.section + '</small></span>' +
                '</a>';
        }).join('');
        activeIndex = -1;
    }

    if (input && panel) {
        input.addEventListener('input', function () { renderResults(input.value); });
        input.addEventListener('focus', function () {
            if (input.value.trim()) renderResults(input.value);
        });
        input.addEventListener('keydown', function (event) {
            var items = panel.querySelectorAll('a');
            if (event.key === 'Escape') {
                hideResults();
                input.blur();
                return;
            }
            if (!items.length || panel.hidden) return;
            if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                event.preventDefault();
                activeIndex += event.key === 'ArrowDown' ? 1 : -1;
                if (activeIndex < 0) activeIndex = items.length - 1;
                if (activeIndex >= items.length) activeIndex = 0;
                items.forEach(function (item, index) {
                    item.classList.toggle('is-active', index === activeIndex);
                });
            }
            if (event.key === 'Enter' && activeIndex >= 0) {
                event.preventDefault();
                window.location.href = items[activeIndex].getAttribute('href');
            }
        });
        document.addEventListener('click', function (event) {
            if (!event.target.closest('.topbar-search')) hideResults();
        });
        document.addEventListener('keydown', function (event) {
            if (event.key === '/' && document.activeElement &&
                !/input|textarea|select/i.test(document.activeElement.tagName) &&
                !document.activeElement.isContentEditable) {
                event.preventDefault();
                input.focus();
            }
        });
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') setMobile(false);
    });

    window.addEventListener('resize', function () {
        if (desktop()) setMobile(false);
    });
});
