
$(document).ready(function module(){

      $("#myInputmodule").on("keyup", function() {
        var value_module = $(this).val().toLowerCase();
        $("#myTablemodule tr").filter(function() {
          $(this).toggle($(this).text().toLowerCase().indexOf(value_module) > -1)
        });
      });
  });
