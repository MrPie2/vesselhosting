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

let domainCheckTimer = null;
let latestDomainRequest = 0;
let selectedDomainAvailable = false;

function setDomainStatus(message, type = 'muted') {
    $('#domain-status').removeClass('text-success text-danger text-muted')
        .addClass('text-' + type).text(message);
}

$(document).on('change', 'input[name="domain_option"]', function () {
    const option = $(this).val();
    $('#domain-config').toggleClass('d-none', !option);
    $('#domain-suggestions').addClass('d-none');
    $('#suggestion-list').empty();

    selectedDomainAvailable = option === 'existing' || option === 'transfer';
    $('.add_to_cart').prop('disabled', !window.cycle || !selectedDomainAvailable);

    if (option === 'register') {
        $('.domain-text').val('').trigger('focus');
        setDomainStatus('Start typing a domain and we will check availability automatically.');
    } else if (option === 'transfer') {
        $('.domain-text').val('');
        setDomainStatus('Enter the domain you want to transfer and continue to checkout.');
    } else {
        $('.domain-text').val('');
        setDomainStatus('Enter the domain you already own.');
    }
});

$(document).on('change', '.billing_cycle', function () {
    window.cycle = $(this).val();
    window.cycle_amount = $(this).attr('amount');
    $('.cart_amount').html('<h4 class="fw-bold">$' + window.cycle_amount + '</h4>');
    $('.add_to_cart').prop('disabled', !window.cycle || !selectedDomainAvailable);
});

$(document).on('input', '.domain-text', function () {
    const option = $('input[name="domain_option"]:checked').val();
    const domain = $(this).val().trim().toLowerCase();

    if (option !== 'register') {
        selectedDomainAvailable = domain.length > 0;
        $('.add_to_cart').prop('disabled', !window.cycle || !selectedDomainAvailable);
        return;
    }

    selectedDomainAvailable = false;
    $('.add_to_cart').prop('disabled', true);
    clearTimeout(domainCheckTimer);
    $('#domain-suggestions').addClass('d-none');
    $('#suggestion-list').empty();

    if (domain.length < 4) {
        setDomainStatus('Enter a domain such as yourbrand.com.');
        return;
    }

    domainCheckTimer = setTimeout(function () {
        const requestId = ++latestDomainRequest;
        $('#domain-spinner').removeClass('d-none');
        setDomainStatus('Checking availability...');

        $.get('/domain-av', {domain: domain})
            .done(function (response) {
                if (requestId !== latestDomainRequest) return;

                if (response.success && response.available) {
                    selectedDomainAvailable = true;
                    setDomainStatus('✓ ' + response.domain + ' is available.', 'success');
                    $('.add_to_cart').prop('disabled', !window.cycle);
                } else {
                    setDomainStatus('✕ ' + (response.domain || domain) + ' is not available.', 'danger');
                }
            })
            .fail(function () {
                if (requestId !== latestDomainRequest) return;
                setDomainStatus('We could not check this domain right now. Please try again.', 'danger');
            })
            .always(function () {
                if (requestId === latestDomainRequest) {
                    $('#domain-spinner').addClass('d-none');
                }
            });

        $.get('/domain-suggestions', {domain: domain})
            .done(function (response) {
                if (requestId !== latestDomainRequest) return;

                const suggestions = response.suggestions || [];
                if (!suggestions.length) return;

                $('#suggestion-list').html(suggestions.map(function (item) {
                    return '<button type="button" class="list-group-item list-group-item-action domain-suggestion d-flex justify-content-between align-items-center" data-domain="' + item + '">' +
                        '<span>' + item + '</span><i class="bi bi-arrow-up-right"></i></button>';
                }).join(''));

                $('#domain-suggestions').removeClass('d-none');
            });
    }, 650);
});

$(document).on('click', '.domain-suggestion', function () {
    $('.domain-text').val($(this).data('domain')).trigger('input');
});

$(document).on('click', '.add_to_cart', function () {
    const domainOption = $('input[name="domain_option"]:checked').val();
    const domain = $('.domain-text').val().trim();
    const planId = $('.plan').data('plan-id');

    if (!window.cycle || !domainOption || !domain || !selectedDomainAvailable) {
        setDomainStatus('Select a billing cycle and enter a valid domain first.', 'danger');
        return;
    }

    const button = $(this);
    button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Adding...');

    $.post('/cart/add', {
        plan_id: planId,
        billing_cycle: window.cycle,
        domain: domain,
        domain_option: domainOption
    })
    .done(function (response) {
        window.location.href = response.redirect;
    })
    .fail(function (xhr) {
        button.prop('disabled', false).text('Add to Cart');
        setDomainStatus('Unable to add this item to the cart.', 'danger');
    });
});

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
