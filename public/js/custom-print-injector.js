/**
 * Custom Print Injector
 * Adds custom print buttons to purchase tables
 * Version: 1.0
 */

$(document).ready(function() {

    /**
     * Main function to add custom print buttons
     */
    function addCustomPrintButtons() {
        // Target purchase table
        var $table = $('#purchase_table');
        
        if (!$table.length) {
            return;
        }

        $table.find('tbody tr').each(function() {
            var $row = $(this);
            
            // Skip if button already exists
            if ($row.find('.custom-print-btn').length > 0) {
                return;
            }

            var purchaseId = findPurchaseId($row);

            if (purchaseId) {
                var actionCell = $row.find('td:first-child');
                
                if (actionCell.length) {
                    var printBtn = createPrintButton(purchaseId);
                    actionCell.append(printBtn);
                }
            }
        });
    }

    /**
     * Find purchase ID from row data
     */
    function findPurchaseId($row) {
        // Try data attribute
        var id = $row.data('id');
        if (id) return id;

        // Try to find from action buttons
        var actionBtn = $row.find('.view_purchase, .edit_purchase, .delete_purchase, .update_status');
        if (actionBtn.length) {
            id = actionBtn.data('purchase_id') || actionBtn.data('id');
            if (id) return id;
        }

        // Try to extract from href attributes
        var links = $row.find('a');
        var foundId = null;
        
        links.each(function() {
            var href = $(this).attr('href');
            if (href) {
                // Match /purchases/123 or /purchases/123/edit etc.
                var matches = href.match(/\/purchases\/(\d+)/);
                if (matches) {
                    foundId = matches[1];
                    return false; // break loop
                }
            }
        });

        return foundId;
    }

    /**
     * Create print button HTML
     */
    function createPrintButton(purchaseId) {
        return $(
            '<a href="/custom-print/purchase/' + purchaseId + '" ' +
            'target="_blank" ' +
            'class="btn btn-success btn-xs custom-print-btn" ' +
            'style="margin:2px;border-radius:4px;padding:3px 8px;font-size:10px;" ' +
            'data-toggle="tooltip" title="Print Custom Receipt">' +
            '<i class="fa fa-print"></i> New' +
            '</a>'
        );
    }

    // ============================================
    // EVENT LISTENERS
    // ============================================

    // Initial load with delay
    setTimeout(addCustomPrintButtons, 1500);

    // After DataTable draw/refresh
    $(document).on('draw.dt', function() {
        setTimeout(addCustomPrintButtons, 500);
    });

    // After modal opens
    $(document).on('shown.bs.modal', function() {
        setTimeout(addCustomPrintButtons, 500);
    });

    // When DOM changes (for dynamically loaded content)
    $(document).on('DOMNodeInserted', function(e) {
        if ($(e.target).find('#purchase_table').length) {
            setTimeout(addCustomPrintButtons, 500);
        }
    });

    // Also run when window loads completely
    $(window).on('load', function() {
        setTimeout(addCustomPrintButtons, 1000);
    });

    console.log('Custom Print Injector loaded successfully ✅');
});