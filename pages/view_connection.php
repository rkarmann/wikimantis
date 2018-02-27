<?php

html_robots_noindex();

layout_page_header( plugin_lang_get( 'plugin_title' ) );

layout_page_begin( __FILE__ );

if( wikiUser::user_is_registered( auth_get_current_user_id() ) ){



# Appel de l'objet wikiConnection
$t_connection = new wikiConnection();

# Récupération des paramètres pour afficher les connexions par produits
$id_client = ( isset( $_GET[ 'id_client' ] ) ) ? $_GET[ 'id_client' ] : NULL;
$id = ( isset( $_GET[ 'id'] ) ) ? $_GET[ 'id' ] : NULL;
$mode = ( isset( $_GET[ 'mode' ] ) ) ? (int)($_GET[ 'mode' ]) : NULL;
$parameters = ( isset( $_POST[ 'parameters' ] ) ) ? $_POST[ 'parameters' ] : NULL;

if( !is_null( $parameters) )
{
  $array_param = wikiArticleSetting::config_explode_parameters( $parameters );

  $type = $array_param[0]; # type de paramètre
  $action = $array_param[1]; # action à réaliser
  $connection_id = $array_param[2]; # ID de la connexion

  $product = ( isset($_POST[ 'product' ]) ) ? $_POST[ 'product' ] : 0;
  $consumer_id = ( isset($_POST[ 'id_client' ]) ) ? $_POST[ 'id_client' ] : NULL;
  $category = ( isset($_POST[ 'category' ]) ) ? $_POST[ 'category' ] : NULL;
  $title = ( isset($_POST[ 'title' ]) ) ? $_POST[ 'title' ] : NULL;
  $description = ( isset($_POST[ 'description' ]) ) ? $_POST[ 'description' ] : NULL;

  $id_client = $consumer_id;

    switch ( $action )
    {
      case 'create':

      $connection = new wikiConnection( $product, $consumer_id, $category, $title, $description );
      $t_id = $connection->save();
      $t_result = ( !empty( $t_id ) ) ? 'created' : 'error_creation';
      echo wikiArticleSetting::print_successful_message( $t_result, $type );

      break;
      case 'modify':

      $connection = new wikiConnection( $product, $consumer_id, $category, $title, $description );
      $t_id = $connection->update( $connection_id );
      $t_result = ( !empty( $t_id ) ) ? 'modified' : 'error_modification';
      echo wikiArticleSetting::print_successful_message( $t_result, $type );

      break;
      case 'delete':

      $t_id = $t_connection->connection_delete_by_id( $connection_id );
      $t_result = ( !empty( $t_id ) ) ? 'deleted' : 'error_deletion';
      echo wikiArticleSetting::print_successful_message( $t_result, $type );

      break;
    } # Fin du switch

  }

    include 'wiki_sidebar.php';

    if( !is_null( $mode ) )
    {
    echo wikiConnection::print_view_mode_bar( NULL, $id_client );
    echo wikiConnection::connection_print_all_in_table( $id_client );
    }
    elseif( !is_null( $id_client ) && is_null( $id ) && is_null( $mode ) )
    {
      if( wikiConnection::connection_exists( $id_client ) )
      {
        echo wikiConnection::print_view_mode_bar( NULL, $id_client );
        echo wikiConnection::connection_print_in_card_by_id( $id_client, NULL );
      }
      else
      {
        echo wikiConnection::connection_default_welcome_message();
      }
    }
    elseif( !is_null( $id_client ) && !is_null( $id ) && is_null( $mode ) )
    {
      echo wikiConnection::print_view_mode_bar( NULL, $id_client );
      echo $t_connection->connection_print_in_card_by_id( $id_client, $id );
    }
    else
    {
        # Ne rien faire...
    }

} else {

  echo wikiUser::print_user_not_registered_message();

}

layout_page_end();

?>
