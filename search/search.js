document.addEventListener('DOMContentLoaded', function() {
    console.log("Search JS Berhasil Dimuat!"); // Untuk cek di inspect element

    // 1. Ambil SEMUA link menu di sidebar
    const menuLinks = document.querySelectorAll('.sidebar-top .menu-item');

    menuLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Ambil teks di dalam menu (misal: "Beranda")
            const menuText = this.textContent.trim();
            console.log("Menu yang diklik: " + menuText);

            if (menuText.includes("Beranda")) {
                e.preventDefault();
                window.location.href = '../beranda/beranda.php';
            }
        });
    });

    // 2. Tombol Back (Panah Kiri)
    const backBtn = document.querySelector('.back-btn');
    if (backBtn) {
        backBtn.addEventListener('click', function() {
            window.location.href = '../beranda/beranda.php';
        });
    }

    // 3. Tombol Keluar
    const logoutBtn = document.querySelector('.logout');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm("Apakah Anda yakin ingin keluar?")) {
                window.location.href = '../login/logout.php';
            }
        });
    }
});