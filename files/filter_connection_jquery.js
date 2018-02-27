
$(document).ready(function type(){

      $("#myInputConnection").on("keyup", function() {
        var value_connection = $(this).val().toLowerCase();
        $("#myTableConnection tr").filter(function() {
          $(this).toggle($(this).text().toLowerCase().indexOf(value_connection) > -1)
        });
      });
  });
