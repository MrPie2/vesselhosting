(function (window, document) {
    'use strict';

    const $ = window.jQuery;

    if (!$) return;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    });

    let selectedDomain = null;
    let selectedAvailable = false;
    let checkTimer = null;

    function updateAddToCartState() {
        const billing = $('.billing_cycle:checked').length > 0;
        const option = $('input[name="domain_option"]:checked').val();
        const domain = ($('#domain-input').val() || '').trim().toLowerCase();

        let valid = billing && !!option && !!domain;

        if (option === 'register') {
            valid = valid && selectedAvailable && selectedDomain === domain;
        }

        $('.add_to_cart').prop('disabled', !valid);
    }

    function showDomainResult(message, success) {
        $('#domain-status')
            .removeClass('text-success text-danger')
            .addClass(success ? 'text-success' : 'text-danger')
            .text(message);
    }

    function loadSuggestions(domain) {
        $.get('/domain-suggestions', { domain: domain })
            .done(function (response) {
                const suggestions = response.suggestions || [];
                if (!suggestions.length) {
                    $('#domain-suggestions').addClass('d-none');
                    return;
                }

                $('#suggestion-list').empty();

                suggestions.forEach(function (domainName) {
                    $('<button>', {
                        type: 'button',
                        class: 'list-group-item list-group-item-action domain-suggestion',
                        text: domainName
                    }).attr('data-domain', domainName).appendTo('#suggestion-list');
                });

                $('#domain-suggestions').removeClass('d-none');
            });
    }

    function checkDomain(domain) {
        domain = (domain || '').trim().toLowerCase();

        if (!domain) {
            selectedDomain = null;
            selectedAvailable = false;
            $('#domain-status').text('');
            $('#domain-suggestions').addClass('d-none');
            updateAddToCartState();
            return;
        }

        selectedDomain = null;
        selectedAvailable = false;
        updateAddToCartState();

        $('#domain-spinner').removeClass('d-none');
        $('#domain-status').removeClass('text-success text-danger').text('Checking domain availability...');

        $.get('/domain-av', { domain: domain })
            .done(function (response) {
                if (response.success && response.available) {
                    selectedDomain = (response.domain || domain).toLowerCase();
                    selectedAvailable = true;
                    showDomainResult(selectedDomain + ' is available.', true);
                } else {
                    selectedDomain = null;
                    selectedAvailable = false;
                    showDomainResult(response.message || domain + ' is not available.', false);
                    loadSuggestions(domain);
                }

                updateAddToCartState();
            })
            .fail(function (xhr) {
                selectedDomain = null;
                selectedAvailable = false;
                showDomainResult(xhr.responseJSON?.message || 'Unable to check domain availability.', false);
                updateAddToCartState();
            })
            .always(function () {
                $('#domain-spinner').addClass('d-none');
            });
    }

    $('#domain-search-form').on('submit', function (e) {
        e.preventDefault();

        $.ajax({
            url: '/domain-s',
            method: 'POST',
            data: { domain_text: $('.domain-text').val() },
            success: function (response) {
                $('.search-results').html(response.message);
            }
        });
    });

    $(document).on('change', '.billing_cycle', function () {
        window.cycle = $(this).val();
        window.cycle_amount = $(this).attr('amount');
        $('.cart_amount').html('<h1>$' + window.cycle_amount + '</h1>');
        updateAddToCartState();
    });

    $(document).on('change', 'input[name="domain_option"]', function () {
        const option = $(this).val();

        $('#domain-config').removeClass('d-none');
        $('#domain-input').val('');
        $('#domain-status').text('');
        $('#domain-suggestions').addClass('d-none');
        $('#suggestion-list').empty();
        $('#transfer-code-wrap').toggleClass('d-none', option !== 'transfer');
        $('#domain-search-btn').toggleClass('d-none', option !== 'register');

        selectedDomain = null;
        selectedAvailable = false;
        updateAddToCartState();
    });

    $(document).on('input', '#domain-input', function () {
        const option = $('input[name="domain_option"]:checked').val();

        selectedDomain = null;
        selectedAvailable = false;
        $('#domain-status').text('');
        updateAddToCartState();

        if (option !== 'register') return;

        clearTimeout(checkTimer);

        const domain = $(this).val().trim();
        if (!domain) return;

        checkTimer = setTimeout(function () {
            checkDomain(domain);
        }, 650);
    });

    $(document).on('click', '#domain-search-btn', function () {
        checkDomain($('#domain-input').val());
    });

    $(document).on('click', '.domain-suggestion', function () {
        const domain = $(this).attr('data-domain');
        $('#domain-input').val(domain);
        checkDomain(domain);
    });

    $(document).on('click', '.add_to_cart', function () {
        const option = $('input[name="domain_option"]:checked').val();
        const domain = ($('#domain-input').val() || '').trim();
        const billingCycle = $('.billing_cycle:checked').val();
        const planId = $('.plan').attr('data-plan-id');

        if (!billingCycle || !option || !domain) return;

        const button = $('.add_to_cart');
        button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Adding...');

        $.ajax({
            url: '/cart/add',
            method: 'POST',
            data: {
                plan_id: planId,
                billing_cycle: billingCycle,
                domain: domain,
                domain_option: option
            },
            success: function (response) {
                window.location.href = response.redirect || '/cart';
            },
            error: function (xhr) {
                $('.cart_amount').html('<div class="alert alert-danger mb-0">' +
                    (xhr.responseJSON?.message || 'Unable to add this item to your cart.') +
                    '</div>');
                button.prop('disabled', false).text('Add to Cart');
            }
        });
    });

    document.querySelectorAll('[data-password-toggle]').forEach(function (button) {
        button.addEventListener('click', function () {
            const input = document.getElementById(button.dataset.passwordToggle);
            if (!input) return;

            input.type = input.type === 'password' ? 'text' : 'password';
            button.innerHTML = input.type === 'password'
                ? '<i class="bi bi-eye"></i>'
                : '<i class="bi bi-eye-slash"></i>';
        });
    });

    document.querySelectorAll('[data-copy]').forEach(function (button) {
        button.addEventListener('click', async function () {
            try {
                await navigator.clipboard.writeText(button.dataset.copy);
                const old = button.innerHTML;
                button.innerHTML = '<i class="bi bi-check2"></i> Copied';
                setTimeout(function () { button.innerHTML = old; }, 1200);
            } catch (error) {
                console.error('Copy failed:', error);
            }
        });
    });
})(window, document);
