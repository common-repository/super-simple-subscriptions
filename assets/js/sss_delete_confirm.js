jQuery(document).ready(function($) {

  // Set confirmation on delete functions.

  // Single delete (link)
  $('.delete a').click(function(e) {

    // Get the link href.
    var link = this;

    // Don't fire the link.
    e.preventDefault();

    // Show confirm box.
    if(confirm("Are you sure you want delete this subscription(s)?")) {

      //delete here.
      window.location = link.href;
    }
  });

  // Bulk delete.
  $('#doaction, #doaction2').click(function(e) {

    // Check if delete option is selected.
    if ($('#bulk-action-selector-top').val() == 'bulk-delete' || $('#bulk-action-selector-top').val() == 'bulk-delete') {

      // Disable delete function.
      e.preventDefault();

      // Show confirm box.
      if(confirm("Are you sure you want delete this subscription(s)?")) {

        // Fire delete form.
        $('#post-body form').submit();
      }
    }

  });

});