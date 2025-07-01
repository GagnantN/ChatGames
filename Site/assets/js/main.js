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

        document.addEventListener('click', function (e) {
        const link = e.target.closest('a[data-page]');
        if (link) {
            e.preventDefault();
            const page = link.getAttribute('data-page');
            if (typeof loadPage === 'function') {
                loadPage(page);
            }
        }
    });

    window.loadPage = loadPage;


    buttons.forEach(button => {
        button.addEventListener('click', () => {
            buttons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');

            // Mettre à jour le fil d’Ariane
            const breadcrumb = document.getElementById('filAriane');
            if (breadcrumb) {
                const pageName = button.textContent.trim();
                breadcrumb.textContent = 'Mon profil > ' + pageName;
            }
            loadPage(button.dataset.page);
        });
    });

    // Charger automatiquement la première page
    loadPage(buttons[0].dataset.page);


});

document.addEventListener('submit', function (e) {
    if (e.target.classList.contains('form-ajout-jeu')) {
        e.preventDefault();

        const form = e.target;
        const idJeu = form.dataset.id;

        fetch('index.php?page=ajouterJeu', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'Id_Jeu=' + encodeURIComponent(idJeu)
        })
        .then(res => res.text())
        .then(() => {
            // Recharge dynamiquement mesJeux sans simuler de clic
            if (typeof loadPage === 'function') {
                loadPage('index.php?page=mesJeux');
            }
        })
        .catch(err => {
            console.error('Erreur lors de l’ajout du jeu', err);
        });
    }
});




// Clique pour changer l'etat des buttons de disponibilité
document.querySelectorAll('.dispo-btn').forEach(button => {
    button.addEventListener('click', () => {
        const img = button.querySelector('img');
        const jour = button.dataset.jour;
        const moment = button.dataset.moment;
        const hiddenInput = document.querySelector(`input[name="disponibilites[${jour}][${moment}]"]`);
        
        const isActive = !img.src.includes('Invisible');
        if (isActive) {
            img.src = img.src.replace('.png', 'Invisible.png');
            hiddenInput.value = 0;
        } else {
            img.src = img.src.replace('Invisible.png', '.png');
            hiddenInput.value = 1;
        }
    });
});

// Permet d'afficher le rendu de l'image avant de la modifier dans la base de donnée.
document.getElementById('imageProfil').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function(e) {
        const img = document.querySelector('.photo-image');
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);
});


document.addEventListener('DOMContentLoaded', () => {
    const fileInput = document.getElementById('imageProfil');
    const imagePreview = document.querySelector('.photo-image');

    if (fileInput && imagePreview) {
        fileInput.addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function (e) {
                imagePreview.src = e.target.result;
            };
            reader.readAsDataURL(file);
        });
    }
});



// Partie Recherche Jeu
document.addEventListener("DOMContentLoaded", () => {
    const inputJeu = document.getElementById("search-jeu");
    const resultatsJeux = document.getElementById("resultats-jeux");

    // Vérifier que les éléments existent (on est sur la page de recherche de jeux)
    if (!inputJeu || !resultatsJeux) return;

    // Recherche en temps réel pendant la saisie
    inputJeu.addEventListener("input", () => {
        const valeur = inputJeu.value.trim();

        // Si la valeur est vide, recharger tous les jeux
        if (valeur.length === 0) {
            window.location.href = 'index.php?page=rechercheJeu';
            return;
        }

        // Faire la requête AJAX
        fetch(`index.php?page=rechercheJeu&search=${encodeURIComponent(valeur)}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            } else {
                throw new Error('La réponse n\'est pas en JSON');
            }
        })
        .then(data => {
            resultatsJeux.innerHTML = "";

            if (data.length === 0) {
                resultatsJeux.innerHTML = `<p>Aucun jeu trouvé pour "${valeur}"</p>`;
                return;
            }

            data.forEach(jeu => {
                const div = document.createElement("div");
                div.className = "carte-jeu";
                div.setAttribute('data-nom', jeu.nom.toLowerCase());
                div.innerHTML = `
                    <img src="${jeu.images || 'Site/assets/images/default-game.png'}" alt="${jeu.nom}" class="image-jeu">
                    <div class="contenu-carte">
                        <h2>${jeu.nom}</h2>
                        <a href="index.php?page=detailJeu&id=${jeu.Id_Jeu}" class="btn-en-savoir-plus">En savoir plus sur le jeu</a>
                    </div>
                `;
                resultatsJeux.appendChild(div);
            });
        })
        .catch(error => {
            console.error("Erreur lors de la recherche de jeux :", error);
            resultatsJeux.innerHTML = "<p>Erreur lors de la recherche. Veuillez réessayer.</p>";
        });
    });

    // Gestion de la soumission du formulaire (quand on appuie sur Entrée)
    const form = inputJeu.closest('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault(); // Empêcher la soumission normale
            
            const valeur = inputJeu.value.trim();
            
            // Si on est dans un contexte AJAX (page profil), utiliser loadPage
            if (typeof loadPage === 'function') {
                const url = valeur ? 
                    `index.php?page=rechercheJeu&search=${encodeURIComponent(valeur)}` : 
                    'index.php?page=rechercheJeu';
                loadPage(url);
            } else {
                // Sinon, redirection normale
                const url = valeur ? 
                    `index.php?page=rechercheJeu&search=${encodeURIComponent(valeur)}` : 
                    'index.php?page=rechercheJeu';
                window.location.href = url;
            }
        });
    }
});

// Ancienne fonction de recherche locale (à garder si nécessaire pour d'autres usages)
function rechercheJeuLocal() {
    const input = document.getElementById('recherche-jeu');
    if (!input) return;
    
    input.addEventListener('input', function () {
        const valeur = this.value.toLowerCase();
        document.querySelectorAll('.carte-jeu').forEach(carte => {
            const nom = carte.getAttribute('data-nom');
            if (nom) {
                carte.style.display = nom.includes(valeur) ? 'block' : 'none';
            }
        });
    });
}


// Partie Filtre de la page Communauté
function ouvrirFiltreCommunaute() {
    console.log("DEBUG: Ouverture filtre communauté");
    document.getElementById('overlay-filtre-communaute').classList.remove('hidden');
    document.getElementById('modal-filtre-communaute').classList.remove('hidden');
}

function fermerFiltreCommunaute() {
    console.log("DEBUG: Fermeture filtre communauté");
    document.getElementById('overlay-filtre-communaute').classList.add('hidden');
    document.getElementById('modal-filtre-communaute').classList.add('hidden');
}

// Recherche en temps réel pour les communautés
document.addEventListener("DOMContentLoaded", () => {
    console.log("DEBUG: DOM loaded pour communauté");
    
    const inputCommunaute = document.getElementById("search-input-communaute");
    const resultatsCommunautes = document.getElementById("resultats-communautes");

    console.log("DEBUG: Input trouvé:", inputCommunaute);
    console.log("DEBUG: Résultats trouvé:", resultatsCommunautes);

    // Vérifier que les éléments existent (on est sur la page de communauté)
    if (!inputCommunaute || !resultatsCommunautes) {
        console.log("DEBUG: Éléments non trouvés, pas sur la page communauté");
        return;
    }

    console.log("DEBUG: Event listener ajouté à l'input");

    inputCommunaute.addEventListener("input", () => {
        const valeur = inputCommunaute.value.trim();
        console.log("DEBUG: Recherche pour:", valeur);

        // Si la valeur est vide, recharger la page sans paramètre search
        if (valeur.length === 0) {
            console.log("DEBUG: Valeur vide, rechargement de la page");
            window.location.href = 'index.php?page=communaute';
            return;
        }

        // Construire l'URL de la requête
        const url = `index.php?page=communaute&search=${encodeURIComponent(valeur)}`;
        console.log("DEBUG: URL de la requête:", url);

        // Faire la requête AJAX vers la même page mais avec le header AJAX
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            console.log("DEBUG: Réponse reçue:", response);
            console.log("DEBUG: Status:", response.status);
            console.log("DEBUG: Content-Type:", response.headers.get('content-type'));
            
            // Vérifier si la réponse est JSON
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            } else {
                // Si ce n'est pas du JSON, lire le texte pour debug
                return response.text().then(text => {
                    console.log("DEBUG: Réponse HTML reçue au lieu de JSON:", text.substring(0, 200));
                    throw new Error('La réponse n\'est pas en JSON');
                });
            }
        })
        .then(data => {
            console.log("DEBUG: Données JSON reçues:", data);
            
            resultatsCommunautes.innerHTML = "";

            if (data.length === 0) {
                resultatsCommunautes.innerHTML = `<p>Aucune communauté trouvée pour "${valeur}"</p>`;
                return;
            }

            data.forEach(commu => {
                console.log("DEBUG: Traitement communauté:", commu);
                
                const div = document.createElement("div");
                div.className = "team-card";
                
                // Déterminer si l'utilisateur est connecté (basé sur l'existence d'éléments de session)
                const isConnected = document.querySelector('.headerAccueil a[href*="profil"]') !== null;
                console.log("DEBUG: Utilisateur connecté:", isConnected);
                
                div.innerHTML = `
                    <img src="Site/assets/images/${commu.imageProfil || 'default.png'}" alt="Image" class="team-img">
                    <div class="team-content">
                        <div class="team-header">
                            <h2>${commu.nom}</h2>
                            <span class="member-count"><img src="Site/assets/images/users-group.png">${commu.membres || 0}</span>
                        </div>
                        <p class="slogan">" ${commu.description || ''} "</p>
                        <div class="tags">
                            <span>${commu.styleGenreUn || ''}</span>
                            <span>${commu.styleGenreDeux || ''}</span>
                            <span>${commu.styleGenreTrois || ''}</span>
                        </div>
                        
                        <div class="buttons">
                            <a href="index.php?page=profilCommunaute&id=${commu.id_communaute}" class="details-btn">Détails</a>
                            ${!isConnected ? 
                                `<a href="#" class="join-btn" onclick="openPopup(); return false;">Rejoins-nous !</a>` :
                                `<a href="index.php?page=rejoindreCommunaute&id=${commu.id_communaute}" class="join-btn">Rejoins-nous !</a>`
                            }
                        </div>
                    </div>
                `;
                resultatsCommunautes.appendChild(div);
            });
            
            console.log("DEBUG: Affichage terminé");
        })
        .catch(error => {
            console.error("DEBUG: Erreur lors de la recherche de communautés:", error);
            resultatsCommunautes.innerHTML = "<p>Erreur lors de la recherche. Veuillez réessayer.</p>";
        });
    });
});

document.addEventListener("DOMContentLoaded", () => {
    const inputCommunaute = document.getElementById("search-input-communaute");
    const searchButton = document.getElementById("btn-search-communaute");

    // Fonction déclenchant la recherche
    const lancerRecherche = () => {
        const valeur = inputCommunaute.value.trim();
        if (valeur.length === 0) {
            window.location.href = 'index.php?page=communaute';
        } else {
            const url = `index.php?page=communaute&search=${encodeURIComponent(valeur)}`;
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                // ... ton code existant de traitement des résultats ...
            })
            .catch(error => {
                console.error("Erreur recherche :", error);
            });
        }
    };

    // Clic sur le bouton
    if (searchButton) {
        searchButton.addEventListener("click", lancerRecherche);
    }

    // Touche Entrée dans l’input
    if (inputCommunaute) {
        inputCommunaute.addEventListener("keypress", e => {
            if (e.key === "Enter") {
                e.preventDefault();
                lancerRecherche();
                }
            });
        }
    });


// Test manuel pour vérifier que les fonctions sont disponibles
console.log("DEBUG: Script communauté chargé");

// Partie Filtre de la page Recherche Amis

function ouvrirFiltre() {
    document.getElementById('overlay-filtre').classList.remove('hidden');
    document.getElementById('modal-filtre').classList.remove('hidden');
}

function fermerFiltre() {
    document.getElementById('overlay-filtre').classList.add('hidden');
    document.getElementById('modal-filtre').classList.add('hidden');
}

document.addEventListener("DOMContentLoaded", () => {
    const input = document.getElementById("search-input");
    const resultats = document.getElementById("resultats-utilisateurs");

    // Vérifier que les éléments existent (on est sur la page de recherche)
    if (!input || !resultats) return;

    input.addEventListener("input", () => {
        const valeur = input.value.trim();

        // Si la valeur est vide, on peut soit ne rien faire, soit recharger la page
        if (valeur.length === 0) {
            // Option 1: Vider les résultats
            // resultats.innerHTML = "";
            
            // Option 2: Recharger la page sans paramètre search
            window.location.href = 'index.php?page=rechercheAmis';
            return;
        }

        // Faire la requête AJAX vers la même page mais avec le header AJAX
        fetch(`index.php?page=rechercheAmis&search=${encodeURIComponent(valeur)}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            // Vérifier si la réponse est JSON
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            } else {
                throw new Error('La réponse n\'est pas en JSON');
            }
        })
        .then(data => {
            resultats.innerHTML = "";

            if (data.length === 0) {
                resultats.innerHTML = "<p>Aucun résultat trouvé.</p>";
                return;
            }

            data.forEach(user => {
                const div = document.createElement("div");
                div.className = "carte-utilisateur";
                div.innerHTML = `
                    <img src="Site/assets/images/${user.imageProfil || 'default.png'}" alt="Profil de ${user.pseudo}" class="carte-image">
                    <h3>${user.pseudo}</h3>
                    <p>${user.genreJeu || ''}</p>
                    <p>${user.support || ''}</p>
                    <div class="carte-boutons">
                        <a href="index.php?page=profilAmi&id=${user.id_utilisateur}" class="btn-detail">
                            <img src="Site/assets/images/user-square.png" alt="">Détails
                        </a>
                        <a href="index.php?page=ajoutAmi&id=${user.id_utilisateur}" class="btn-ajouter">
                            <img src="Site/assets/images/plus-circle.png" alt="">Ajoute
                        </a>
                    </div>
                `;
                resultats.appendChild(div);
            });
        })
        .catch(error => {
            console.error("Erreur lors de la recherche :", error);
            resultats.innerHTML = "<p>Erreur lors de la recherche. Veuillez réessayer.</p>";
        });
    });
});

// Partie Messagerie emoji

function insertEmoji(emoji) {
    const input = document.querySelector('input[name="message"]');
    input.value += emoji;
    input.focus();
}

// Partie Recheche Jeu

document.getElementById('recherche-jeu').addEventListener('input', function () {
    const valeur = this.value.toLowerCase();
    document.querySelectorAll('.carte-jeu').forEach(carte => {
        const nom = carte.getAttribute('data-nom');
        carte.style.display = nom.includes(valeur) ? 'block' : 'none';
    });
});


// Permet scroller automatiquement en bas à l'ouverture

document.addEventListener("DOMContentLoaded", function() {
    const messagesSection = document.querySelector('.commu-messages-section');
    messagesSection.scrollTop = messagesSection.scrollHeight;
});

// Bouton alerte

function closePopup() {
    const popup = document.getElementById('popup-overlay');
    if (popup) popup.remove();
}


function openPopupConnexion() {
    document.getElementById('popup-connexion').style.display = 'flex';
}
function closePopupConnexion() {
    document.getElementById('popup-connexion').style.display = 'none';
}