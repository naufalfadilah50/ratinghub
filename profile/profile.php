<?php
session_start();
include "../koneksi.php";

if (!isset($_SESSION['nama'])) {
    header("Location: ../login/login.html");
    exit();
}
$nama_user = $_SESSION['nama'];

// AMBIL DATA USER LENGKAP DARI DATABASE
$query_user = mysqli_query($koneksi, "SELECT * FROM users WHERE nama_lengkap = '$nama_user'");
$user_data = mysqli_fetch_assoc($query_user);

// Variabel Foto & Anti-Cache (Logika mencontek sidebar agar sinkron)
$foto_tampil = !empty($user_data['foto']) ? $user_data['foto'] : 'profile.png';
$anti_cache = time(); 

// Ambil Data Statistik Nyata dari Database
$q_dinilai = mysqli_query($koneksi, "SELECT * FROM ulasan WHERE nama_user = '$nama_user'");
$jumlah_dinilai = mysqli_num_rows($q_dinilai);

$q_disimpan = mysqli_query($koneksi, "SELECT * FROM simpan_film WHERE nama_user = '$nama_user'");
$jumlah_disimpan = mysqli_num_rows($q_disimpan);

$jumlah_diulas = $jumlah_dinilai; 

// Master Data Film (Untuk Modal)
$films = [
    1 => ["judul" => "Summer Crashers", "genre" => "Komedi"],
    2 => ["judul" => "Code Red: Protocol", "genre" => "Aksi, Thriller"],
    3 => ["judul" => "The Silent Echo", "genre" => "Aksi, Sci-Fi"],
    4 => ["judul" => "Laughing Out Loud", "genre" => "Komedi"],
    5 => ["judul" => "The Darkness Within", "genre" => "Horor, Thriller"],
    6 => ["judul" => "Beyond the Stars", "genre" => "Sci-Fi, Drama"],
    7 => ["judul" => "Tears of the Sun", "genre" => "Drama, Aksi"],
    8 => ["judul" => "Cybernetic Dawn", "genre" => "Sci-Fi, Aksi"],
    9 => ["judul" => "Whispers in the Dark", "genre" => "Horor, Thriller"]
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>RatingHub - Profile</title>
    <link rel="stylesheet" href="../beranda/style.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* CSS ASLI */
        .profile-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .profile-grid { display: grid; grid-template-columns: 350px 1fr; gap: 30px; }
        .card-identity { background: var(--bg-card); border-radius: 20px; padding: 40px 30px; text-align: center; border: 1px solid rgba(255,255,255,0.05); }
        .avatar-main { width: 140px; height: 140px; border-radius: 50%; border: 3px solid var(--primary-red); object-fit: cover; margin-bottom: 20px; cursor: pointer; transition: 0.3s; }
        .avatar-main:hover { filter: brightness(0.8); }
        .stats-row { display: flex; justify-content: space-between; gap: 10px; margin: 25px 0; }
        .stat-item { flex: 1; background: rgba(0,0,0,0.3); padding: 15px 5px; border-radius: 12px; cursor: pointer; transition: 0.3s; border: 1px solid transparent; }
        .stat-item:hover { border-color: var(--primary-red); background: rgba(229,9,20,0.05); }
        .stat-item strong { display: block; font-size: 20px; color: #fff; }
        .stat-item span { font-size: 10px; color: var(--text-muted); text-transform: uppercase; }
        .btn-logout-alt { width: 100%; background: rgba(229, 9, 20, 0.1); color: var(--primary-red); padding: 15px; border-radius: 12px; font-weight: 700; text-decoration: none; display: flex; justify-content: center; gap: 10px; transition: 0.3s; }
        .btn-logout-alt:hover { background: var(--primary-red); color: #fff; }
        .section-title { font-size: 16px; font-weight: 700; margin-bottom: 20px; display: block; }
        .action-card { background: var(--bg-card); padding: 20px; border-radius: 15px; display: flex; align-items: center; justify-content: space-between; text-decoration: none; color: #fff; margin-bottom: 15px; border: 1px solid rgba(255,255,255,0.05); transition: 0.3s; }
        .action-card:hover { transform: translateY(-3px); border-color: var(--primary-red); }
        .icon-box { width: 45px; height: 45px; border-radius: 10px; background: rgba(229,9,20,0.1); color: var(--primary-red); display: flex; justify-content: center; align-items: center; font-size: 18px; margin-right: 15px; }

        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); backdrop-filter: blur(5px); }
        .modal-content { background: #1a1d29; margin: 5% auto; padding: 30px; border-radius: 20px; width: 600px; max-height: 80vh; overflow-y: auto; position: relative; border: 1px solid rgba(255,255,255,0.1); }
        .close-modal { position: absolute; right: 25px; top: 20px; color: #8b92a5; font-size: 24px; cursor: pointer; }
        .modal-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; }
        .modal-movie-card { text-align: center; position: relative; }
        .modal-movie-card img { width: 100%; border-radius: 10px; aspect-ratio: 2/3; object-fit: cover; }
        .modal-movie-card .mini-rating { position: absolute; top: 5px; right: 5px; background: rgba(0,0,0,0.8); padding: 2px 6px; border-radius: 4px; font-size: 10px; color: #facc15; }
        .modal-movie-card p { font-size: 11px; margin-top: 5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #fff; }
        .review-list-item { background: rgba(255,255,255,0.03); padding: 15px; border-radius: 12px; margin-bottom: 15px; display: flex; gap: 15px; }
        .review-list-item img { width: 60px; height: 80px; border-radius: 8px; object-fit: cover; }
        .review-info h4 { font-size: 14px; margin-bottom: 5px; }
        .review-info .stars { color: #facc15; font-size: 12px; margin-bottom: 8px; }
        .review-info .text { font-size: 12px; color: var(--text-muted); font-style: italic; line-height: 1.4; }

        /* CSS TAMBAHAN UNTUK PENGATURAN & KEAMANAN & LANGGANAN */
        .setting-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .switch { position: relative; display: inline-block; width: 34px; height: 20px; }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #333; transition: .4s; border-radius: 34px; }
        .slider:before { position: absolute; content: ""; height: 14px; width: 14px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; }
        input:checked + .slider { background-color: #e50914; }
        input:checked + .slider:before { transform: translateX(14px); }

        .form-group { margin-bottom: 20px; text-align: left; }
        .form-group label { display: flex; align-items: center; gap: 10px; color: var(--text-muted); font-size: 14px; margin-bottom: 8px; }
        .form-group label i { color: #fff; font-size: 16px; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; background: #0d1017; border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; padding: 12px; color: #fff; outline: none; }
        .btn-save-profile { width: 100%; background: var(--primary-red); color: #fff; border: none; padding: 15px; border-radius: 12px; font-weight: 700; cursor: pointer; display: flex; justify-content: center; align-items: center; gap: 10px; transition: 0.3s; }
        .btn-save-profile:hover { background: #ff0a16; }
        
        .poster-container { position: relative; width: 100%; aspect-ratio: 2/3; border-radius: 10px; overflow: hidden; }
        .overlay-detail { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); display: flex; justify-content: center; align-items: center; opacity: 0; transition: 0.3s; }
        .modal-movie-card:hover .overlay-detail { opacity: 1; }
        .btn-mini-detail { background: var(--primary-red); color: white; padding: 6px 10px; border-radius: 20px; font-size: 10px; font-weight: 700; text-decoration: none; }

        /* STYLE KHUSUS MODAL LANGGANAN */
        .subscription-card { background: linear-gradient(135deg, #1a1d29 0%, #2c0508 100%); border: 1px solid rgba(229, 9, 20, 0.3); border-radius: 20px; padding: 30px; margin-top: 20px; position: relative; overflow: hidden; box-shadow: 0 10px 30px rgba(229, 9, 20, 0.1); }
        .benefit-list { list-style: none; padding: 0; margin: 20px 0; }
        .benefit-item { display: flex; align-items: center; gap: 10px; font-size: 13px; margin-bottom: 10px; color: #c4c6cf; }
        .benefit-item i.fa-check { color: #2ecc71; }
        .benefit-item i.fa-xmark { color: #e50914; opacity: 0.5; }
        .btn-pro { width: 100%; background: linear-gradient(to right, #e50914, #ff0a16); color: white; border: none; padding: 14px; border-radius: 12px; font-weight: 800; cursor: pointer; display: flex; justify-content: center; align-items: center; gap: 10px; transition: 0.3s; }
        .billing-history-box { background: rgba(255, 255, 255, 0.02); border: 1px dashed rgba(255, 255, 255, 0.1); border-radius: 15px; padding: 40px 20px; text-align: center; margin-top: 20px; }
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
                <a href="../beranda/beranda.php" class="menu-item"><i class="fa-solid fa-house"></i> Beranda</a>
                <a href="../search/search.php" class="menu-item"><i class="fa-solid fa-magnifying-glass"></i> Jelajah</a>
                <a href="../beranda/saved.php" class="menu-item"><i class="fa-regular fa-bookmark"></i> Disimpan</a>
                <a href="profile.php" class="menu-item active"><i class="fa-solid fa-user"></i> Profile</a>
            </nav>
            <div class="sidebar-footer">
                <a href="../login/logout.php" class="menu-item logout"><i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar</a>
            </div>
        </aside>

        <main class="main-content">
            <header class="profile-header">
                <div>
                    <h1>Profile Saya</h1>
                    <p>Kelola informasi akun dan aktivitas Anda.</p>
                </div>
                <button class="btn-settings" onclick="openModal('modalSettings')" style="background:var(--bg-card); border:1px solid rgba(255,255,255,0.1); color:#fff; padding:10px 20px; border-radius:8px; cursor:pointer;">
                    <i class="fa-solid fa-gear"></i> Pengaturan Akun
                </button>
            </header>

            <div class="profile-grid">
                <div class="card-identity">
                    <img src="../beranda/img/<?php echo $foto_tampil; ?>?v=<?php echo $anti_cache; ?>" class="avatar-main" onclick="openModal('modalEditProfile')">
                    
                    <h2><?php echo htmlspecialchars($nama_user); ?></h2>
                    <p style="color:var(--text-muted); font-size:14px; margin-bottom: 15px;">Kritikus Film Junior</p>
                    <p style="font-size: 13px; color: #c4c6cf; line-height: 1.6; margin-bottom: 10px;">
                        <?php echo !empty($user_data['bio']) ? htmlspecialchars($user_data['bio']) : "Belum ada bio."; ?>
                    </p>
                    <p style="font-size: 11px; color: var(--text-muted); margin-bottom: 25px;">
                        Bergabung sejak <?php echo date('F Y', strtotime($user_data['created_at'])); ?>
                    </p>
                    
                    <div class="stats-row">
                        <div class="stat-item" onclick="openModal('modalRating')">
                            <strong><?php echo $jumlah_dinilai; ?></strong>
                            <span>Dinilai</span>
                        </div>
                        <div class="stat-item" onclick="openModal('modalSaved')">
                            <strong><?php echo $jumlah_disimpan; ?></strong>
                            <span>Disimpan</span>
                        </div>
                        <div class="stat-item" onclick="openModal('modalUlasan')">
                            <strong><?php echo $jumlah_diulas; ?></strong>
                            <span>Diulas</span>
                        </div>
                    </div>

                    <a href="../login/logout.php" class="btn-logout-alt">
                        <i class="fa-solid fa-right-from-bracket"></i> Keluar dari Akun
                    </a>
                </div>

                <div class="card-details">
                    <span class="section-title">Aktivitas Anda</span>
                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
                        <a href="javascript:void(0)" onclick="openModal('modalRating')" class="action-card">
                            <div style="display:flex; align-items:center;">
                                <div class="icon-box"><i class="fa-solid fa-play"></i></div>
                                <div><strong>Riwayat Tontonan</strong><p style="font-size:11px; color:var(--text-muted)">Film terakhir dinilai</p></div>
                            </div>
                            <i class="fa-solid fa-chevron-right" style="font-size:12px; opacity:0.3;"></i>
                        </a>
                        <a href="javascript:void(0)" onclick="openModal('modalSaved')" class="action-card">
                            <div style="display:flex; align-items:center;">
                                <div class="icon-box" style="color:#ff4757; background:rgba(255,71,87,0.1)"><i class="fa-solid fa-heart"></i></div>
                                <div><strong>Film Favorit</strong><p style="font-size:11px; color:var(--text-muted)">Kelola koleksi simpanan</p></div>
                            </div>
                            <i class="fa-solid fa-chevron-right" style="font-size:12px; opacity:0.3;"></i>
                        </a>
                    </div>

                    <span class="section-title" style="margin-top:40px;">Pengaturan Akun</span>
                    <div style="display:flex; flex-direction:column; gap:12px;">
                         <div class="action-card" onclick="openModal('modalKeamanan')" style="cursor:pointer;">
                            <div style="display:flex; align-items:center;">
                                <div class="icon-box" style="background:rgba(255,255,255,0.05); color:var(--text-muted)"><i class="fa-solid fa-shield-halved"></i></div>
                                <div><strong>Keamanan Akun</strong><p style="font-size:11px; color:var(--text-muted)">Kata sandi dan autentikasi</p></div>
                            </div>
                            <i class="fa-solid fa-chevron-right" style="font-size:12px; opacity:0.3;"></i>
                        </div>
                        <div class="action-card" onclick="openModal('modalBilling')" style="cursor:pointer;">
                            <div style="display:flex; align-items:center;">
                                <div class="icon-box" style="background:rgba(255,255,255,0.05); color:var(--text-muted)"><i class="fa-solid fa-credit-card"></i></div>
                                <div><strong>Langganan & Tagihan</strong><p style="font-size:11px; color:var(--text-muted)">Kelola paket langganan</p></div>
                            </div>
                            <i class="fa-solid fa-chevron-right" style="font-size:12px; opacity:0.3;"></i>
                        </div>
                        <div class="action-card" onclick="openModal('modalEditProfile')" style="cursor:pointer;">
                            <div style="display:flex; align-items:center;">
                                <div class="icon-box" style="background:rgba(255,255,255,0.05); color:var(--text-muted)"><i class="fa-solid fa-pen"></i></div>
                                <div><strong>Edit Profile</strong><p style="font-size:11px; color:var(--text-muted)">Ubah informasi pribadi</p></div>
                            </div>
                            <i class="fa-solid fa-chevron-right" style="font-size:12px; opacity:0.3;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <div id="modalBilling" class="modal">
        <div class="modal-content" style="width: 500px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                <h2 style="margin:0; font-size:20px; color:#fff;">Langganan & Tagihan</h2>
                <span class="close-modal" onclick="closeModal('modalBilling')">&times;</span>
            </div>
            <div class="subscription-card">
                <span style="color: var(--primary-red); font-size: 11px; font-weight: 800; letter-spacing: 1px; text-transform: uppercase;">Paket Saat Ini</span>
                <h1 style="margin: 5px 0 15px 0; font-size: 28px; color: #fff;">Gratis (Basic)</h1>
                <div class="benefit-list">
                    <div class="benefit-item">
                        <i class="fa-solid fa-check"></i> 
                        <span>Rating & Review Film Tanpa Batas</span>
                    </div>
                    <div class="benefit-item">
                        <i class="fa-solid fa-check"></i> 
                        <span>Simpan maksimal 100 Film</span>
                    </div>
                    <div class="benefit-item">
                        <i class="fa-solid fa-xmark"></i> 
                        <span style="opacity: 0.5;">Bebas Iklan</span>
                    </div>
                    <div class="benefit-item">
                        <i class="fa-solid fa-xmark"></i> 
                        <span style="opacity: 0.5;">Lencana Kritikus Pro</span>
                    </div>
                </div>
                <button class="btn-pro">
                    <i class="fa-solid fa-star"></i> Tingkatkan ke Pro (Rp 49.000/bln)
                </button>
            </div>
            <h3 style="font-size: 14px; color: #fff; margin: 30px 0 10px 0;">Riwayat Tagihan Terakhir</h3>
            <div class="billing-history-box">
                <i class="fa-solid fa-credit-card"></i>
                <p style="margin: 0; font-size: 14px; color: #fff; font-weight: 600;">Belum ada transaksi</p>
                <p style="margin: 5px 0 0 0; font-size: 12px; color: #565d70;">Anda saat ini menggunakan paket Gratis.</p>
            </div>
        </div>
    </div>

    <div id="modalKeamanan" class="modal">
        <div class="modal-content" style="width: 500px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px;">
                <h2 style="margin:0; font-size:20px; color:#fff;">Keamanan Akun</h2>
                <span class="close-modal" onclick="closeModal('modalKeamanan')">&times;</span>
            </div>
            <form action="proses_ganti_password.php" method="POST">
                <h3 style="font-size: 14px; color: #8b92a5; margin-bottom: 15px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px;">Ubah Kata Sandi</h3>
                <div class="form-group">
                    <label>Kata Sandi Saat Ini</label>
                    <input type="password" name="password_lama" placeholder="••••••••" required style="border: 1px solid rgba(229, 9, 20, 0.5);">
                </div>
                <div class="form-group">
                    <label>Kata Sandi Baru</label>
                    <input type="password" name="password_baru" placeholder="••••••••" required>
                </div>
                <div class="form-group">
                    <label>Konfirmasi Kata Sandi Baru</label>
                    <input type="password" name="konfirmasi_password" placeholder="••••••••" required>
                </div>
                <h3 style="font-size: 14px; color: #8b92a5; margin: 25px 0 15px 0; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px;">Keamanan Lanjutan</h3>
                <div style="background: rgba(255,255,255,0.03); padding: 15px; border-radius: 12px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                    <div>
                        <p style="margin: 0; font-size: 13px; color: #fff; font-weight: 600;">Autentikasi Dua Faktor (2FA)</p>
                        <p style="margin: 0; font-size: 11px; color: #565d70;">Tingkatkan keamanan akun menggunakan kode OTP</p>
                    </div>
                    <label class="switch"><input type="checkbox" disabled><span class="slider"></span></label>
                </div>
                <button type="submit" class="btn-save-profile">Perbarui Keamanan</button>
            </form>
        </div>
    </div>

    <div id="modalSettings" class="modal">
        <div class="modal-content" style="width: 450px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px;">
                <h2 style="margin:0; font-size:20px; color:#fff;">Pengaturan Aplikasi</h2>
                <span class="close-modal" onclick="closeModal('modalSettings')">&times;</span>
            </div>
            <div class="form-group" style="margin-bottom: 10px;">
                <label style="margin-bottom: 15px;"><i class="fa-solid fa-bell"></i> Preferensi Notifikasi</label>
                <div class="setting-row">
                    <span style="font-size: 13px; color: #fff;">Rekomendasi Film Mingguan</span>
                    <label class="switch"><input type="checkbox" checked><span class="slider"></span></label>
                </div>
                <div class="setting-row">
                    <span style="font-size: 13px; color: #fff;">Pemberitahuan Ulasan Baru</span>
                    <label class="switch"><input type="checkbox" checked><span class="slider"></span></label>
                </div>
                <div class="setting-row">
                    <span style="font-size: 13px; color: #fff;">Promo & Penawaran Berlangganan</span>
                    <label class="switch"><input type="checkbox"><span class="slider"></span></label>
                </div>
            </div>
            <div class="form-group">
                <label><i class="fa-solid fa-language"></i> Bahasa & Regional</label>
                <span style="font-size: 11px; color: #8b92a5; display: block; margin-bottom: 8px;">Bahasa Aplikasi</span>
                <select>
                    <option value="id">Bahasa Indonesia</option>
                    <option value="en" disabled>English (UK)</option>
                </select>
            </div>
            <button class="btn-save-profile" onclick="closeModal('modalSettings')">
                <i class="fa-solid fa-check"></i> Simpan Pengaturan
            </button>
            <p style="text-align:center; color:#565d70; font-size:11px; margin-top:20px;">RatingHub Mobile/Web Version 1.0.4-beta</p>
        </div>
    </div>

    <div id="modalEditProfile" class="modal">
        <div class="modal-content" style="width: 500px;">
            <span class="close-modal" onclick="closeModal('modalEditProfile')">&times;</span>
            <h2 style="margin-bottom:25px;">Edit Profil Saya</h2>
            <form action="proses_edit_profile.php" method="POST" enctype="multipart/form-data">
                <div style="display:flex; align-items:center; gap:20px; margin-bottom:30px;">
                    <img src="../beranda/img/<?php echo $foto_tampil; ?>?v=<?php echo $anti_cache; ?>" style="width:80px; height:80px; border-radius:50%; object-fit:cover; border:2px solid var(--primary-red);">
                    <input type="file" name="foto" style="display:none;" id="fileFoto">
                    <button type="button" onclick="document.getElementById('fileFoto').click()" style="background:rgba(255,255,255,0.05); color:#fff; border:none; padding:10px 20px; border-radius:8px; cursor:pointer; font-size:13px;">Ubah Foto Profil</button>
                </div>
                <div class="form-group">
                    <label>Nama Pengguna</label>
                    <input type="text" name="nama" value="<?php echo htmlspecialchars($user_data['nama_lengkap']); ?>">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>">
                </div>
                <div class="form-group">
                    <label>Bio Singkat</label>
                    <textarea name="bio" rows="4"><?php echo htmlspecialchars($user_data['bio']); ?></textarea>
                </div>
                <button type="submit" class="btn-save-profile">
                    <i class="fa-solid fa-check"></i> Simpan Perubahan
                </button>
            </form>
        </div>
    </div>

    <div id="modalRating" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('modalRating')">&times;</span>
            <h3 style="margin-bottom:25px;">Film yang Anda Nilai</h3>
            <div class="modal-grid">
                <?php 
                mysqli_data_seek($q_dinilai, 0);
                while($d = mysqli_fetch_assoc($q_dinilai)) {
                    $id_f = $d['id_film'];
                    if(isset($films[$id_f])){
                        echo "<div class='modal-movie-card'>
                                <div class='mini-rating'><i class='fa-solid fa-star'></i> {$d['rating']}.0</div>
                                <img src='../beranda/img/{$id_f}.jpg'>
                                <p>{$films[$id_f]['judul']}</p>
                              </div>";
                    }
                }
                ?>
            </div>
        </div>
    </div>

    <div id="modalSaved" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('modalSaved')">&times;</span>
            <h3 style="margin-bottom:25px;">Koleksi Tersimpan</h3>
            <div class="modal-grid" style="grid-template-columns: repeat(3, 1fr);">
                <?php 
                mysqli_data_seek($q_disimpan, 0);
                while($s = mysqli_fetch_assoc($q_disimpan)) {
                    $id_f = $s['id_film'];
                    if(isset($films[$id_f])){
                        echo "<div class='modal-movie-card' style='border: 1px solid var(--primary-red); border-radius:12px; padding:5px;'>
                                <div class='poster-container'>
                                    <i class='fa-solid fa-bookmark' style='position:absolute; top:10px; left:10px; color:var(--primary-red); z-index:10;'></i>
                                    <img src='../beranda/img/{$id_f}.jpg'>
                                    <div class='overlay-detail'>
                                        <a href='../beranda/detail.php?id={$id_f}' class='btn-mini-detail'>Lihat Detail</a>
                                    </div>
                                </div>
                                <p style='color:var(--primary-red); font-weight:700'>{$films[$id_f]['judul']}</p>
                              </div>";
                    }
                }
                ?>
            </div>
        </div>
    </div>

    <div id="modalUlasan" class="modal">
        <div class="modal-content" style="width: 500px;">
            <span class="close-modal" onclick="closeModal('modalUlasan')">&times;</span>
            <h3 style="margin-bottom:25px;">Riwayat Ulasan Anda</h3>
            <?php 
            mysqli_data_seek($q_dinilai, 0);
            while($u = mysqli_fetch_assoc($q_dinilai)) {
                $id_f = $u['id_film'];
                if(isset($films[$id_f])){
                ?>
                <div class="review-list-item">
                    <img src="../beranda/img/<?php echo $id_f; ?>.jpg">
                    <div class="review-info">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 5px;">
                            <img src="../beranda/img/<?php echo $foto_tampil; ?>?v=<?php echo $anti_cache; ?>" 
                                 style="width: 25px; height: 25px; border-radius: 50%; object-fit: cover; border: 1px solid var(--primary-red); flex-shrink: 0;">
                            <h4 style="margin: 0;"><?php echo $films[$id_f]['judul']; ?></h4>
                        </div>
                        <div class="stars">
                            <?php for($i=1;$i<=5;$i++) echo ($i<=$u['rating']) ? "★" : "☆"; ?> 
                        </div>
                        <p class="text">"<?php echo htmlspecialchars($u['komentar']); ?>"</p>
                    </div>
                </div>
            <?php } } ?>
        </div>
    </div>

    <script>
        function openModal(id) { document.getElementById(id).style.display = "block"; }
        function closeModal(id) { document.getElementById(id).style.display = "none"; }
        window.onclick = function(event) {
            if (event.target.className === "modal") { event.target.style.display = "none"; }
        }
    </script>
</body>
</html>