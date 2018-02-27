
$(document).ready(function type(){

      $("#myInputConsumer").on("keyup", function() {
        var value_consumer = $(this).val().toLowerCase();
        $("#myTableConsumer tr").filter(function() {
          $(this).toggle($(this).text().toLowerCase().indexOf(value_consumer) > -1)
        });
      });
  });
