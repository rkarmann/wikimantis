
$(document).ready(function type(){

      $("#myInputtype").on("keyup", function() {
        var value_type = $(this).val().toLowerCase();
        $("#myTabletype tr").filter(function() {
          $(this).toggle($(this).text().toLowerCase().indexOf(value_type) > -1)
        });
      });
  });
