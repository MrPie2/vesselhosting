import 'bootstrap';
import $ from 'jquery';

window.$ = window.jQuery= $;

$.ajaxSetup({
    headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
})

$('#domain-search-form').on('submit', function(e) {
    e.preventDefault();
    var domain_text=$('.domain-text').val();
    $.ajax({
        url:'/domain-s',
        method:"POST",
        data:{domain_text:domain_text},
        success:function(response){
            $('.search-results').html(response.message).addClass('alert alert-success');
        }
    })
})

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
