
  $(document).ready(function(){

  // view_all_articles
  $("#myInputView").on("keyup", function a() {
    var value_view = $(this).val().toLowerCase();
    $("#myTableView tr").filter(function aa() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value_view) > -1)
    });
  });

  // config_page
  $("#myInputtype").on("keyup", function b() {
    var value_type = $(this).val().toLowerCase();
    $("#myTabletype tr").filter(function bb() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value_type) > -1)
    });
  });

  // config_page
  $("#myInputproduct").on("keyup", function c() {
    var value_product = $(this).val().toLowerCase();
    $("#myTableproduct tr").filter(function cc() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value_product) > -1)
    });
  });

  // config_page
  $("#myInputmodule").on("keyup", function d() {
    var value_module = $(this).val().toLowerCase();
    $("#myTablemodule tr").filter(function dd() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value_module) > -1)
    });
  });

  // config_page
  $("#myInputUserMantis").on("keyup", function e() {
    var value = $(this).val().toLowerCase();
    $("#myTableUserMantis tr").filter(function ee() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });

  // config_page
  $("#myInputItem").on("keyup", function f() {
    var value_item = $(this).val().toLowerCase();
    $("#myTableItem tr").filter(function ff() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value_item) > -1)
    });
  });

  // config_page view_contact
  $("#myInputContact").on("keyup", function g() {
    var value_contact = $(this).val().toLowerCase();
    $("#myTableContact tr").filter(function gg() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value_contact) > -1)
    });
  });

  // config_page
  $("#myInputConsumer").on("keyup", function h() {
    var value_consumer = $(this).val().toLowerCase();
    $("#myTableConsumer tr").filter(function hh() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value_consumer) > -1)
    });
  });

  // config_page view_connection
  $("#myInputConnection").on("keyup", function i() {
    var value_connection = $(this).val().toLowerCase();
    $("#myTableConnection tr").filter(function ii() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value_connection) > -1)
    });
  });

  });
