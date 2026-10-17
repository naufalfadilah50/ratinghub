<?php
session_start();
// Cek Login
if (!isset($_SESSION['nama'])) {
    header("Location: ../login/login.html");
    exit();
}
$nama_user = $_SESSION['nama'];

// DATABASE MINI FILM
$films = [
    1 => ["judul" => "Summer Crashers", "genre" => "Komedi", "rating" => "4.6", "sutradara" => "Taika Waititi", "pemeran" => "Jonah Hill, Channing Tatum"],
    2 => ["judul" => "Code Red: Protocol", "genre" => "Aksi, Thriller", "rating" => "4.8", "sutradara" => "Christopher Nolan", "pemeran" => "Tom Hardy, Charlize Theron"],
    3 => ["judul" => "The Silent Echo", "genre" => "Aksi, Sci-Fi", "rating" => "4.5", "sutradara" => "Denis Villeneuve", "pemeran" => "Ryan Gosling, Emily Blunt"],
    4 => ["judul" => "Laughing Out Loud", "genre" => "Komedi", "rating" => "4.2", "sutradara" => "Todd Phillips", "pemeran" => "Kevin Hart, Jack Black"],
    5 => ["judul" => "The Darkness Within", "genre" => "Horor, Thriller", "rating" => "3.8", "sutradara" => "James Wan", "pemeran" => "Vera Farmiga, Patrick Wilson"],
    6 => ["judul" => "Beyond the Stars", "genre" => "Sci-Fi, Drama", "rating" => "4.8", "sutradara" => "Alfonso Cuarón", "pemeran" => "Matthew McConaughey, Anne Hathaway"],
    7 => ["judul" => "Tears of the Sun", "genre" => "Drama, Aksi", "rating" => "4.1", "sutradara" => "Antoine Fuqua", "pemeran" => "Denzel Washington, Viola Davis"],
    8 => ["judul" => "Cybernetic Dawn", "genre" => "Sci-Fi, Aksi", "rating" => "TBA", "sutradara" => "Ridley Scott", "pemeran" => "Michael Fassbender", "segera" => true],
    9 => ["judul" => "Whispers in the Dark", "genre" => "Horor, Thriller", "rating" => "TBA", "sutradara" => "Jordan Peele", "pemeran" => "Daniel Kaluuya", "segera" => true]
];

// Menerima input pencarian & filter
$q = isset($_GET['q']) ? strtolower($_GET['q']) : '';
$genre_filter = isset($_GET['genre']) ? $_GET['genre'] : 'Semua';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'Populer';

// Logika Filter
$filtered_films = [];
foreach ($films as $id => $f) {
    $match = true;

    if ($q != '') {
        $gabungan_teks = strtolower($f['judul'] . ' ' . $f['pemeran'] . ' ' . $f['sutradara']);
        if (strpos($gabungan_teks, $q) === false) {
            $match = false;
        }
    }

    if ($genre_filter != 'Semua') {
        if (strpos($f['genre'], $genre_filter) === false) {
            $match = false;
        }
    }

    if ($sort == 'Segera') {
        if (!isset($f['segera'])) $match = false; 
    }

    if ($match) {
        $filtered_films[$id] = $f;
    }
}

// Logika Sorting
if ($sort == 'Terbaru') {
    krsort($filtered_films); 
} elseif ($sort == 'Populer') {
    uasort($filtered_films, function($a, $b) {
        return $b['rating'] <=> $a['rating'];
    });
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RatingHub - Jelajah Film</title>
    
    <link rel="stylesheet" href="../beranda/style.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .page-header { display: flex; align-items: center; gap: 20px; margin-bottom: 30px; }
        .back-btn-search { background: transparent; color: var(--text-light); border: 1px solid rgba(255,255,255,0.2); width: 45px; height: 45px; border-radius: 50%; display: flex; justify-content: center; align-items: center; cursor: pointer; transition: 0.3s; font-size: 18px; }
        .back-btn-search:hover { background: rgba(255,255,255,0.1); }
        .header-text h1 { font-size: 28px; margin-bottom: 5px; }
        .header-text p { color: var(--text-muted); font-size: 14px; }
        
        .search-filter-bar { display: flex; align-items: center; gap: 15px; margin-bottom: 30px; }
        .search-box { flex: 1; max-width: 400px; background: var(--bg-card); display: flex; align-items: center; padding: 12px 20px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05); }
        .search-box i { color: var(--text-muted); margin-right: 15px; font-size: 18px; }
        .search-box input { background: transparent; border: none; color: white; width: 100%; outline: none; font-size: 14px; }
        
        .filter-pills { display: flex; gap: 10px; flex-wrap: wrap; }
        .filter-pills .pill { text-decoration: none; display: inline-block; }
        
        .results-info { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .result-count { color: var(--text-muted); font-size: 14px; }
        .result-count strong { color: white; }
        
        .sort-options { display: flex; gap: 20px; font-size: 14px; }
        .sort-options a { color: var(--text-muted); text-decoration: none; padding-bottom: 8px; border-bottom: 2px solid transparent; transition: 0.3s; }
        .sort-options a.active { color: white; border-bottom: 2px solid var(--primary-red); font-weight: 600; }
        .sort-options a:hover { color: white; }

        /* Badge Segera (Biru) */
        .badge-segera {
            position: absolute;
            top: 15px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #2563eb;
            color: #ffffff;
            padding: 6px 30px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            z-index: 2;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.4);
        }
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
                <a href="../beranda/beranda.php" class="menu-item">
                    <i class="fa-solid fa-house"></i> Beranda
                </a>
                <a href="search.php" class="menu-item active">
                    <i class="fa-solid fa-magnifying-glass"></i> Jelajah
                </a>
                <a href="../beranda/saved.php" class="menu-item">
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
            
            <header class="page-header">
                <button class="back-btn-search" onclick="window.location.href='../beranda/beranda.php'">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                <div class="header-text">
                    <h1>Semua Film</h1>
                    <p>Temukan film dari berbagai genre dan tahun rilis</p>
                </div>
            </header>

            <form action="search.php" method="GET">
                <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort); ?>">
                <input type="hidden" name="genre" value="<?php echo htmlspecialchars($genre_filter); ?>">

                <div class="search-filter-bar">
                    <div class="search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" name="q" value="<?php echo htmlspecialchars($q); ?>" placeholder="Cari judul film, pemeran, atau sutradara" autocomplete="off">
                        <button type="submit" style="display:none;"></button>
                    </div>
                    
                    <div class="filter-pills">
                        <?php 
                        $kategori = ['Semua', 'Aksi', 'Komedi', 'Horor', 'Sci-Fi', 'Drama', 'Thriller'];
                        foreach ($kategori as $kat) {
                            $active_class = ($kat == $genre_filter) ? 'active' : '';
                            echo "<a href='search.php?genre={$kat}&sort={$sort}&q={$q}' class='pill {$active_class}'>{$kat}</a>";
                        }
                        ?>
                    </div>
                </div>
            </form>

            <section class="results-info">
                <p class="result-count">Menampilkan <strong><?php echo count($filtered_films); ?></strong> film</p>
                <div class="sort-options">
                    <a href="search.php?sort=Terbaru&genre=<?php echo $genre_filter; ?>&q=<?php echo $q; ?>" class="<?php echo ($sort == 'Terbaru') ? 'active' : ''; ?>">Terbaru</a>
                    <a href="search.php?sort=Populer&genre=<?php echo $genre_filter; ?>&q=<?php echo $q; ?>" class="<?php echo ($sort == 'Populer') ? 'active' : ''; ?>">Populer</a>
                    <a href="search.php?sort=Segera&genre=<?php echo $genre_filter; ?>&q=<?php echo $q; ?>" class="<?php echo ($sort == 'Segera') ? 'active' : ''; ?>">Segera</a>
                </div>
            </section>

            <section class="movie-grid">
                <?php if (count($filtered_films) > 0): ?>
                    <?php foreach ($filtered_films as $id => $f): ?>
                    <div class="movie-card">
                        <div class="poster">
                            <img src="../beranda/img/<?php echo $id; ?>.jpg" alt="<?php echo htmlspecialchars($f['judul']); ?>">
                            
                            <?php if (isset($f['segera'])): ?>
                                <div class="badge-segera">SEGERA</div>
                            <?php else: ?>
                                <div class="rating"><i class="fa-solid fa-star"></i> <?php echo $f['rating']; ?></div>
                            <?php endif; ?>

                            <div class="overlay">
                                <a href="../beranda/detail.php?id=<?php echo $id; ?>" class="btn-lihat-detail">Lihat Detail</a>
                            </div>
                        </div>
                        <h4><?php echo htmlspecialchars($f['judul']); ?></h4>
                        
                        <?php if (isset($f['segera'])): ?>
                            <p>2026 <span class="dot">•</span> TBA</p>
                        <?php else: ?>
                            <p>2026 <span class="dot">•</span> <?php echo htmlspecialchars($f['genre']); ?></p>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="grid-column: 1 / -1; text-align: center; padding: 50px 0; color: var(--text-muted);">
                        <i class="fa-solid fa-film" style="font-size: 40px; margin-bottom: 15px; opacity: 0.5;"></i>
                        <p>Ups, tidak ada film yang cocok dengan pencarianmu.</p>
                    </div>
                <?php endif; ?>
            </section>

        </main>
    </div>

</body>
</html>