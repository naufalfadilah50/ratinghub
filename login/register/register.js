document.addEventListener('DOMContentLoaded', function() {
    // Mengambil elemen form dan input berdasarkan ID yang ada di HTML
    const registerForm = document.getElementById('registerForm');
    const fullnameInput = document.getElementById('fullname');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm-password');

    // Menangani kejadian ketika tombol Daftar (submit) diklik
    registerForm.addEventListener('submit', function(event) {
        event.preventDefault(); // Mencegah reload halaman

        const fullnameValue = fullnameInput.value.trim();
        const emailValue = emailInput.value.trim();
        const passwordValue = passwordInput.value.trim();
        const confirmPasswordValue = confirmPasswordInput.value.trim();

        // Validasi input
        if (fullnameValue === '') {
            alert('Mohon masukkan nama lengkap Anda.');
            fullnameInput.focus();
            return;
        }

        if (emailValue === '') {
            alert('Mohon masukkan alamat email Anda.');
            emailInput.focus();
            return;
        }

        if (passwordValue === '') {
            alert('Mohon masukkan kata sandi.');
            passwordInput.focus();
            return;
        }

        if (passwordValue.length < 8) {
            alert('Kata sandi harus minimal 8 karakter.');
            passwordInput.focus();
            return;
        }

        if (confirmPasswordValue === '') {
            alert('Mohon konfirmasi kata sandi Anda.');
            confirmPasswordInput.focus();
            return;
        }

        if (passwordValue !== confirmPasswordValue) {
            alert('Kata sandi tidak cocok. Silakan periksa kembali.');
            confirmPasswordInput.focus();
            return;
        }
        
        // Jika validasi lolos
        alert(`Pendaftaran berhasil! Selamat bergabung di RatingHub, ${fullnameValue}.`);
        
        // Berpindah ke halaman login (keluar satu folder menggunakan ../)
        window.location.href = "../login.html"; 
    });
});