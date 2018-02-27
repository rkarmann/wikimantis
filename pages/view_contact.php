<?php

html_robots_noindex();

layout_page_header( plugin_lang_get( 'plugin_title' ) );

layout_page_begin( __FILE__ );

if( wikiUser::user_is_registered( auth_get_current_user_id() ) ){

# Appel de l'objet wikiContact
$t_contact = new wikiContact();

$t_result = '';

# Récupération des paramètres pour afficher les contacts par clients
$consumer_id = ( isset( $_GET[ 'id_client'] ) ) ? $_GET[ 'id_client' ] : NULL;
$id = ( isset( $_GET[ 'id' ] ) ) ? $_GET[ 'id' ] : NULL;
$contact_type = ( isset( $_GET[ 'type' ] ) ) ? $_GET[ 'type' ] : 'all';
$mode = ( isset( $_GET[ 'mode' ] ) ) ? $_GET[ 'mode' ] : NULL;
$parameters = ( isset( $_POST[ 'parameters' ] ) ) ? $_POST[ 'parameters' ] : NULL;

  if( !is_null( $mode ) && !is_null( $consumer_id) )
  {
    include 'wiki_sidebar.php';

    echo wikiContact::print_all_contacts_in_table( $consumer_id, $contact_type );

  } else {

  if( !is_null( $parameters ) )
  {
    $array_param = wikiArticleSetting::config_explode_parameters( $parameters );

    $type   = $array_param[0]; # type de paramètre
    $action = $array_param[1]; # action à réaliser
    $c_id = $array_param[2]; # ID du contact

      if( $type === 'contact' ){

      $firstname = ( isset($_POST[ 'firstname' ]) ) ? trim($_POST[ 'firstname' ] ): NULL;
      $lastname = ( isset($_POST[ 'lastname' ]) ) ? trim($_POST[ 'lastname' ]) : NULL;
      $phone = (( isset($_POST[ 'indicatif_ph' ]) ) ? trim($_POST[ 'indicatif_ph' ]) : NULL) . ' ' .
               (( isset($_POST[ 'phone' ]) ) ? trim($_POST[ 'phone' ]) : NULL);
      $mobile = (( isset($_POST[ 'indicatif_mb' ]) ) ? trim($_POST[ 'indicatif_mb' ]) : NULL) . ' ' .
               (( isset($_POST[ 'mobile' ]) ) ? trim($_POST[ 'mobile' ]) : NULL);
      $address = ( isset($_POST[ 'address' ]) ) ? trim($_POST[ 'address' ]) : NULL;
      $info = ( isset( $_POST[ 'info' ] ) ) ? trim($_POST[ 'info' ]) : NULL;
      $mail = (( isset($_POST[ 'mail_p' ]) ) ? trim($_POST[ 'mail_p' ]) : NULL) . '@' .
              (( isset($_POST[ 'mail_s' ]) ) ? trim($_POST[ 'mail_s' ]) : NULL);
      $function = ( isset($_POST[ 'function' ]) ) ? trim($_POST[ 'function' ]) : NULL;
      $id_client = ( isset($_POST[ 'id_client' ]) ) ? trim($_POST[ 'id_client' ]) : NULL;
      $contact_type = ( isset( $_POST[ 'contact_type' ] ) ) ? trim($_POST[ 'contact_type'] ) : NULL;
      $consumer_id = $id_client;

      switch ( $action )
      {
        case 'create':

        $contact = new wikiContact( $firstname, $lastname, $phone, $mobile, $mail, $address, $info, $function, $id_client, $contact_type );
        $t_id = $contact->save();
        $t_result = ( !empty($t_id) ) ? 'created' : 'error_creation';
        echo wikiArticleSetting::print_successful_message( $t_result, $type );

        break;
        case 'modify':

        $contact = new wikiContact( $firstname, $lastname, $phone, $mobile, $mail, $address, $info, $function, $id_client, $contact_type );
        $t_id = $contact->update( $c_id );
        $t_result = ( !empty($t_id) ) ? 'modified' : 'error_modification';
        echo wikiArticleSetting::print_successful_message( $t_result, $type );

        break;
        case 'delete':

        $t_id = $t_contact->contact_delete_by_id( $c_id );
        $t_result = ( !empty($t_id) ) ? 'deleted' : 'error_deletion';
        echo wikiArticleSetting::print_successful_message( $t_result, $type );

        break;
      }
    }
  }

  include 'wiki_sidebar.php';

  if( !is_null( $consumer_id) && is_null( $id ) )
  {

    if( wikiContact::contact_exists( $consumer_id, $contact_type ) )
    {
      echo $t_contact->print_all_contacts_by_consumer_id( $consumer_id, $contact_type, NULL );

    } else {

      echo $t_contact->contact_default_welcome_message();

    }

  } elseif( !is_null( $consumer_id ) && !is_null( $id ) ) {

    echo $t_contact->print_all_contacts_by_consumer_id( $consumer_id, $contact_type, $id );

  } else {


  }

}

} else {

  echo wikiUser::print_user_not_registered_message();

}

layout_page_end();

?>
