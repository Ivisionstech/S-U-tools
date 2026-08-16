$(document).ready(function() {
    function addCustomPrintButtons() {
        $('#purchase_table tbody tr').each(function() {
            var $row = $(this);
            if ($row.find('.custom-print-btn').length) return;
            
            var purchaseId = $row.data('id');
            if (!purchaseId) {
                var viewBtn = $row.find('.view_purchase, .edit_purchase');
                if (viewBtn.length) {
                    purchaseId = viewBtn.data('purchase_id') || viewBtn.data('id');
                }
            }
            
            if (purchaseId) {
                $row.find('td:first-child').append(
                    '<a href="/custom-print/purchase/' + purchaseId + '" target="_blank" ' +
                    'class="btn btn-success btn-xs custom-print-btn" style="margin:2px;">' +
                    '<i class="fa fa-print"></i> New</a>'
                );
            }
        });
    }
    
    setTimeout(addCustomPrintButtons, 1500);
    $(document).on('draw.dt', function() {
        setTimeout(addCustomPrintButtons, 500);
    });
});