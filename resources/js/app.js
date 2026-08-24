import 'bootstrap';
import 'bootstrap-icons/font/bootstrap-icons.css';

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-password-toggle]').forEach(button => {
        button.addEventListener('click', () => {
            const input = document.getElementById(button.dataset.passwordToggle);
            if (!input) return;
            input.type = input.type === 'password' ? 'text' : 'password';
            button.innerHTML = input.type === 'password'
                ? '<i class="bi bi-eye"></i>'
                : '<i class="bi bi-eye-slash"></i>';
        });
    });

    document.querySelectorAll('[data-copy]').forEach(button => {
        button.addEventListener('click', async () => {
            await navigator.clipboard.writeText(button.dataset.copy);
            const old = button.innerHTML;
            button.innerHTML = '<i class="bi bi-check2"></i> Copied';
            setTimeout(() => button.innerHTML = old, 1200);
        });
    });
});