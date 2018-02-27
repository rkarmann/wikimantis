
$(document).ready(function(){

  $("#myInputView").on("keyup", function() {
    var value_view = $(this).val().toLowerCase();
    $("#myTableView tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value_view) > -1)
    });
  });
});
