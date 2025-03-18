let index = 0;
const slides = document.querySelectorAll('section .slide');
console.log(slides);

function nextSlide() {
    index = (index + 1) % slides.length;
    slides.forEach(slide => {
        slide.style.transform = `translateX(-${index * 100}%)`;
    });
}

// Changer d'image toutes les 4 secondes
setInterval(nextSlide, 4000);

document.addEventListener("DOMContentLoaded", () => {
    const images = document.querySelectorAll('.imgCarrousel'); // Sélectionne toutes les images du carrousel
    const lightbox = document.getElementById('lightbox');
    const lightboxImg = document.getElementById('lightbox-img');
    const closeBtn = document.querySelector('.close');

    images.forEach(img => {
        img.addEventListener('click', () => {
            lightbox.style.display = 'flex'; // Afficher la lightbox
            lightboxImg.src = img.src; // Mettre l'image cliquée en grand
        });
    });

    // Fermer la lightbox en cliquant sur la croix
    closeBtn.addEventListener('click', () => {
        lightbox.style.display = 'none';
    });

    // Fermer en cliquant en dehors de l'image
    lightbox.addEventListener('click', (e) => {
        if (e.target === lightbox) {
            lightbox.style.display = 'none';
        }
    });
});

