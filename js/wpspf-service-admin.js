(function($){
    $('#doaction, #doaction2').on('click', function() {
        var action = (this.id === 'doaction') ? $('#bulk-action-selector-top').val() : $('bulk-action-selector-bottom').val();
        if( action === "-1" ){
            alert('Please select an action.');
            return false;
        }
        else if( action === 'delete' ) {
            return confirm('Permanently delete the selected items?');
        }
    });
})(jQuery);
