document.addEventListener('DOMContentLoaded', () => {
    const buttons = document.querySelectorAll('.tabs button');
    const content = document.getElementById('contenu-dynamique');

    function loadPage(url) {
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.text())
        .then(html => {
            content.innerHTML = html;
        })
        .catch(() => {
            content.innerHTML = '<p>Erreur de chargement.</p>';
        });
    }

    buttons.forEach(button => {
        button.addEventListener('click', () => {
            buttons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            loadPage(button.dataset.page);
        });
    });

    // Charger automatiquement la première page
    loadPage(buttons[0].dataset.page);
});
