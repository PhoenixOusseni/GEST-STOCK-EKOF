/**
 * SAFRECO GSM - Modern Application JavaScript
 */

document.addEventListener('DOMContentLoaded', function () {

    // ========== TopNav Mobile Toggle ==========
    const toggler = document.querySelector('.topnav-toggler');
    const navMenu = document.getElementById('topnavMenu');

    if (toggler && navMenu) {
        toggler.addEventListener('click', function (e) {
            e.stopPropagation();
            navMenu.classList.toggle('open');
        });

        // Close menu when clicking outside
        document.addEventListener('click', function (e) {
            if (!navMenu.contains(e.target) && !toggler.contains(e.target)) {
                navMenu.classList.remove('open');
            }
        });
    }

    // ========== Custom Sub-menu Dropdowns ==========
    document.querySelectorAll('.topnav-link-dropdown').forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            const wrap = this.closest('.topnav-dropdown-wrap');
            const sub  = wrap ? wrap.querySelector('.topnav-sub') : null;
            if (!sub) return;

            const isOpen = sub.classList.contains('open');

            // Close all other open sub-menus
            document.querySelectorAll('.topnav-sub.open').forEach(function (el) {
                el.classList.remove('open');
                const parentLink = el.closest('.topnav-dropdown-wrap').querySelector('.topnav-link-dropdown .topnav-arrow');
                if (parentLink) parentLink.style.transform = '';
            });

            if (!isOpen) {
                sub.classList.add('open');
                const arrow = this.querySelector('.topnav-arrow');
                if (arrow) arrow.style.transform = 'rotate(180deg)';
            }
        });
    });

    // Close sub-menus when clicking outside
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.topnav-dropdown-wrap')) {
            document.querySelectorAll('.topnav-sub.open').forEach(function (sub) {
                sub.classList.remove('open');
            });
            document.querySelectorAll('.topnav-arrow').forEach(function (arrow) {
                arrow.style.transform = '';
            });
        }
    });

    // ========== Mobile Search Toggle ==========
    const searchToggle = document.getElementById('searchToggle');
    const mobileSearch = document.getElementById('mobileSearch');
    const searchClose  = document.getElementById('searchClose');

    if (searchToggle && mobileSearch) {
        searchToggle.addEventListener('click', function () {
            mobileSearch.classList.toggle('show');
            if (mobileSearch.classList.contains('show')) {
                mobileSearch.querySelector('input').focus();
            }
        });
    }

    if (searchClose && mobileSearch) {
        searchClose.addEventListener('click', function () {
            mobileSearch.classList.remove('show');
        });
    }

    // ========== Back to Top Button ==========
    const backToTopBtn = document.querySelector('.back-to-top');

    if (backToTopBtn) {
        window.addEventListener('scroll', function () {
            backToTopBtn.classList.toggle('show', window.pageYOffset > 300);
        });

        backToTopBtn.addEventListener('click', function (e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // ========== DataTable Initialization ==========
    if (typeof $ !== 'undefined' && $.fn.dataTable) {
        const dataTables = document.querySelectorAll('table[id^="datatable"]');
        dataTables.forEach(function (table) {
            const tableId = '#' + table.getAttribute('id');
            if (!$.fn.dataTable.isDataTable(tableId)) {
                $(tableId).dataTable({
                    paging: true,
                    lengthChange: true,
                    searching: true,
                    ordering: true,
                    info: true,
                    autoWidth: false,
                    responsive: true,
                    language: {
                        sEmptyTable:    "Aucune donnée disponible",
                        sInfo:          "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
                        sInfoEmpty:     "Affichage de 0 à 0 sur 0 entrées",
                        sInfoFiltered:  "(filtré à partir de _MAX_ entrées totales)",
                        sLengthMenu:    "Afficher _MENU_ entrées",
                        sLoadingRecords:"Chargement...",
                        sProcessing:    "Traitement...",
                        sSearch:        "Rechercher :",
                        sSearchPlaceholder: "Rechercher...",
                        sZeroRecords:   "Aucun enregistrement trouvé",
                        oPaginate: {
                            sFirst:    "Première",
                            sLast:     "Dernière",
                            sNext:     "Suivante",
                            sPrevious: "Précédente"
                        }
                    }
                });
            }
        });
    }

    // ========== Select2 Initialization ==========
    if (typeof $ !== 'undefined' && $.fn.select2) {
        $('.select2').select2({
            allowClear: true,
            placeholder: 'Sélectionner une option...',
            theme: 'bootstrap-5',
            width: '100%'
        });
    }

    // ========== Form Validation ==========
    document.querySelectorAll('form[data-validate="true"]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });

    // ========== Delete Confirmation ==========
    document.querySelectorAll('[data-confirm="delete"]').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer cet élément ? Cette action est irréversible.')) {
                e.preventDefault();
            }
        });
    });

    // ========== Submit Loading State ==========
    document.querySelectorAll('button[type="submit"][data-loading-text]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const loadingText = this.getAttribute('data-loading-text');
            this.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>${loadingText}`;
            this.disabled = true;
        });
    });

    // ========== ESC Key ==========
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            if (navMenu) navMenu.classList.remove('show');
            if (mobileSearch) mobileSearch.classList.remove('show');
        }
    });

    console.log('SAFRECO GSM initialized');
});
