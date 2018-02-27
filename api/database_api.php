<?php

/**
  * Database API
  *
  **/

class DatabaseAPI
{

  # Retrieve article table name
  public static function get_article_table()
  {
    return plugin_table( "article", "wikiMantis" );
  }

  # Retrieve setting table name
  public static function get_setting_table()
  {
    return plugin_table( "setting", "wikiMantis" );
  }

  # Retrieve customer table name
  public static function get_customer_table()
  {
    return plugin_table( "customer", "wikiMantis" );
  }

  # Retrieve contact table name
  public static function get_contact_table()
  {
    return plugin_table( "contact", "wikiMantis" );
  }

  # Retrieve user table name
  public static function get_user_table()
  {
    return plugin_table( "user", "wikiMantis" );
  }

  # Retrieve user preferences table name
  public static function get_user_pref_table()
  {
    return plugin_table( "user_pref", "wikiMantis" );
  }

  public static function delete_from_table( $table_name, $id )
  {
    $table = plugin_table( $table_name, "wikiMantis" );

    $query = "DELETE FROM {$table} WHERE id=" . db_param();
    $result = db_query( $query, array( $id ) );

    return !is_null( $result ) ? true : false;
  }

  public static function count_from_table( $table_name )
  {
    $table = plugin_table( $table_name, "wikiMantis" );

    $query = "SELECT count(*) FROM {$table}";
    $result = db_query( $query );
    $data = db_fetch_array( $result );

    return $data[ 'count(*)' ];
  }
}
 ?>
