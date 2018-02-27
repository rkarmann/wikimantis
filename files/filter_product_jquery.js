

$(document).ready(function product(){

  $("#myInputproduct").on("keyup", function() {
    var value_product = $(this).val().toLowerCase();
    $("#myTableproduct tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value_product) > -1)
    });
  });

});
