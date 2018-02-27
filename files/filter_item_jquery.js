
$(document).ready(function type(){

      $("#myInputItem").on("keyup", function() {
        var value_item = $(this).val().toLowerCase();
        $("#myTableItem tr").filter(function() {
          $(this).toggle($(this).text().toLowerCase().indexOf(value_item) > -1)
        });
      });
  });
