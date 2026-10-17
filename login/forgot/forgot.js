document.addEventListener('DOMContentLoaded', function() {
    const forgotForm = document.getElementById('forgotForm');
    const emailInput = document.getElementById('email');
    
    const formContainer = document.getElementById('form-container');
    const successContainer = document.getElementById('success-container');
    const displayEmail = document.getElementById('display-email');

    forgotForm.addEventListener('submit', function(event) {
        event.preventDefault();

        const emailValue = emailInput.value.trim();

        if (emailValue === '') {
            alert('Mohon masukkan alamat email Anda.');
            emailInput.focus();
            return;
        }

        displayEmail.textContent = emailValue;
        formContainer.classList.add('hidden');
        successContainer.classList.remove('hidden');
    });
});