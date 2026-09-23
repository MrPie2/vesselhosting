(function (window, document) {
    'use strict';

    const $ = window.jQuery;

    if ($) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        });

        $('#domain-search-form').on('submit', function (e) {
            e.preventDefault();

            const domainText = $('.domain-text').val();

            $.ajax({
                url: '/domain-s',
                method: 'POST',
                data: { domain_text: domainText },
                success: function (response) {
                    $('.search-results').html(response.message);
                },
                error: function (xhr) {
                    console.error('Domain search failed:', xhr.status, xhr.responseText);
                }
            });
        });

        $(document).on('click', '.billing_cycle', function () {
            window.cycle = $(this).val();
            window.cycle_amount = $(this).attr('amount');
            $('.cart_amount').html('<h1>$' + window.cycle_amount + '</h1>');
        });

        $(document).on('click', '.add_to_cart', function () {
            const cycle = window.cycle;
            const amount = window.cycle_amount;
            const domain = $('.domain-text').val();
            const plan = $('.plan').attr('cpanel_planid');

            $.ajax({
                url: '/create-account',
                method: 'POST',
                data: {
                    cycle: cycle,
                    amount: amount,
                    domain: domain,
                    plan: plan
                },
                success: function (response) {
                    $('.cart_amount')
                        .html('<h5>' + response.message + '</h5>')
                        .addClass('alert alert-success');
                },
                error: function (xhr) {
                    console.error('Create account failed:', xhr.status, xhr.responseText);
                }
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
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
                    setTimeout(function () {
                        button.innerHTML = old;
                    }, 1200);
                } catch (error) {
                    console.error('Copy failed:', error);
                }
            });
        });
    });
})(window, document);
