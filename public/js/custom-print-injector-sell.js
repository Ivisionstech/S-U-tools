/**
 * Custom Print Injector for Sell Module
 * Adds Custom Print functionality to Sell DataTable
 * Version: 1.0
 */

$(document).ready(function() {

    /**
     * Add Custom Print button to sell table
     */
    function addCustomPrintButtons() {
        // Target sell table
        var $table = $('#sell_table');
        
        if (!$table.length) {
            return;
        }

        $table.find('tbody tr').each(function() {
            var $row = $(this);
            
            // Skip if button already exists
            if ($row.find('.custom-print-sell-btn').length > 0) {
                return;
            }

            var sellId = findSellId($row);

            if (sellId) {
                var actionCell = $row.find('td:first-child');
                
                if (actionCell.length) {
                    var printBtn = createPrintButton(sellId);
                    actionCell.append(printBtn);
                }
            }
        });
    }

    /**
     * Find sell ID from row data
     */
    function findSellId($row) {
        // Try data attribute
        var id = $row.data('id');
        if (id) return id;

        // Try to find from action buttons
        var actionBtn = $row.find('.view-sale, .edit-sale, .delete-sale, .print-invoice');
        if (actionBtn.length) {
            id = actionBtn.data('sell_id') || actionBtn.data('id') || actionBtn.data('href');
            if (id) return id;
        }

        // Try to extract from href attributes
        var links = $row.find('a');
        var foundId = null;
        
        links.each(function() {
            var href = $(this).attr('href');
            if (href) {
                // Match /sells/123 or /sells/123/edit etc.
                var matches = href.match(/\/sells\/(\d+)/);
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
    function createPrintButton(sellId) {
        return $(
            '<a href="/custom-print/sell/' + sellId + '" ' +
            'target="_blank" ' +
            'class="btn btn-success btn-xs custom-print-sell-btn" ' +
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

    // When DOM changes
    $(document).on('DOMNodeInserted', function(e) {
        if ($(e.target).find('#sell_table').length) {
            setTimeout(addCustomPrintButtons, 500);
        }
    });

    // On window load
    $(window).on('load', function() {
        setTimeout(addCustomPrintButtons, 1000);
    });

    console.log('Custom Print Injector for Sell loaded successfully ✅');
});