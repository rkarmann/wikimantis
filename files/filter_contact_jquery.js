

$(document).ready(function product(){

  $("#myInputContact").on("keyup", function() {
    var value_contact = $(this).val().toLowerCase();
    $("#myTableContact tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value_contact) > -1)
    });
  });

});
