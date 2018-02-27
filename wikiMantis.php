<?php

# Plugin WIKI Cylande
# Réalisation décembre 2017 - Janvier 2018

class wikiMantisPlugin extends MantisPlugin
{
  # Déclaration du plugin
  function register() {
    $this->name = 'wikiMantis';
    $this->description = plugin_lang_get( 'plugin_description' );
    $this->page = 'config_page';

    $this->version = '0.1.07-beta';
    $this->requires = array(
      "MantisCore" => "2.0.0",
    );

    $this->author = 'Romain Karmann';
    $this->contact = 'romainkarmann@gmail.com';
    $this->url = '';
  }

  # TODO: replace 'EVENT_LAYOUT_RESOURCES' & 'EVENT_LAYOUT_PAGE_FOOTER' by own Wiki events so that Mantis
  # UI is not modified by wiki CSS and Scripts
  function hooks() {
    return array(
      'EVENT_PLUGIN_INIT' => 'apis',
      'EVENT_CORE_READY' => 'check_install',
      'EVENT_CORE_HEADERS' => 'csp_headers',
      'EVENT_LAYOUT_RESOURCES' => 'wiki_resources',
      'EVENT_MENU_MAIN' => 'wiki_menu',
      'EVENT_LAYOUT_PAGE_FOOTER' => 'scripts',
    );
  }

  # Check if user is admin and registered into WikiDB
  function check_install() {

    $current_user_id = auth_get_current_user_id();

    if( user_is_administrator( $current_user_id ) )
      {

        if( wikiUser::user_is_registered( $current_user_id ) )
          {
            # Ne rien faire
          }
        else
          {
            $group_name = 'Admins';
            $user = new wikiUser( $current_user_id, 1, 1, 2, 2, 2);
            $user_group = $user->save_group( $group_name );
            $user_id = $user->save_user();
          }
      }
  }

  # Preload APIS
  function apis() {
    require_once("api/article_api.php");
    require_once("api/config_api.php");
    require_once("api/connection_api.php");
    require_once("api/consumer_api.php");
    require_once("api/contact_api.php");
    require_once("api/print_article_api.php");
    require_once("api/user_api.php");
    require_once("api/search_api.php");
    require_once("api/print_api.php");
  }

  # CSP fix
  function csp_headers() {
		http_csp_add( 'script-src', 'https://cdnjs.cloudflare.com' );
    http_csp_add( 'script-src', "'unsafe-inline'" );
    http_csp_add( 'img-src', "'self'" );
  }

  # Load script into MantisPage
  public function scripts()
  {
    # Implement CKeditor init
    echo '<script type="text/javascript" src="' . plugin_file( 'ckeditor_init.js' ) . '"></script>';

    # Implement Bootstrap filter
    echo '<script type="text/javascript" src="' . plugin_file( 'filter_jquery.js' ) . '"></script>';

    # Implement color-picker jscolor.js
    echo '<script type="text/javascript" src="' . plugin_file( "jscolor.js" ) . '"></script>';

    # Implement AJAX functions
    echo '<script type="text/javascript" src="' . plugin_file( "dynamic_selectbox.js" ) . '"></script>';

    # Implement JS functions
    echo '<script type="text/javascript" src="' . plugin_file( "search_user_email.js" ) . '"></script>';

  }

  function wiki_resources() {
    # Implement CKeditor RTE
    echo '<script type="text/javascript" src="plugins/ckeditor/ckeditor.js"></script>';

    # Implement CSS stylesheet
    echo '<link type="text/css" rel="stylesheet" href="' . plugin_file( 'custom_css.css' ) . '"/>';

    # Implement W3.CSS
    echo '<link type="text/css" rel="stylesheet" href="' . plugin_file( "w3.css" ) . '"/>';
  }


  # Add Wiki's link in the sidebar
  function wiki_menu() {
    # Only display the link when user is registered
    if( wikiUser::user_is_registered( auth_get_current_user_id() ))
    {
      $links = array();
      $links[] = array(
        'title' => 'Wiki Cylande',
        'url'   => plugin_page('wiki_main', true),
        'icon'  => 'fa-wikipedia-w'
      );
      return $links;
    }
  }

  # Database table schema
  #
  function schema()
  {
    return array(
      # Article table [ARTICLE] 21-12-2017
      array( "CreateTableSQL", array( plugin_table( "article" ),
        "
      id             I       NOTNULL UNSIGNED AUTOINCREMENT PRIMARY,
      id_author      I       NOTNULL,
      id_coauthor    XL      NULL,
      id_customer    I       NULL,
      type           I       NOTNULL,
      timestamp      I       NOTNULL UNSIGNED,
      object         XL      NOTNULL,
      category       XL      NOTNULL,
      description    XL      NOTNULL,
      keyword        XL      NULL,
      views          I       NULL
      ",
    array("mysql" => "DEFAULT CHARSET=utf8" ) ) ),
      # Customer table 31-12-2017
      array( "CreateTableSQL", array( plugin_table( "customer" ),
        "
      id            I       NOTNULL UNSIGNED AUTOINCREMENT PRIMARY,
      name          C(100)  NOTNULL,
      id_users      XL      NULL
      ",
    array( "mysql" => "DEFAULT CHARSET=utf8" ) ) ),
      # Contact table 02-01-2018
      array( "CreateTableSQL", array( plugin_table( "contact" ),
        "
      id            I       NOTNULL UNSIGNED AUTOINCREMENT PRIMARY,
      id_customer   I       NOTNULL,
      timestamp     I       NOTNULL UNSIGNED,
      firstname     C(100)  NOTNULL,
      lastname      C(100)  NOTNULL,
      phone         C(100)  NOTNULL,
      country       C(100)  NOTNULL,
      mobile        C(100)  NULL,
      mail          C(100)  NOTNULL,
      address       C(100)  NULL,
      info          XL      NULL,
      function      C(100)  NULL
      ",
    array( "mysql" => "DEFAULT CHARSET=utf8" ) ) ),
      # Category table 24-01-2018
      array( "CreateTableSQL", array( plugin_table( "category" ),
        "
      id            I       NOTNULL UNSIGNED AUTOINCREMENT PRIMARY,
      name          C(100)  NOTNULL,
      color         C(100)  NOTNULL,
      sub           XL      NULL
      ",
    array( "mysql" => "DEFAULT CHARSET=utf8" ) ) ),
      # Table des fiches [ITEM] 24-01-2018
      array( "CreateTableSQL", array( plugin_table( "user" ),
        "
      id                       I       NOTNULL UNSIGNED AUTOINCREMENT PRIMARY,
      user_id                  I       NOTNULL,
      user_group               I       NOTNULL,
      user_is_admin            I       NOTNULL DEFAULT=0,
      user_article_rights      I       NULL,
      user_connection_rights   I       NULL,
      user_contact_rights      I       NULL
      ",
    array( "mysql" => "DEFAULT CHARSET=utf8" ) ) ),
      # Table des fiches [ITEM] 24-01-2018
      array( "CreateTableSQL", array( plugin_table( "group" ),
        "
      id            I       NOTNULL UNSIGNED AUTOINCREMENT PRIMARY,
      group_name   C(100)   NOTNULL
      ",
    array( "mysql" => "DEFAULT CHARSET=utf8" ) ) ),
      # Setting table 09-02-2018
      array( "CreateTableSQL", array( plugin_table( "setting" ),
        "
      id                 I     NOTNULL UNSIGNED AUTOINCREMENT PRIMARY KEY,
      plugin_name        XL    NOTNULL DEFAUL='WikiMantis',
      welcome_title      I     NULL
      ",
    array( "mysql" => "DEFAULT CHARSET=utf8" ) ) ),
      # User preferences table
      array( "CreateTableSQL", array( plugin_table( "user_pref" ),
        "
      user_id              I     NOTNULL UNSIGNED AUTOINCREMENT PRIMARY KEY,
      user_home_pref       XL    NULL
      ",
    array( "mysql" => "DEFAULT CHARSET=utf8" ) ) ),
  );

  }
}



 ?>
