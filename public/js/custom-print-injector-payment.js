/**
 * Custom Print Injector for Payment Modal
 * Version: 3.0 - Final
 * Adds Custom Print button to payment view modal - Clean version with no duplicates
 */

$(document).ready(function() {

    function addCustomPrintButtonToPaymentModal() {
        // Look for any visible modal
        var $modal = $('.modal:visible');
        
        if (!$modal.length) {
            return;
        }

        // Remove any existing custom print buttons first (prevent duplicates)
        $modal.find('.custom-print-payment-btn').remove();

        // Find the modal footer
        var $footer = $modal.find('.modal-footer');
        
        if (!$footer.length) {
            return;
        }

        // Check if there's already a print button, replace it
        var $existingPrintBtn = $footer.find('.tw-dw-btn-primary, .btn-primary, .tw-dw-btn-info');
        
        // Get transaction ID and type from the modal
        var transactionId = 1;
        var transactionType = 'sell';

        // Try to find transaction ID from the modal content
        var $body = $modal.find('.modal-body');
        var $links = $body.find('a');
        
        $links.each(function() {
            var href = $(this).attr('href');
            if (href) {
                var sellMatch = href.match(/\/sells\/(\d+)/);
                var purchaseMatch = href.match(/\/purchases\/(\d+)/);
                if (sellMatch) {
                    transactionId = sellMatch[1];
                    transactionType = 'sell';
                    return false;
                } else if (purchaseMatch) {
                    transactionId = purchaseMatch[1];
                    transactionType = 'purchase';
                    return false;
                }
            }
        });

        // Also check data-href attributes
        if (transactionId == 1) {
            $body.find('[data-href]').each(function() {
                var href = $(this).data('href');
                if (href) {
                    var sellMatch = href.match(/\/sells\/(\d+)/);
                    var purchaseMatch = href.match(/\/purchases\/(\d+)/);
                    if (sellMatch) {
                        transactionId = sellMatch[1];
                        transactionType = 'sell';
                        return false;
                    } else if (purchaseMatch) {
                        transactionId = purchaseMatch[1];
                        transactionType = 'purchase';
                        return false;
                    }
                }
            });
        }

        // Also check for transaction ID from the modal title or content
        if (transactionId == 1) {
            var modalText = $modal.text();
            var idMatch = modalText.match(/ID[:\s]+(\d+)/i);
            if (idMatch) {
                transactionId = idMatch[1];
                if (modalText.includes('Sell') || modalText.includes('sale') || modalText.includes('Invoice')) {
                    transactionType = 'sell';
                } else if (modalText.includes('Purchase') || modalText.includes('purchase') || modalText.includes('Ref No')) {
                    transactionType = 'purchase';
                }
            }
        }

        // Create the button with dynamic URL
        var printBtn = $(
            '<a href="/custom-print/' + transactionType + '/' + transactionId + '" ' +
            'target="_blank" ' +
            'class="btn btn-success custom-print-payment-btn no-print" ' +
            'style="margin-right:5px; border-radius:4px; padding:8px 20px; color:white; background:#28a745; text-decoration:none; display:inline-block;">' +
            '<i class="fas fa-print"></i> Print' +
            '</a>'
        );

        // If there's an existing print button, replace it
        if ($existingPrintBtn.length) {
            $existingPrintBtn.replaceWith(printBtn);
        } else {
            // Otherwise add it at the beginning
            $footer.prepend(printBtn);
        }
    }

    // Run immediately
    setTimeout(function() {
        addCustomPrintButtonToPaymentModal();
    }, 500);

    // Run after 2 seconds
    setTimeout(function() {
        addCustomPrintButtonToPaymentModal();
    }, 2000);

    // Run when modal opens
    $(document).on('shown.bs.modal', function(e) {
        setTimeout(function() {
            addCustomPrintButtonToPaymentModal();
        }, 300);
    });

    // Run when any AJAX completes
    $(document).ajaxComplete(function() {
        setTimeout(function() {
            addCustomPrintButtonToPaymentModal();
        }, 300);
    });

    console.log('Custom Print Injector for Payment loaded successfully ✅');
});