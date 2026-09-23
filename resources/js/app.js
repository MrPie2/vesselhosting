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
            $('.search-results').html(response.message);
        }
    })
})

$('.billing_cycle').click(function(){
    window.cycle=$(this).val();
    window.cycle_amount=$(this).attr('amount');
 $('.cart_amount').html("<h1>$"+(window.cycle_amount)+"</h1>");
})


$(document).on('click', '.add_to_cart', function(){
 let cycle=window.cycle;
let amount=window.cycle_amount;
var domain=$('.domain-text').val();
var plan=$('.plan').attr('cpanel_planid');
$.ajax({
url:'/create-account',
method:'POST',
data:{cycle:cycle,amount:amount,domain:domain,plan:plan},
success:function(response){
$('.cart_amount').html("<h5>" + response.message + "</h5>").addClass('alert alert-success');
},
error: function (xhr) {
        console.log('STATUS:', xhr.status);
        console.log('RESPONSE:', xhr.responseText);
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
