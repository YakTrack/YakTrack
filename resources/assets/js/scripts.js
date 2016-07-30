$(document).ready( function() {

    /*
     * Delete item button
     **/
    $('.delete-item-button').click( function() {

        // Get item information from DOM
        var itemTypeContainer = $(this).closest('.item-type-container')
        var itemContainer = $(this).closest('.item-container');
     console.log($(itemTypeContainer).data('item-type'));   
        // Load information into delete item model
        $('.item-name').text($(itemContainer).data('item-name'));
        $('.item-type').text($(itemTypeContainer).data('item-type'));
        $('#delete-item-form').attr('action', $(itemContainer).data('item-destroy-route'));

        // Open modal dialog
        $('#delete-item-modal').modal('show'); 
    });
});
