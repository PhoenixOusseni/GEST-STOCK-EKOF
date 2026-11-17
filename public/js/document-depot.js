(function () {
    document.addEventListener('DOMContentLoaded', function () {
        const depot = document.getElementById('depot_id');
        const form = depot ? depot.closest('form') : null;
        const box = document.getElementById('articles-container');
        if (!depot || !form || !box) return;

        const stockUrl = form.dataset.stockUrl || '';
        const guard = form.dataset.stockGuard === '1';
        const tokenInput = form.querySelector('input[name="_token"]');

        function articleIdOf(row) {
            const select = row.querySelector('select.article-select, select.select2-article');
            if (!select) return '';
            return window.jQuery ? (jQuery(select).val() || '') : (select.value || '');
        }

        function qtyInputOf(row) {
            return row.querySelector('.quantite-input, input[name="quantites[]"]');
        }

        function setLocked(locked) {
            form.classList.toggle('depot-pending', locked);
        }

        function applyStock(row, qty) {
            const out = row.querySelector('.stock-disponible');
            if (!out) return;
            out.value = qty;
            out.dataset.qty = qty;
            const qteInput = qtyInputOf(row);
            if (guard && qteInput) {
                const qte = parseFloat(qteInput.value) || 0;
                qteInput.classList.toggle('is-invalid', qte > parseFloat(qty));
            }
        }

        function refresh(row) {
            const out = row.querySelector('.stock-disponible');
            const articleId = articleIdOf(row);
            if (!out) return;
            if (!articleId || !depot.value || !stockUrl) {
                out.value = '';
                out.dataset.qty = '';
                return;
            }
            fetch(stockUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': tokenInput ? tokenInput.value : ''
                },
                body: JSON.stringify({ article_id: articleId, depot_id: depot.value })
            })
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    applyStock(row, data.quantite_disponible || 0);
                })
                .catch(function () {
                    applyStock(row, 0);
                });
        }

        function refreshAll() {
            box.querySelectorAll('.article-row').forEach(refresh);
        }

        depot.addEventListener('change', function () {
            setLocked(!depot.value);
            refreshAll();
        });

        document.addEventListener('change', function (e) {
            const select = e.target.closest('select.article-select, select.select2-article');
            if (!select || !box.contains(select)) return;
            const row = select.closest('.article-row');
            if (row) refresh(row);
        });

        if (window.jQuery) {
            jQuery(document).on('select2:select', '.article-select, .select2-article', function () {
                const row = this.closest('.article-row');
                if (row && box.contains(row)) refresh(row);
            });
        }

        box.addEventListener('input', function (e) {
            if (!guard) return;
            const row = e.target.closest('.article-row');
            if (!row) return;
            const qteInput = qtyInputOf(row);
            const out = row.querySelector('.stock-disponible');
            if (qteInput && out && e.target === qteInput) {
                const qte = parseFloat(qteInput.value) || 0;
                const dispo = parseFloat(out.dataset.qty || out.value) || 0;
                qteInput.classList.toggle('is-invalid', qte > dispo);
            }
        });

        form.addEventListener('submit', function (e) {
            if (!depot.value) {
                e.preventDefault();
                alert('Sélectionnez d\'abord le magazin.');
                depot.focus();
                return;
            }
            if (!guard) return;

            const byArticle = {};
            let blocked = false;
            let pending = false;
            box.querySelectorAll('.article-row').forEach(function (row) {
                const articleId = articleIdOf(row);
                const qteInput = qtyInputOf(row);
                const out = row.querySelector('.stock-disponible');
                if (!articleId || !qteInput || !out) return;
                if (out.dataset.qty === undefined || out.dataset.qty === '') {
                    pending = true;
                    return;
                }
                const qte = parseFloat(qteInput.value) || 0;
                const dispo = parseFloat(out.dataset.qty) || 0;
                if (!byArticle[articleId]) {
                    byArticle[articleId] = { dispo: dispo, qte: 0, inputs: [] };
                }
                byArticle[articleId].qte += qte;
                byArticle[articleId].inputs.push(qteInput);
            });

            if (pending) {
                e.preventDefault();
                alert('Le stock du magazin est encore en cours de calcul. Patientez une seconde, puis enregistrez.');
                return;
            }

            Object.keys(byArticle).forEach(function (id) {
                const item = byArticle[id];
                if (item.qte > item.dispo) {
                    blocked = true;
                    item.inputs.forEach(function (input) { input.classList.add('is-invalid'); });
                }
            });

            if (blocked) {
                e.preventDefault();
                alert('La quantité demandée dépasse le stock réel de ce magazin. L\'enregistrement est refusé.');
            }
        });

        setLocked(!depot.value);
        if (depot.value) {
            refreshAll();
            setTimeout(refreshAll, 400);
        }
    });
})();
