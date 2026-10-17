<?php
session_start();
include "../koneksi.php"; // Pastikan koneksi disertakan

// Cek Login
if (!isset($_SESSION['nama'])) {
    header("Location: ../login/login.html");
    exit();
}
$nama_user = $_SESSION['nama']; 

// FITUR BARU: AMBIL DATA FOTO USER LOGIN DARI DATABASE
$query_user = mysqli_query($koneksi, "SELECT foto FROM users WHERE nama_lengkap = '$nama_user'");
$user_data = mysqli_fetch_assoc($query_user);
$foto_profil = !empty($user_data['foto']) ? $user_data['foto'] : 'profile.png';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RatingHub - Beranda</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <div class="app-container">
        <aside class="sidebar">
            <div class="brand">
                <div class="brand-icon"><i class="fa-solid fa-film"></i></div>
                <span class="brand-name">RatingHub</span>
            </div>

            <nav class="sidebar-menu">
                <a href="beranda.php" class="menu-item active">
                    <i class="fa-solid fa-house"></i> Beranda
                </a>
                <a href="../search/search.php" class="menu-item">
                    <i class="fa-solid fa-magnifying-glass"></i> Jelajah
                </a>
                <a href="saved.php" class="menu-item">
                    <i class="fa-regular fa-bookmark"></i> Disimpan
                </a>
                <a href="../profile/profile.php" class="menu-item">
                    <i class="fa-regular fa-user"></i> Profile
                </a>
            </nav>

            <div class="sidebar-footer">
                <a href="../login/logout.php" class="menu-item logout">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar
                </a>
            </div>
        </aside>

        <main class="main-content">
            <header class="top-header">
                <div class="greeting">
                    <p>Selamat datang kembali,</p>
                    <h2><?php echo htmlspecialchars($nama_user); ?></h2>
                </div>
                
                <div class="profile-pic" onclick="window.location.href='../profile/profile.php'" style="cursor: pointer;" title="Buka Profile">
                    <img src="img/<?php echo $foto_profil; ?>?v=<?php echo time(); ?>" alt="Profile">
                </div>
            </header>

            <section class="hero-banner">
                <div class="hero-content">
                    <span class="badge-red">Komedi</span>
                    <h1>Summer Crashers</h1>
                    <p>Dua sahabat mencoba menyusup ke pesta musim panas paling eksklusif di Hawaii, hanya untuk menyadari bahwa pesta tersebut adalah jebakan para mafia.</p>
                    <div class="hero-meta">
                        <span class="rating-star"><i class="fa-solid fa-star"></i> 4.6</span>
                        <span>2026</span>
                        <span><span class="dot">•</span> 1h 50m</span>
                    </div>
                    <button class="btn-detail" onclick="window.location.href='detail.php?id=1'">
                        <i class="fa-solid fa-circle-info"></i> Lihat Detail
                    </button>
                </div>
            </section>

            <section class="category-section">
                <h3>Kategori Film</h3>
                <div class="category-pills">
                    <button class="pill active">Semua</button>
                    <button class="pill">Aksi</button>
                    <button class="pill">Komedi</button>
                    <button class="pill">Horor</button>
                    <button class="pill">Sci-Fi</button>
                    <button class="pill">Drama</button>
                    <button class="pill">Thriller</button>
                </div>
            </section>

            <section class="movie-section">
                <div class="section-header">
                    <h3>Sedang Tayang</h3>
                    <a href="../search/search.php" class="see-all">Lihat Semua ></a>
                </div>
                <div class="movie-grid">
                    <?php 
                    $sedang_tayang = [
                        1 => ['Summer Crashers', 'Komedi', '4.6'],
                        2 => ['Code Red: Protocol', 'Aksi, Thriller', '4.8'],
                        3 => ['The Silent Echo', 'Aksi, Sci-Fi', '4.5'],
                        4 => ['Laughing Out Loud', 'Komedi', '4.2'],
                        5 => ['The Darkness Within', 'Horor, Thriller', '3.8'],
                        6 => ['Beyond the Stars', 'Sci-Fi, Drama', '4.8'],
                        7 => ['Tears of the Sun', 'Drama, Aksi', '4.1']
                    ];
                    foreach ($sedang_tayang as $id => $f): ?>
                    <div class="movie-card">
                        <div class="poster">
                            <img src="img/<?php echo $id; ?>.jpg" alt="<?php echo $f[0]; ?>">
                            <div class="rating"><i class="fa-solid fa-star"></i> <?php echo $f[2]; ?></div>
                            <div class="overlay">
                                <a href="detail.php?id=<?php echo $id; ?>" class="btn-lihat-detail">Lihat Detail</a>
                            </div>
                        </div>
                        <h4><?php echo $f[0]; ?></h4>
                        <p class="movie-genre"><?php echo $f[1]; ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="movie-section" style="margin-bottom: 80px;">
                <div class="section-header">
                    <h3>Akan Datang <span class="badge-blue">BARU 2026</span></h3>
                </div>
                <div class="movie-grid">
                    <div class="movie-card">
                        <div class="poster">
                            <img src="img/8.jpg" alt="Cybernetic Dawn">
                            <div class="badge-year">2026</div>
                            <div class="overlay">
                                <a href="detail.php?id=8" class="btn-lihat-detail">Lihat Detail</a>
                            </div>
                        </div>
                        <h4>Cybernetic Dawn</h4>
                        <p class="movie-genre">Sci-Fi, Aksi</p>
                    </div>
                    <div class="movie-card">
                        <div class="poster">
                            <img src="img/9.jpg" alt="Whispers in the Dark">
                            <div class="badge-year">2026</div>
                            <div class="overlay">
                                <a href="detail.php?id=9" class="btn-lihat-detail">Lihat Detail</a>
                            </div>
                        </div>
                        <h4>Whispers in the Dark</h4>
                        <p class="movie-genre">Horor, Thriller</p>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const buttons = document.querySelectorAll('.pill');
            const movieCards = document.querySelectorAll('.movie-card');

            buttons.forEach(button => {
                button.addEventListener('click', () => {
                    buttons.forEach(btn => btn.classList.remove('active'));
                    button.classList.add('active');

                    const filter = button.textContent.trim().toLowerCase();

                    movieCards.forEach(card => {
                        const genreText = card.querySelector('.movie-genre').textContent.toLowerCase();

                        if (filter === 'semua' || genreText.includes(filter)) {
                            card.style.display = ''; 
                        } else {
                            card.style.display = 'none'; 
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>