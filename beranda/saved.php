<?php
session_start();
include "../koneksi.php";

if (!isset($_SESSION['nama'])) {
    header("Location: ../login/login.html");
    exit();
}
$nama_user = $_SESSION['nama'];

// Ambil data film yang disimpan user dari database
$query_saved = mysqli_query($koneksi, "SELECT id_film FROM simpan_film WHERE nama_user = '$nama_user' ORDER BY tanggal_simpan DESC");
$jumlah_simpan = mysqli_num_rows($query_saved);

// Master data film
$films = [
    1 => ["judul" => "Summer Crashers", "genre" => "Komedi", "rating" => "4.6"],
    2 => ["judul" => "Code Red: Protocol", "genre" => "Aksi, Thriller", "rating" => "4.8"],
    3 => ["judul" => "The Silent Echo", "genre" => "Aksi, Sci-Fi", "rating" => "4.5"],
    4 => ["judul" => "Laughing Out Loud", "genre" => "Komedi", "rating" => "4.2"],
    5 => ["judul" => "The Darkness Within", "genre" => "Horor, Thriller", "rating" => "3.8"],
    6 => ["judul" => "Beyond the Stars", "genre" => "Sci-Fi, Drama", "rating" => "4.8"],
    7 => ["judul" => "Tears of the Sun", "genre" => "Drama, Aksi", "rating" => "4.1"],
    8 => ["judul" => "Cybernetic Dawn", "genre" => "Sci-Fi, Aksi", "rating" => "TBA", "segera" => true],
    9 => ["judul" => "Whispers in the Dark", "genre" => "Horor, Thriller", "rating" => "TBA", "segera" => true]
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RatingHub - Koleksi Tersimpan</title>
    
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* CSS KHUSUS KONTEN TENGAH (Tampilan Koleksi Tersimpan) */
        .saved-header-container { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 25px; }
        .header-left-group { display: flex; gap: 20px; }
        .header-icon-box { background-color: rgba(229, 9, 20, 0.15); color: var(--primary-red); width: 48px; height: 48px; border-radius: 12px; display: flex; justify-content: center; align-items: center; font-size: 20px; margin-top: 5px; }
        .header-title-box h1 { font-size: 32px; font-weight: 800; margin-bottom: 8px; color: var(--text-light); }
        .header-title-box p { color: var(--text-muted); font-size: 14px; line-height: 1.5; }
        .header-right-group { padding-top: 8px; }
        .badge-film-count { background-color: var(--bg-card); border: 1px solid rgba(255, 255, 255, 0.05); padding: 10px 18px; border-radius: 8px; font-size: 13px; font-weight: 600; display: flex; gap: 8px; align-items: center; }
        .badge-film-count .angka { color: var(--primary-red); }
        .badge-film-count .teks { color: var(--text-light); }
        .header-divider-line { width: 100%; height: 1px; background-color: rgba(255, 255, 255, 0.1); margin-bottom: 40px; border: none; }
        .badge-segera { position: absolute; top: 15px; left: 50%; transform: translateX(-50%); background-color: #2563eb; color: #ffffff; padding: 6px 30px; border-radius: 20px; font-size: 11px; font-weight: 700; z-index: 2; letter-spacing: 0.5px; box-shadow: 0 4px 10px rgba(37, 99, 235, 0.4); }

        .btn-trash-saved {
            position: absolute;
            top: 10px;
            left: 10px;
            background: rgba(229, 9, 20, 0.9);
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 14px;
            z-index: 15;
            text-decoration: none;
            opacity: 0;
            transition: 0.3s ease;
        }
        .movie-card:hover .btn-trash-saved { opacity: 1; }
        .btn-trash-saved:hover { background: #ff0000; transform: scale(1.1); }
    </style>
</head>
<body>
    <div class="app-container">
        
        <aside class="sidebar">
            <div class="brand">
                <div class="brand-icon"><i class="fa-solid fa-film"></i></div>
                <span class="brand-name">RatingHub</span>
            </div>

            <nav class="sidebar-menu">
                <a href="beranda.php" class="menu-item">
                    <i class="fa-solid fa-house"></i> Beranda
                </a>
                <a href="../search/search.php" class="menu-item">
                    <i class="fa-solid fa-magnifying-glass"></i> Jelajah
                </a>
                <a href="saved.php" class="menu-item active">
                    <i class="fa-solid fa-bookmark"></i> Disimpan
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
            <div class="saved-header-container">
                <div class="header-left-group">
                    <div class="header-icon-box">
                        <i class="fa-solid fa-bookmark"></i>
                    </div>
                    <div class="header-title-box">
                        <h1>Koleksi Tersimpan</h1>
                        <p>Film-film favorit Anda yang telah disimpan untuk ditonton nanti atau<br>diulas.</p>
                    </div>
                </div>
                <div class="header-right-group">
                    <div class="badge-film-count">
                        <span class="angka"><?php echo $jumlah_simpan; ?></span>
                        <span class="teks">Film ditambahkan</span>
                    </div>
                </div>
            </div>
            
            <hr class="header-divider-line">

            <section class="movie-grid">
                <?php 
                if ($jumlah_simpan > 0) {
                    while($row = mysqli_fetch_assoc($query_saved)) {
                        $id = $row['id_film'];
                        if(isset($films[$id])) {
                            $f = $films[$id];
                            ?>
                            <div class="movie-card">
                                <div class="poster">
                                    <img src="img/<?php echo $id; ?>.jpg" alt="">
                                    
                                    <a href="hapus_simpan.php?id=<?php echo $id; ?>" class="btn-trash-saved" onclick="return confirm('Hapus film ini dari daftar?')">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>

                                    <?php if (isset($f['segera'])): ?>
                                        <div class="badge-segera">SEGERA</div>
                                    <?php else: ?>
                                        <div class="rating"><i class="fa-solid fa-star"></i> <?php echo $f['rating']; ?></div>
                                    <?php endif; ?>

                                    <div class="overlay">
                                        <a href="detail.php?id=<?php echo $id; ?>" class="btn-lihat-detail">Lihat Detail</a>
                                    </div>
                                </div>
                                <h4><?php echo htmlspecialchars($f['judul']); ?></h4>
                                <?php if (isset($f['segera'])): ?>
                                    <p>2026 <span class="dot">•</span> TBA</p>
                                <?php else: ?>
                                    <p>2026 <span class="dot">•</span> <?php echo htmlspecialchars($f['genre']); ?></p>
                                <?php endif; ?>
                            </div>
                            <?php
                        }
                    }
                } else {
                    echo "<div style='grid-column: 1 / -1; text-align: center; padding: 60px 0; color: var(--text-muted);'>";
                    echo "<i class='fa-regular fa-folder-open' style='font-size: 48px; margin-bottom: 20px; opacity: 0.5;'></i>";
                    echo "<p>Koleksi masih kosong. Yuk, cari dan simpan film favoritmu!</p>";
                    echo "</div>";
                }
                ?>
            </section>
        </main>
    </div>
</body>
</html>