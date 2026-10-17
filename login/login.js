document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.querySelector('form');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');

    loginForm.addEventListener('submit', function(event) {
        event.preventDefault();

        const emailValue = emailInput.value.trim();
        const passwordValue = passwordInput.value.trim();

        if (emailValue === '') {
            alert('Mohon masukkan alamat email Anda.');
            emailInput.focus();
            return; 
        }

        if (passwordValue === '') {
            alert('Mohon masukkan kata sandi Anda.');
            passwordInput.focus();
            return; 
        }

        alert('Berhasil masuk! Anda sedang dialihkan ke Beranda...');
        
        // Keluar folder login, masuk folder beranda
        window.location.href = "../beranda/beranda.php";
    });
});