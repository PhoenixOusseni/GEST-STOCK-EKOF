<script>
    let articleIndex = parseInt(document.getElementById('bon-form').dataset.lineCount, 10) || 1;

    function initSelect2Client(el) {
        $(el || '.select2-client').select2({
            width: '100%',
            placeholder: 'Rechercher un client...',
            allowClear: true,
            ajax: {
                url: '{{ route('clients.search') }}',
                dataType: 'json',
                delay: 250,
                data: params => ({ search: params.term }),
                processResults: data => ({
                    results: data.map(c => ({ id: c.id, text: c.raison_sociale || c.nom }))
                })
            }
        });
    }

    function initSelect2Article(el) {
        $(el).select2({
            width: '100%',
            placeholder: 'Rechercher un article...',
            allowClear: true,
            ajax: {
                url: '{{ route('articles.search') }}',
                dataType: 'json',
                delay: 250,
                data: params => ({ search: params.term }),
                processResults: data => ({
                    results: data.map(a => ({
                        id: a.id,
                        text: a.designation + ' (' + a.code + ')',
                        prix: a.prix_vente
                    }))
                })
            }
        });
    }

    function addArticleEvents(row) {
        const articleSelect = row.querySelector('.article-select');
        const prixVenteInput = row.querySelector('.prix-vente-input');
        const quantiteInput = row.querySelector('.quantite-input');
        $(articleSelect).on('select2:select', function (e) {
            if (e.params.data.prix) prixVenteInput.value = e.params.data.prix;
            updateTotal();
        });
        prixVenteInput.addEventListener('input', updateTotal);
        quantiteInput.addEventListener('input', updateTotal);
    }

    function updateTotal() {
        let total = 0;
        document.querySelectorAll('.article-row').forEach(function (row) {
            const qte = parseFloat(row.querySelector('.quantite-input').value) || 0;
            const prix = parseFloat(row.querySelector('.prix-vente-input').value) || 0;
            total += qte * prix;
        });
        document.getElementById('total-display').textContent = total.toLocaleString('fr-FR') + ' FCFA';
        document.getElementById('articles-count').textContent = document.querySelectorAll('.article-row').length;
        const paye = parseFloat(document.getElementById('montant_paye').value) || 0;
        const reste = Math.max(0, total - paye);
        const resteInput = document.getElementById('reste_a_payer_display');
        resteInput.value = reste.toLocaleString('fr-FR') + ' FCFA';
        resteInput.classList.toggle('text-danger', reste > 0);
        resteInput.classList.toggle('text-success', reste <= 0);
    }

    document.getElementById('montant_paye').addEventListener('input', updateTotal);

    document.getElementById('add-article').addEventListener('click', function () {
        const container = document.getElementById('articles-container');
        const newArticle = document.querySelector('.article-row').cloneNode(true);
        newArticle.querySelectorAll('.select2-container').forEach(el => el.remove());
        newArticle.querySelectorAll('select').forEach(el => {
            el.style.display = '';
            el.removeAttribute('data-select2-id');
            el.selectedIndex = 0;
        });
        newArticle.querySelectorAll('input').forEach(input => {
            if (input.type !== 'hidden') input.value = '';
        });
        newArticle.querySelectorAll('[name]').forEach(field => {
            field.setAttribute('name', field.getAttribute('name').replace(/\[\d+\]/, '[' + articleIndex + ']'));
        });
        container.appendChild(newArticle);
        articleIndex++;
        initSelect2Article(newArticle.querySelector('.select2-article'));
        addArticleEvents(newArticle);
        updateTotal();
    });

    document.addEventListener('click', function (e) {
        if (!e.target.classList.contains('remove-article') && !e.target.closest('.remove-article')) return;
        if (document.querySelectorAll('.article-row').length <= 1) {
            alert('Le bon doit contenir au moins un article.');
            return;
        }
        const row = e.target.closest('.article-row');
        $(row).find('.select2-article').select2('destroy');
        row.remove();
        updateTotal();
    });

    $(document).ready(function () {
        initSelect2Client('.select2-client');
        document.querySelectorAll('.select2-article').forEach(initSelect2Article);
        document.querySelectorAll('.article-row').forEach(addArticleEvents);
        updateTotal();
    });
</script>
