<?php

    /**
    * Consumer API
    * @package wikiCylande/api
    * */

class CustomerAPI
{
  # ID du client
  public $c_id;

  # Nom du client
  public $c_name;

  # Logo du client 'URL'
  public $c_logo;

  # Constructeur
  function __construct( $name ="", $logo="" )
  {
    $this->c_name = $name;
    $this->c_logo = $logo;
  }

  # Sauvegarde dans la base de données
   public function save()
  {
    # Sélection de la table "consumer"
    $t_consumer_table = plugin_table( "consumer", "wikiCylande" );

    # Fabrication de la requête
    $t_query = "INSERT INTO {$t_consumer_table}
               (
                 name,
                 logo
               ) VALUES (
                 " . db_param() . ",
                 " . db_param() . "
               )";

    # Envoi de la requête et transmission des paramètres
    db_query( $t_query, array( $this->c_name, $this->c_logo ) );

    # Insertion de l'id dans la table (PRIMARY KEY)
    $this->c_id = db_insert_id( $t_consumer_table );

    # Renvoi de l'id
    return $this->c_id;

  }

  # Mise à jour de la base de données
  public function update( $c_id )
  {
    # Sélection de la table "consumer"
    $t_consumer_table = plugin_table( "consumer", "wikiCylande" );

    # Fabrication de la requête de mise à jour
    $t_query = "UPDATE {$t_consumer_table} SET
                name=" . db_param() . ",
                logo=" . db_param() . "
                WHERE id=" . db_param();

    # Envoi de la requête et transmission des paramètres
    db_query( $t_query, array( $this->c_name, $this->c_logo, $c_id ) );

    # Renvoyer l'id
    return $c_id;

  }

  public function consumer_count_related_contact( $consumer_id )
  {
    $t_contact_table = plugin_table( "contact", "wikiCylande" );

    $t_query = "SELECT count(*) FROM {$t_contact_table} WHERE id_client=" . db_param();
    $t_result = db_query( $t_query, array( $consumer_id ) );

    $t_count = db_fetch_array( $t_result );

    $count = $t_count[ 'count(*)' ];

    $t_display = '';

    if( $count >= 1 )
    {
      $t_display .= '<a href="' . plugin_page( 'view_contact' ) . '&amp;id_client=' . $consumer_id . '&amp;type=consumer&amp;mode=1' . '" class="w3-text-green">';

      $t_display .= '<p class="w3-text-green">';

      $t_display .= ( $count > 1 ) ? $count . '&nbsp;' . plugin_lang_get( 'contacts' ) : $count . '&nbsp;' . plugin_lang_get( 'contact' );

      $t_display .= '</p></a>';

    } else {

      $t_display .= '<p class="w3-text-red">';

      $t_display .= plugin_lang_get( 'no_related_contact' );

      $t_display .= '</p>';

    }

    return $t_display;
  }

  public function consumer_count_related_connection( $consumer_id )
  {
    $t_connection_table = plugin_table( "connection", "wikiCylande" );

    $t_query = "SELECT count(*) FROM {$t_connection_table} WHERE id_client=" . db_param();
    $t_result = db_query( $t_query, array( $consumer_id ) );

    $t_count = db_fetch_array( $t_result );

    $count = $t_count[ 'count(*)' ];

    $t_display = '';

    if( $count >= 1 )
    {
      $t_display .= '<a href="' . plugin_page( 'view_connection' ) . '&amp;id_client=' . $consumer_id . '" class="w3-text-green">';

      $t_display .= ( $count > 1 ) ? $count . '&nbsp;' . plugin_lang_get( 'connections' ) : $count . '&nbsp;' . plugin_lang_get( 'connection' );

      $t_display .= '</a>';

    } else {

      $t_display .= '<p class="w3-text-red">';

      $t_display .= plugin_lang_get( 'no_related_connection' );

      $t_display .= '</p>';

    }

    return $t_display;
  }

  public function consumer_print_all_in_table()
  {
    $t_consumer_table = plugin_table( "consumer", "wikiCylande" );

    $t_query = "SELECT * FROM {$t_consumer_table} ORDER BY name ASC";
    $t_results = db_query( $t_query );

    $t_setting = new wikiArticleSetting();

    $t_consumer = new wikiConsumer();

    $t_display = '
                  <table>
                    <tr>
                      <td class="w3-gray" width="10%" style="text-align:center;">
                       <span class="w3-text-white"><i class="ace-icon fa fa-search">&nbsp;</i></span>
                      </td>
                      <td width="90%">
                       <input class="form-control w3-card-2" style="width:100%;" size="100%" id="myInputConsumer" type="text" placeholder="' . plugin_lang_get( 'search_consumer' ) . '"/>
                      </td>
                    </tr>
                   </table>
                   <table class="w3-table-all w3-hoverable" >
                    <thead>
                    <tr>
                      <th>' . plugin_lang_get( 'consumer' ) . '</th>
                      <th style="text-align:center;">
                        <i class="ace-icon fa fa-pencil"></i>
                      </th>
                      <th style="text-align: center;">
                        <i class="ace-icon fa fa-trash"></i>
                      </th>
                    </tr>
                    </thead>
                    <tbody id="myTableConsumer">';


    while( $t_consumer = db_fetch_array( $t_results ) )
    {
      $consumer = $t_consumer[ 'name'];
      $logo = $t_consumer[ 'logo' ];

      $t_display .= '<tr>
        <td><p>' . $consumer . '</p>
        ' . $this->consumer_count_related_contact( $t_consumer[ 'id' ] ) . '
        ' . $this->consumer_count_related_connection( $t_consumer[ 'id' ] )
        . '</td>
        <td style="text-align:center;vertical-align: middle;">
        <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#ModalModifyConsumer' . $t_consumer[ 'id' ] . '">OK</button>
        </td>
        <td style="text-align:center;vertical-align: middle;">
        <button type="button" class="btn w3-hover-red btn-sm" data-toggle="modal" data-target="#ModalDeleteConsumer' . $t_consumer[ 'id' ] . '">OK</button>
        </td>
      </tr>

      <div id="ModalDeleteConsumer' . $t_consumer[ 'id' ] . '" class="modal w3-animate-zoom" role="dialog">
        <div class="modal-dialog modal-md">
           <div class="modal-content">
             <form action="" method="post">
               <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="ace-icon fa fa-cogs"></i>&nbsp;' . plugin_lang_get( 'delete_consumer' ) . '</h4>
               </div>
               <div class="modal-body">
                 <p>' . plugin_lang_get( 'confirm_delete_consumer' ) . '
                 ' . $this->consumer_count_related_contact( $t_consumer[ 'id' ] ) .
                     $this->consumer_count_related_connection( $t_consumer[ 'id' ] ) . '</p>
                 <input type="hidden" class="hidden" id="parameters" name="parameters" value="consumer,' . 'delete' . ',' . $t_consumer[ 'id' ] . '" />
               </div>
               <div class="modal-footer">
                 <button formaction="' . plugin_page( 'config_page' ) . '" class="btn btn-default">'. plugin_lang_get( 'delete' ) . '</button>
                 <button type="button" class="btn btn-default" data-dismiss="modal">' . plugin_lang_get( 'cancel' ) . '</button>
              </form>
            </div>
          </div>
        </div>
      </div>

      <div id="ModalModifyConsumer' . $t_consumer[ 'id' ] . '" class="modal fade" role="dialog">
       <div class="modal-dialog">
        <div class="modal-content">
          <form action="" method="post">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title"><i class="ace-icon fa fa-pencil-square-o"></i>&nbsp;' . plugin_lang_get( 'modify_consumer' ) . '</h4>
          </div>
          <div class="modal-body">
            <h4 class="modal-title" style="color:#307ECC;">' . plugin_lang_get( 'name' ) . '</h4><br />
            <input type="text" id="name" name="name" size="50" maxlength="128" value="' . $t_consumer[ 'name' ] . '" required />
            <hr />
            <h4 class="modal-title" style="color:#307ECC;">' . plugin_lang_get( 'logo' ) . '</h4><br />
            <input type="text" id="logo" name="logo" size="50" maxlength="128" value="' . $t_consumer[ 'logo' ] . '" required />
            <input type="hidden" class="hidden" id="parameters" name="parameters" value="consumer,modify,' . $t_consumer[ 'id' ] . '" />
          </div>
          <div class="modal-footer">
            <button formaction="' . plugin_page( 'config_page' ) . '" class="w3-btn w3-green" style="text-decoration:none;">' . plugin_lang_get( 'save' ) . '</button>
            <a href="" class="w3-btn w3-red" data-dismiss="modal" style="text-decoration:none;">' . plugin_lang_get( 'close' ) . '</a>
          </div>
        </form>
        </div>

          </div>
        </div>';
    }

    $t_display .= '</tbody></table>';

    return $t_display;
  }

  # Fonction de suppression d'un client
  function consumer_delete_by_id( $c_id)
  {
    $t_consumer_table = plugin_table( "consumer", "wikiCylande" );

    $t_query = "DELETE FROM {$t_consumer_table} WHERE id=" . db_param();
    $t_result = db_query( $t_query, array( $c_id ) );

    return ( !is_null($t_result) ) ? true : NULL;

  }

  # Fonction d'affichage des ID_Clients en liste déroulante
  public static function print_all_consumers_ids_in_selectbox( $id = NULL )
  {
  $t_consumer_table = plugin_table( "consumer", "wikiCylande" );

  $query = "SELECT * FROM {$t_consumer_table} ORDER BY name ASC";

  $result = db_query( $query );

  $display = '<select id="id_client" name="id_client" class="autofocus form-control">
                  <option value="999999">' . plugin_lang_get( 'all' ) . '</option>';

  while( $t_consumers = db_fetch_array( $result ) )
  {
      if( !is_null( $id ) && $id === $t_consumers[ 'id' ] )
      {
      $display .= '<option value="' . $t_consumers[ 'id'] . '" selected>' . $t_consumers[ 'name' ] . '</option>';
      } else {
      $display .= '<option value="' . $t_consumers[ 'id'] . '">' . $t_consumers[ 'name' ] . '</option>';
      }
  }

  $display .= '</select>';

  return $display;
  }

  public static function get_consumer_name( $c_id )
  {
    $t_consumer_table = plugin_table( "consumer", "wikiCylande" );

    if( $c_id === '999999' )
    {
      return plugin_lang_get( 'all_consumer' );
    }
    else
    {
      $t_query = "SELECT name FROM {$t_consumer_table} WHERE id=" . db_param();
      $c_name = db_query( $t_query, array( $c_id ) );

      $c_name_array = db_fetch_array( $c_name );

      return $c_name_array[ 'name' ];
    }
  }

  public function consumer_get_sidebar_items_and_display_it( $parameter_icon )
  {
    $t_consumer_table = plugin_table( "consumer", "wikiCylande" );

    $t_query = "SELECT * FROM {$t_consumer_table} ORDER BY name ASC";
    $t_results = db_query( $t_query );

    $t_connection = new wikiConnection();

    $t_article = new wikiArticle();

    $t_contact = new wikiContact();

    $t_display = '<div class="widget-box widget-color-blue w3-card-2">
                  <div class="widget-header widget-header-small" >
                  <h4 class="widget-title lighter" style="color:#FFFFFF;">
                  <a data-toggle="collapse" href="#collapseConsumers" role="button" aria-expanded="false" aria-controls="collapseConsumers" style="text-decoration:none;color:#FFFFFF;">
                      <i class="ace icon fa ' . $parameter_icon . '"></i>
                      ' . plugin_lang_get( 'consumers' ) . '
                  </a>
                  </h4>
                  </div>
                  <div class="collapse" id="collapseConsumers">
                  <ul class="nav nav-list">';

    while( $t_consumers = db_fetch_array( $t_results ) )
    {
      $consumer_name = str_replace( ' ', '', $t_consumers[ 'name' ]);
      $consumer_name = str_replace( '(', '', $consumer_name);
      $consumer_name = str_replace( ')', '', $consumer_name);
      $consumer_name = str_replace( '\'', '', $consumer_name );

      $t_display .=  '
                      <li>
                      <a data-toggle="collapse" href="#collapse-Contact-' . $consumer_name . '" role="button" aria-expanded="false" aria-controls="collapse-Contact-' . $t_consumers[ 'name' ] . '" style="text-decoration:none;">
                      <i class="menu-icon fa fa-chevron-circle-right"></i>
                      <span class="sidebar-title">' . $t_consumers[ 'name' ] . '</span>
                      </a>
                      <b class="arrow"></b>
                      </li>
                      ';

      $t_display .= '<div class="collapse" id="collapse-Contact-' . $consumer_name . '">
                     <ul class="nav nav-list">';

      if( wikiUser::user_can_read_contacts())
      {
      $t_display .= ' <li>
                        <a href="' . plugin_page( 'view_contact' ) . '&amp;id_client=' . $t_consumers[ 'id' ] . '&amp;type=consumer' . '" style="text-decoration:none;">
                        <i class="menu-icon fa fa-book"></i>
                        <span class="menu-text">' . plugin_lang_get( 'show_contacts_consumer' ) . $t_contact->contact_count_by_consumer( $t_consumers[ 'id' ], 'consumer' ) .'</span>
                        </a>
                        <b class="arrow"></b>
                      </li>
                      <li>
                        <a href="' . plugin_page( 'view_contact' ) . '&amp;id_client=' . $t_consumers[ 'id' ] . '&amp;type=cylande' . '" style="text-decoration:none;">
                        <i class="menu-icon fa fa-book"></i>
                        <span class="menu-text">' . plugin_lang_get( 'show_contacts_cylande' ) . $t_contact->contact_count_by_consumer( $t_consumers[ 'id' ], 'cylande' ) .'</span>
                        </a>
                        <b class="arrow"></b>
                      </li>';
      }

      if( wikiUser::user_can_read_connections())
      {
        $t_display .= '<li>
                          <a href="' . plugin_page( 'view_connection' ) . '&amp;id_client=' . $t_consumers[ 'id' ] . '" style="text-decoration:none;">
                          <i class="menu-icon fa fa-link"></i>
                          <span class="menu-text">' . plugin_lang_get( 'show_connections' ) . $t_connection->connection_count_by_consumer( $t_consumers[ 'id' ] ) . '</span>
                          </a>
                          <b class="arrow"></b>
                        </li>';

      }

      if( wikiUser::user_can_read_articles())
      {
        $t_display .=  '<li>
                          <a href="' . plugin_page( 'wiki_view_all' ) . '&amp;t=id_client' . '&amp;id=' . $t_consumers[ 'id' ] . '" style="text-decoration:none;">
                          <i class="menu-icon fa fa-link"></i>
                          <span class="menu-text">' . plugin_lang_get( 'show_articles' ) . $t_article->article_count_by_type( 'id_client' ,$t_consumers[ 'id' ] ) . '</span>
                          </a>
                          <b class="arrow"></b>
                        </li>';
      }

        $t_display .= '</ul>
                      </div>';
    }

    $t_display .= '</ul>
                   </div>
                   </div>
                   ';

    return $t_display;

  }

}

?>
