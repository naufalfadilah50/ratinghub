<?php
session_start();
include "../koneksi.php";

if (!isset($_SESSION['nama'])) {
    header("Location: ../login/login.html");
    exit();
}
$nama_user = $_SESSION['nama'];
$id_film = isset($_GET['id']) ? $_GET['id'] : 1;

// FITUR BARU: AMBIL DATA FOTO USER LOGIN UNTUK HEADER
$q_user_login = mysqli_query($koneksi, "SELECT foto FROM users WHERE nama_lengkap = '$nama_user'");
$data_login = mysqli_fetch_assoc($q_user_login);
$foto_profil_header = !empty($data_login['foto']) ? $data_login['foto'] : 'profile.png';

// Cek apakah film sudah disimpan
$cek_simpan = mysqli_query($koneksi, "SELECT * FROM simpan_film WHERE nama_user = '$nama_user' AND id_film = '$id_film'");
$sudah_disimpan = (mysqli_num_rows($cek_simpan) > 0);

// Data Film Master
$films = [
    1 => ["judul" => "Summer Crashers", "genre" => "Komedi", "durasi" => "1h 50m", "tahun" => "2026", "rating" => "4.6", "sutradara" => "Taika Waititi", "pemeran" => "Jonah Hill, Channing Tatum", "sinopsis" => "Dua sahabat mencoba menyusup ke pesta musim panas paling eksklusif di Hawaii, hanya untuk menyadari bahwa pesta tersebut adalah jebakan para mafia."],
    2 => ["judul" => "Code Red: Protocol", "genre" => "Aksi, Thriller", "durasi" => "2h 15m", "tahun" => "2026", "rating" => "4.8", "sutradara" => "Christopher Nolan", "pemeran" => "Tom Hardy, Charlize Theron", "sinopsis" => "Seorang mantan agen rahasia dipanggil kembali untuk menghentikan serangan siber global."],
    3 => ["judul" => "The Silent Echo", "genre" => "Aksi, Sci-Fi", "durasi" => "2h 05m", "tahun" => "2026", "rating" => "4.5", "sutradara" => "Denis Villeneuve", "pemeran" => "Ryan Gosling, Emily Blunt", "sinopsis" => "Di masa depan di mana suara diawasi oleh pemerintah..."],
    4 => ["judul" => "Laughing Out Loud", "genre" => "Komedi", "durasi" => "1h 45m", "tahun" => "2026", "rating" => "4.2", "sutradara" => "Todd Phillips", "pemeran" => "Kevin Hart, Jack Black", "sinopsis" => "Kompetisi stand-up comedy lokal berubah menjadi ajang kekacauan."],
    5 => ["judul" => "The Darkness Within", "genre" => "Horor, Thriller", "durasi" => "1h 55m", "tahun" => "2026", "rating" => "3.8", "sutradara" => "James Wan", "pemeran" => "Vera Farmiga, Patrick Wilson", "sinopsis" => "Keluarga pindah ke rumah tua warisan leluhur."],
    6 => ["judul" => "Beyond the Stars", "genre" => "Sci-Fi, Drama", "durasi" => "2h 20m", "tahun" => "2026", "rating" => "4.8", "sutradara" => "Alfonso Cuarón", "pemeran" => "Matthew McConaughey, Anne Hathaway", "sinopsis" => "Misi pencarian planet baru bagi umat manusia."],
    7 => ["judul" => "Tears of the Sun", "genre" => "Drama, Aksi", "durasi" => "2h 10m", "tahun" => "2026", "rating" => "4.1", "sutradara" => "Antoine Fuqua", "pemeran" => "Denzel Washington, Viola Davis", "sinopsis" => "Kisah perjuangan heroik regu penyelamat."],
    8 => ["judul" => "Cybernetic Dawn", "genre" => "Sci-Fi, Aksi", "durasi" => "TBA", "tahun" => "2026", "rating" => "TBA", "sutradara" => "Ridley Scott", "pemeran" => "Michael Fassbender", "sinopsis" => "AI mencapai kesadaran penuh."],
    9 => ["judul" => "Whispers in the Dark", "genre" => "Horor, Thriller", "durasi" => "TBA", "tahun" => "2026", "rating" => "TBA", "sutradara" => "Jordan Peele", "pemeran" => "Daniel Kaluuya", "sinopsis" => "Desa terpencil dihantui suara bisikan."]
];

$data = $films[$id_film];

// LOGIKA FILTER RATING
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'terbaru';
$order_by = "ulasan.id DESC"; 
if ($filter == 'tertinggi') { $order_by = "ulasan.rating DESC"; }
elseif ($filter == 'terendah') { $order_by = "ulasan.rating ASC"; }

// FITUR BARU: QUERY JOIN UNTUK MENGAMBIL FOTO PROFILE DARI TABEL USERS
$query_ulasan = mysqli_query($koneksi, "SELECT ulasan.*, users.foto FROM ulasan 
                LEFT JOIN users ON ulasan.nama_user = users.nama_lengkap 
                WHERE ulasan.id_film = '$id_film' ORDER BY $order_by");
$jumlah_ulasan = mysqli_num_rows($query_ulasan);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail - <?php echo $data['judul']; ?></title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="detail.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* CSS Tambahan untuk Form Ulasan dan Bintang */
        .star-rating { display: flex; flex-direction: row-reverse; justify-content: flex-end; gap: 8px; }
        .star-rating input { display: none; }
        .star-rating label { font-size: 28px; color: #2c313c; cursor: pointer; transition: 0.2s; }
        .star-rating label:hover, .star-rating label:hover ~ label, .star-rating input:checked ~ label { color: #facc15; }
        
        .review-box-container { background: #151822; border-radius: 16px; padding: 25px; border: 1px solid rgba(255,255,255,0.05); margin-top: 15px; }
        .comment-input-box { width: 100%; background: #0d1017; border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 15px; color: white; min-height: 100px; margin-top: 15px; resize: none; }
        
        .btn-submit-review { float: right; background: #2c313c; color: #8b92a5; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; margin-top: 10px; }
        .btn-submit-review:hover { background: #e50914; color: white; }

        .community-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; margin-top: 40px; }
        .badge-count { background: #1c212d; padding: 2px 10px; border-radius: 10px; font-size: 12px; color: #8b92a5; margin-left: 8px; }
        .filter-select { background: #151822; color: #8b92a5; border: 1px solid rgba(255,255,255,0.1); padding: 8px 15px; border-radius: 8px; outline: none; cursor: pointer; }
        
        /* PERBAIKAN: Foto Profile Jernih & Bulat */
        .avatar-review-wrapper { width: 45px; height: 45px; border-radius: 50%; overflow: hidden; border: 2px solid var(--primary-red); flex-shrink: 0; }
        .avatar-review-wrapper img { width: 100%; height: 100%; object-fit: cover; }
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
                <a href="beranda.php" class="menu-item"><i class="fa-solid fa-house"></i> Beranda</a>
                <a href="../search/search.php" class="menu-item"><i class="fa-solid fa-magnifying-glass"></i> Jelajah</a>
                <a href="saved.php" class="menu-item"><i class="fa-regular fa-bookmark"></i> Disimpan</a>
                <a href="../profile/profile.php" class="menu-item"><i class="fa-regular fa-user"></i> Profile</a>
            </nav>
            <div class="sidebar-footer">
                <a href="../login/logout.php" class="menu-item logout"><i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar</a>
            </div>
        </aside>

        <main class="main-content detail-page">
            <div class="detail-top-nav" style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                <a href="beranda.php" class="back-btn" style="margin-bottom: 0;"><i class="fa-solid fa-chevron-left"></i> Kembali</a>
                
                <?php if ($sudah_disimpan): ?>
                    <a href="hapus_simpan.php?id=<?php echo $id_film; ?>&from=detail" class="back-btn" style="background: var(--primary-red); color: white; border-color: var(--primary-red); margin-bottom: 0;">
                        <i class="fa-solid fa-bookmark"></i> Tersimpan
                    </a>
                <?php else: ?>
                    <a href="proses_simpan.php?id=<?php echo $id_film; ?>" class="back-btn" style="margin-bottom: 0;">
                        <i class="fa-regular fa-bookmark"></i> Simpan Film
                    </a>
                <?php endif; ?>
            </div>

            <section class="detail-header">
                <img src="img/<?php echo $id_film; ?>.jpg" class="poster-detail" alt="<?php echo $data['judul']; ?>">
                <div class="info-utama">
                    <span class="badge-genre"><?php echo $data['genre']; ?></span>
                    <h1><?php echo $data['judul']; ?></h1>
                    <div class="meta-stats" style="color: var(--text-muted); display: flex; gap: 20px;">
                        <?php if ($data['rating'] !== "TBA"): ?>
                            <span><i class="fa-solid fa-star" style="color: var(--star-color);"></i> <?php echo $data['rating']; ?> / 5.0</span>
                        <?php endif; ?>
                        <span><i class="fa-regular fa-calendar"></i> <?php echo $data['tahun']; ?></span>
                        <span><i class="fa-regular fa-clock"></i> <?php echo $data['durasi']; ?></span>
                    </div>
                </div>
            </section>

            <section class="detail-grid">
                <div class="left-col">
                    <h3>Sinopsis Cerita</h3>
                    <p><?php echo $data['sinopsis']; ?></p>

                    <h3>Pemeran & Kru</h3>
                    <div class="kru-box">
                        <p><strong>SUTRADARA</strong> <?php echo $data['sutradara']; ?></p>
                        <p><strong>PEMERAN UTAMA</strong> <?php echo $data['pemeran']; ?></p>
                    </div>

                    <div class="section-block">
                        <h3>Berikan Ulasanmu</h3>
                        <div class="review-box-container">
                            <form action="proses_ulasan.php" method="POST">
                                <input type="hidden" name="id_film" value="<?php echo $id_film; ?>">
                                <div style="display: flex; align-items: center; gap: 20px;">
                                    <div class="star-rating">
                                        <input type="radio" id="star5" name="rating" value="5" required />
                                        <label for="star5"><i class="fa-solid fa-star"></i></label>
                                        <input type="radio" id="star4" name="rating" value="4" />
                                        <label for="star4"><i class="fa-solid fa-star"></i></label>
                                        <input type="radio" id="star3" name="rating" value="3" />
                                        <label for="star3"><i class="fa-solid fa-star"></i></label>
                                        <input type="radio" id="star2" name="rating" value="2" />
                                        <label for="star2"><i class="fa-solid fa-star"></i></label>
                                        <input type="radio" id="star1" name="rating" value="1" />
                                        <label for="star1"><i class="fa-solid fa-star"></i></label>
                                    </div>
                                    <span style="color: #8b92a5; font-size: 13px;">Ketuk bintang untuk menilai</span>
                                </div>
                                <textarea name="komentar" class="comment-input-box" placeholder="Ceritakan pendapatmu tentang film ini..." required></textarea>
                                <button type="submit" class="btn-submit-review">Kirim Ulasan <i class="fa-regular fa-paper-plane"></i></button>
                                <div style="clear: both;"></div>
                            </form>
                        </div>
                    </div>

                    <div class="community-header">
                        <div style="display: flex; align-items: center;">
                            <h3 style="margin:0;">Ulasan Komunitas</h3>
                            <span class="badge-count"><?php echo $jumlah_ulasan; ?></span>
                        </div>
                        <form method="GET">
                            <input type="hidden" name="id" value="<?php echo $id_film; ?>">
                            <select name="filter" class="filter-select" onchange="this.form.submit()">
                                <option value="terbaru" <?php echo ($filter == 'terbaru') ? 'selected' : ''; ?>>Terbaru</option>
                                <option value="tertinggi" <?php echo ($filter == 'tertinggi') ? 'selected' : ''; ?>>Rating Tertinggi</option>
                                <option value="terendah" <?php echo ($filter == 'terendah') ? 'selected' : ''; ?>>Rating Terendah</option>
                            </select>
                        </form>
                    </div>

                    <div class="review-list">
                        <?php while($row = mysqli_fetch_assoc($query_ulasan)) : ?>
                            <div class="review-item" style="background: var(--bg-card); padding: 20px; border-radius: 12px; margin-bottom: 15px; border: 1px solid rgba(255,255,255,0.05);">
                                <div style="display:flex; align-items:center; gap:15px; margin-bottom:10px;">
                                    <div class="avatar-review-wrapper">
                                        <?php $foto_ulasan = !empty($row['foto']) ? $row['foto'] : 'profile.png'; ?>
                                        <img src="../beranda/img/<?php echo $foto_ulasan; ?>?v=<?php echo time(); ?>">
                                    </div>
                                    <div>
                                        <strong style="display:block; font-size:14px;"><?php echo htmlspecialchars($row['nama_user']); ?></strong>
                                        <span style="font-size:12px; color:var(--text-muted);">Baru saja</span>
                                    </div>
                                    <div style="margin-left:auto; color:#facc15; font-size:12px;">
                                        <?php for($i=1;$i<=5;$i++) echo ($i<=$row['rating']) ? "★" : "☆"; ?>
                                    </div>
                                </div>
                                <p style="font-size:15px; line-height:1.6; color:#c4c6cf;"><?php echo htmlspecialchars($row['komentar']); ?></p>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>

                <div class="right-col">
                    <div class="info-card">
                        <h3>Informasi Film</h3>
                        <p><span>Genre</span> <strong><?php echo $data['genre']; ?></strong></p>
                        <p><span>Tahun</span> <strong><?php echo $data['tahun']; ?></strong></p>
                        <p><span>Durasi</span> <strong><?php echo $data['durasi']; ?></strong></p>
                        <p><span>Sutradara</span> <strong><?php echo $data['sutradara']; ?></strong></p>
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>
</html>