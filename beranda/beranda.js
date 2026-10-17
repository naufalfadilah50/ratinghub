document.addEventListener('DOMContentLoaded', function() {
    
    // --- 1. FITUR FILTER KATEGORI ---
    const categoryPills = document.querySelectorAll('.pill');
    const movieCards = document.querySelectorAll('.movie-card');

    categoryPills.forEach(pill => {
        pill.addEventListener('click', function() {
            // Ubah tampilan tombol aktif
            categoryPills.forEach(p => p.classList.remove('active'));
            this.classList.add('active');

            const selectedCategory = this.innerText.trim().toLowerCase();

            // Sembunyikan atau munculkan film berdasarkan genre
            movieCards.forEach(card => {
                const movieGenre = card.querySelector('p').innerText.toLowerCase();
                
                if (selectedCategory === 'semua' || movieGenre.includes(selectedCategory)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    // --- 2. FITUR TOMBOL LIHAT DETAIL ---
    const allDetailBtns = document.querySelectorAll('.btn-detail, .detail-btn');

    allDetailBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Mencari judul film terdekat dari tombol yang diklik
            const cardParent = this.closest('.movie-card') || this.closest('.hero-content');
            let title = "";
            
            if (cardParent) {
                const titleElement = cardParent.querySelector('h1, h4');
                title = titleElement ? titleElement.innerText : "Film";
            }

            alert('Membuka detail untuk film: ' + title);
        });
    });
});