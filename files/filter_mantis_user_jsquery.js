
$(document).ready(function type(){

      $("#myInputUserMantis").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#myTableUserMantis tr").filter(function() {
          $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
      });
  });
