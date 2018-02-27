<?php

  /**
  * Config API
  * @package wikiCylande/api
  * */

class wikiArticleSetting
{
  # int
  public $id;
  # string C(100)
  public $type;
  # string C(100)
  public $value;
  # string C(100)
  public $color;
  # Integer (I)
  public $id_parameter;

  # Constructeur
  function __construct( $type="", $value="", $color="", $id_parameter=0 )
  {
    $this->type = $type;
    $this->value = $value;
    $this->color = $color;
    $this->id_parameter = $id_parameter;
  }

  public static function get_parameter_table( $parameter_type )
  {
    return plugin_table( $parameter_type, "wikiCylande" );
  }

  # Fonction de sauvegarde en base de données
  public function save()
  {

      $t_setting_table = plugin_table( $this->type, "wikiCylande" );

      $t_query = "INSERT INTO {$t_setting_table}
                    (
                      " . $this->type . "_name,
                      " . $this->type . "_color,
                      id_parameter
                    ) VALUES (
                      " . db_param() . ",
                      " . db_param() . ",
                      " . db_param() . "
                      )";

        db_query( $t_query, array(
          $this->value,
          $this->color,
          $this->id_parameter
        ) );

        $this->id = db_insert_id( $t_setting_table );

        return ( !empty($this->id) ) ? $this->id : NULL;
  }

  # Fonction 'explode' pour la récupération des paramètre sur config_page
  public static function config_explode_parameters( $parameters )
  {
    return explode(',', $parameters );
  }

  public static function print_successful_message( $result, $type )
  {
    switch( $result )
    {
      case 'created':
      case 'modified':
      case 'deleted':
      case 'shared':
      case 'unshared':
      $message = $result . '_';
      $class = 'alert-success';
      break;

      case 'error_creation':
      case 'error_modification':
      case 'error_deletion':
      case 'error_sharing':
      case 'error_unsharing':
      $message = $result . '_';
      $class ='alert-warning';
      break;

    }

    $t_message = '<div class="col-md-12 col-xs-12 no-padding">
                      <div class="alert ' . $class . ' w3-animate-opacity">
                        <strong><i class="ace-icon fa fa-info-circle"></i>
                        ' . plugin_lang_get( $message . 'title_success' ) . '</strong>&nbsp;:&nbsp;' .
                        plugin_lang_get( $message . $type ) . '
                      </div>
                    </div>';

    return $t_message;
  }

  # Fonction de mise à jour de la base de données
  /** @param $parameter_id >>> ID du paramètre
    **/


  public function update( $parameter_id )
  {
    $parameter_type = $this->type;

    $t_setting_table = plugin_table( $parameter_type, "wikiCylande" );

    $t_query = "UPDATE {$t_setting_table} SET
    " . $parameter_type . "_name=" . db_param() . ",
    " . $parameter_type . "_color=" . db_param() . ",
    id_parameter=" . db_param() . "
    WHERE id=" . db_param();

    $t_result = db_query( $t_query, array(
      $this->value,
      $this->color,
      $this->id_parameter,
      $parameter_id,
    ) );

      return ( !empty($t_result) ) ? $parameter_id : NULL;
  }

  # Fonction qui permet de d'afficher les catégories existantes sous forme de tableau
  /** @param $parameter_type >>> 'product', 'module', 'type'
    **/

  function get_categories_and_display_it( $parameter_type )
  {
    $t_category_table = plugin_table( $parameter_type, "wikiCylande" );

    $t_query = "SELECT * FROM {$t_category_table} ORDER BY id ASC";
    $t_results = db_query( $t_query );

    $t_article = new wikiArticle();

    $t_display = '
    <table>
    <tr>
    <td class="w3-gray" width="10%" style="text-align:center;">
    <span class="w3-text-white"><i class="ace-icon fa fa-search">&nbsp;</i></span>
    </td>
    <td width="90%">
    <input class="form-control w3-card-2" style="width:100%;" size="100%" id="myInput' . $parameter_type . '" type="text" placeholder="' . plugin_lang_get( 'search_' . $parameter_type ) . '"/>
    </td>
    </tr>
    </table>
    <table class="w3-table-all container w3-hoverable ">
    <thead>
    <tr>
      <th>' . plugin_lang_get( 'name' ) . '</th>
      <th style="text-align: center;">' . plugin_lang_get( 'color' ) . '</th>
      <th style="text-align:center;">
      <i class="ace-icon fa fa-pencil"></i>
      </th>
      <th style="text-align:center;">
      <i class="ace-icon fa fa-trash"></i>
      </th>
    </tr>
    </thead>
    <tbody id="myTable' . $parameter_type . '">';

    switch( $parameter_type )
    {
      case 'product':
      $parameter_target = 'product';
      break;
      case 'module':
      $parameter_target = 'product';
      break;
      case 'type':
      $parameter_target = 'module';
      break;
    }

    while( $t_category = db_fetch_array( $t_results ) )
    {
      $name = $t_category[ $parameter_type . '_name'];
      $color = $t_category[ $parameter_type . '_color' ];

      $t_display .= '<tr>
                      <td>' . $name . '
                      ' . self::display_parameter_relation( $parameter_type, $t_category[ 'id_parameter' ] ) . '
                      ' . $t_article->count_related_articles( $parameter_type, $t_category[ 'id' ] ) . '</td>
                      <td style="color:#FFFFFF;background-color:' . $color . ';"></td>
                      <td style="text-align:center;vertical-align: middle;">
                      <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#Modal' . $parameter_type . $t_category[ 'id' ] . '">OK</button>
                      </td>
                      <td style="text-align:center;vertical-align: middle;">
                      <button type="button" class="btn w3-hover-red btn-sm align-middle" data-toggle="modal" data-target="#ModalDelete--' . $parameter_type . $t_category[ 'id' ] . '">OK</button>
                      </td>
                    </tr>
            <div id="ModalDelete--' . $parameter_type . $t_category[ 'id' ] . '" class="modal w3-animate-zoom" role="dialog">
              <div class="modal-dialog modal-md">
                 <div class="modal-content">
                   <form action="" method="post">
                     <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                          <h4 class="modal-title"><i class="ace-icon fa fa-cogs"></i>&nbsp;' . plugin_lang_get( 'delete_' . $parameter_type ) . '</h4>
                     </div>
                     <div class="modal-body">
                       <p>' . plugin_lang_get( 'confirm_delete_' . $parameter_type ) . '</p><br />
                       ' . $t_article->display_related_articles_count( $parameter_type, $t_category[ 'id' ] ) . '
                       <input type="hidden" class="hidden" id="parameters" name="parameters" value="' . $parameter_type . ',' . 'delete' . ',' . $t_category[ 'id' ] . '" />
                     </div>
                     <div class="modal-footer">
                       <button formaction="' . plugin_page( 'config_page' ) . '" class="btn btn-default">'. plugin_lang_get( 'delete' ) . '</button>
                       <button type="button" class="btn btn-default" data-dismiss="modal">' . plugin_lang_get( 'cancel' ) . '</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>

        <div id="Modal'. $parameter_type . $t_category[ 'id' ] . '" class="modal w3-animate-zoom" role="dialog">
          <div class="modal-dialog">
            <div class="modal-content">
              <form action="" method="post">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="ace-icon fa fa-pencil-square-o"></i>&nbsp;' . plugin_lang_get( 'modify_' . $parameter_type ) . '</h4>
              </div>
              <div class="modal-body">
                <h4 class="modal-title" style="color:#307ECC;">' . plugin_lang_get( 'name' ) . '</h4><br />
                <input type="text" id="name" name="name" size="50" maxlength="128" value="' . $name . '" required />
                <hr />';

                if( $parameter_type != 'product')
                {
    $t_display .= '<h4 class="modal-title" style="color:#307ECC;">' . plugin_lang_get( 'associated_to' ) . '</h4><br />
                  ' . self::config_print_all_parameters_in_selectbox( $parameter_target, $t_category[ 'id_parameter' ]) . '
                  <hr />';
                }

    $t_display .= '<h4 class="modal-title" style="color:#307ECC;">' . plugin_lang_get( 'color' ) . '</h4><br />
                <input  class="jscolor" type="text" id="color" name="color" size="50" maxlength="128" value="' . $color . '" required />
                <input type="hidden" class="hidden" id="parameters" name="parameters" value="' . $parameter_type . ',' . 'modify' . ',' . $t_category[ 'id' ] . '"
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

  public static function get_parameter_id_by_name( $parameter_type, $name )
  {
    $setting_table = plugin_table( $parameter_type, "wikiCylande" );

    $query = "SELECT id FROM {$setting_table} WHERE " . $parameter_type . "_name=" . db_param();
    $result = db_query( $query, array( $name ) );
    $data = db_fetch_array( $result );

    $parameter_id = ( !empty($data[ 'id ']) ) ? $data[ 'id' ] : NULL;

    return $parameter_id;
  }
  # Fonction qui permet de récupérer le nom d'une catégorie en transmettant son
  # type et son ID
  /** @param $parameter_type >>> 'product', 'module', 'type'
    * @param $parameter_id >>> ID du paramètre
    **/

  public static function get_parameter_by_id ( $parameter_type, $parameter_id )
  {
    $t_setting_table = plugin_table( $parameter_type, "wikiCylande" );

    if( $parameter_id === '0' )
    {
      return plugin_lang_get( 'all_' . $parameter_type );
    }
    else
    {
    $t_query = "SELECT * FROM {$t_setting_table} WHERE id=" . db_param();
    $t_result = db_query( $t_query, array( $parameter_id ) );

    $t_parameter = db_fetch_array( $t_result );

    return $t_parameter[ $parameter_type . '_name' ];
    }
  }

  # Fonction qui permet de récupérer la couleur d'une catégorie en transmettant son
  # type et son ID
  /** @param $parameter_type >>> 'product', 'module', 'type'
    * @param $parameter_id >>> ID du paramètre
    **/

  public static function get_color_parameter_by_id ( $parameter_type, $parameter_id )
  {
    $t_setting_table = plugin_table( $parameter_type, "wikiCylande" );

    $t_query = "SELECT * FROM {$t_setting_table} WHERE id=" . db_param();
    $t_result = db_query( $t_query, array( $parameter_id ) );

    $t_parameter_color = db_fetch_array( $t_result );

    return $t_parameter_color[ $parameter_type . '_color' ];
  }

  # Fonction d'affichage des sous-catégories dans la sidebar
  # Menu déroulant (collapse Bootstrap)
  /** @param $parameter_type >>> 'product', 'module', 'type'
    * @param $parameter_id >>> ID du paramètre
    * @param $parameter_target >>> 'product', 'module', 'type'
    **/

  public static function config_get_sidebar_subclasses_and_display_it( $parameter_type, $parameter_target, $parameter_id )
  {
    $t_wiki_table = plugin_table ( "article", "wikiCylande" );

    $t_query = "SELECT * FROM {$t_wiki_table} WHERE " . $parameter_type . "=" . $parameter_id;
    $t_results = db_query( $t_query );

    $name = str_replace( ' ', '',  self::get_parameter_by_id( $parameter_type, $parameter_id ) );

    $t_display = '<div id="collapse-' . $parameter_type . $parameter_target . '-' . $name . '" class="collapse" role="tabpanel" data-parent="accordion-' . $parameter_type . '">
                  <ul class="nav nav-list">';

    $ids = array();

    while( $t_items = db_fetch_array( $t_results ) )
    {
      # Pour éviter les doublons
      if( in_array($t_items[ $parameter_target ], $ids) )
      {
        # Ne rien faire

      } else {

      $ids[] = $t_items[ $parameter_target ];

      $t_display .= '
                      <li>
                        <a href="' . plugin_page( 'wiki_view_all' ) . '&amp;t=' . $parameter_type . '&amp;id=' . $t_items[ $parameter_type ] . '&amp;sub_type=' . $parameter_target . '&amp;sub_id=' . $t_items[ $parameter_target ] . '">
                        <i class="menu-icon fa fa-chevron-right"></i>
                        <span class="menu-text">'
                          . self::get_parameter_by_id( $parameter_target, $t_items[ $parameter_target ] )
                          . wikiArticle::article_count_by_type_and_target( $parameter_type, $t_items[ $parameter_type ], $parameter_target, $t_items[ $parameter_target ] ) . '
                        </span>
                        </a>
                      </li>
                      ';

      }
    }

    $t_display .= '<li>
                    <a href="' . plugin_page( 'wiki_view_all' ) . '&amp;t=' . $parameter_type . '&amp;id=' . $parameter_id . '">
                    <i class="menu-icon fa fa-inbox"></i>
                    <span class="menu-text">' . plugin_lang_get( 'view_all_articles' ) . '</span>
                    </a>
                    <b class="arrow"></b>
                   </li>
                   </ul></div>';

    return $t_display;
  }


  # Fonction d'affichage des catégories dans la sidebar
  # Menu déroulant (collapse Bootstrap)
  /** @param $parameter_type >>> 'product', 'module', 'type'
    * @param $parameter_icon >>> Icone (font-awesome)
    **/

  public static function config_get_sidebar_items_and_display_it( $parameter_type, $parameter_icon )
  {
    $t_setting_table = plugin_table( $parameter_type, "wikiCylande" );

    switch( $parameter_type )
    {
      case 'product':
      $parameter_target = 'module';
      break;

      case 'module':
      $parameter_target = 'type';
      break;

      case 'type':
      $parameter_target = 'product';
      break;
    }

    $t_query = "SELECT * FROM {$t_setting_table} ORDER BY " . $parameter_type . "_name ASC";
    $t_result = db_query( $t_query );

    $t_article = new wikiArticle();

    $t_display = '<div class="widget-box widget-color-blue w3-card-2" id="accordion-' . $parameter_type . '">
                  <div class="widget-header widget-header-small" >
                  <h4 class="widget-title lighter" style="color:#FFFFFF;">
                  <a data-toggle="collapse" href="#collapse' . $parameter_type . '" role="button" aria-expanded="false" aria-controls="collapse' . $parameter_type . '" style="text-decoration:none;color:#FFFFFF;">
                      <i class="ace icon fa ' . $parameter_icon . '"></i>
                      ' . plugin_lang_get( $parameter_type ) . '
                  </a>
                  </h4>
                  </div>
                  <div class="collapse" id="collapse' . $parameter_type .'">
                  <ul class="nav nav-list">';

    while( $t_settings = db_fetch_array( $t_result ) )
    {
      if( wikiArticle::article_count( $parameter_type, $t_settings[ 'id' ]) > 0 ){
      $name = str_replace( ' ', '', self::get_parameter_by_id( $parameter_type, $t_settings[ 'id' ] ) );
      $t_display .=  '
                      <li role="tabpanel">
                      <a data-toggle="collapse" href="#collapse-' . $parameter_type . $parameter_target . '-' . $name . '" role="button" aria-expanded="false" aria-controls="collapse-' . $parameter_type . $parameter_target . '" style="text-decoration:none;">
                      <i class="menu-icon fa fa-chevron-circle-right"></i>
                      <span class="sidebar-title">' . $t_settings[ $parameter_type . '_name' ] . '</span>' . $t_article->article_count_by_type( $parameter_type, $t_settings[ 'id' ] ) . '
                      </a>
                      <b class="arrow"></b>
                      </li>
                      ';

      $t_display .= self::config_get_sidebar_subclasses_and_display_it( $parameter_type, $parameter_target, $t_settings[ 'id' ] );
    }
    }

    $t_display .= '
                   </ul>
                   </div>
                   </div>';

    return $t_display;

  }

  # Fonction de suppression des catégories
  /** @param $parameter_type >>> 'product', 'module', 'type'
    * @param $parameter_id >>> ID du paramètre
    **/

  function config_delete_parameter_by_id( $parameter_type, $parameter_id )
  {
    $t_setting_table = plugin_table( $parameter_type, "wikiCylande" );

    $t_query = "DELETE FROM {$t_setting_table} WHERE id=" . db_param();
    $t_result = db_query( $t_query, array( $parameter_id ) );

    return ( !empty( $t_result ) ) ? true : NULL;
  }

  # Fonction d'affichage des catégorie dans une liste déroulante 'select'
  # Page de configuration & page de création / modification d'article
  /** @param $parameter_type >>> 'product', 'module', 'type'
    * @param $parameter_id >>> ID du paramètre
    * @param $parameter_target >>> 'product', 'module', 'type'
    **/

  public static function config_print_all_parameters_in_selectbox( $parameter_type, $parameter_id )
  {
    $t_setting_table = plugin_table( $parameter_type, "wikiCylande" );

    $t_query = "SELECT * FROM {$t_setting_table} ORDER BY " . $parameter_type . "_name ASC";

    $t_results = db_query( $t_query );

    if( $parameter_type === 'product' )
    {
      $t_display = '<select id="' . $parameter_type . '" name="' . $parameter_type . '" class="autofocus form-control" onchange="">
                    <option value="0">' . plugin_lang_get( 'all' ) . '</option>';
    } elseif( $parameter_type === 'module' ){
      $t_display = '<select id="' . $parameter_type . '" name="' . $parameter_type . '" class="autofocus form-control" onchange="">
                    <option value="0">' . plugin_lang_get( 'all' ) . '</option>';
    } else {
    $t_display = '<select id="' . $parameter_type . '" name="' . $parameter_type . '" class="autofocus form-control">
                  <option value="0">' . plugin_lang_get( 'all' ) . '</option>';
    }
    while( $t_settings = db_fetch_array( $t_results ) )
    {
      if( $parameter_id != null){
        if( $t_settings[ 'id' ] === $parameter_id ){
            $t_display .= '<option value="' . $t_settings[ 'id'] . '" selected>' . $t_settings[ $parameter_type . '_name' ] . '</option>';
        } else {
          $t_display .= '<option value="' . $t_settings[ 'id'] . '">' . $t_settings[ $parameter_type . '_name' ] . '</option>';
        }
      } else {
        $t_display .= '<option value="' . $t_settings[ 'id'] . '">' . $t_settings[ $parameter_type . '_name' ] . '</option>';
      }
    }

    $t_display .= '</select>';

    return $t_display;
  }

  public static function get_associated_parameters( $id, $parameter_type )
  {

    if( !is_null($id) )
    {
      $parameter_table = self::get_parameter_table( $parameter_type );

      $query = "SELECT * FROM {$parameter_table} WHERE id_parameter=" . db_param() . " ORDER BY " . $parameter_type . "_name ASC";
      $result = db_query( $query, array( $id ) );

      $display = '';

      while( $data = db_fetch_array( $result ) )
      {
        $display.= '<option value="' . $data['id'] . '">' . $data[ $parameter_type . '_name' ] . '</option>';
      }

      return $display;

    } else {

      return '<option value="0"></option>';
    }

  }

  public static function display_parameter_relation( $parameter_type, $parameter_id )
  {
    if( $parameter_type != 'product' )
      {

        switch( $parameter_type )
          {
            case 'product':
            $parameter_target = 'product';
            break;
            case 'module':
            $parameter_target = 'product';
            break;
            case 'type':
            $parameter_target = 'module';
            break;
          }

        $parameter_table = self::get_parameter_table( $parameter_target );

        $query = "SELECT * FROM {$parameter_table} WHERE id=" . db_param();
        $result = db_query( $query, array( $parameter_id ) );
        $data = db_fetch_array( $result );

        $display = '<p>(
                      ' . $data[ $parameter_target . '_name' ] . '
                    )</p>';

        return $display;

        }
  }

    /** Get the plugin name, if no settings have been set, return default
      * plugin name (see lang files)
      * @return plugin_name
      **/

  public static function config_get_plugin_name()
  {
      $setting_table = self::get_parameter_table( 'setting' );

      $query = "SELECT count(*) FROM {$setting_table}";
      $result = db_query( $query );
      $count = db_fetch_array( $result );

      $plugin_name = $count[ 'count(*)' ] > 0 ? self::config_get_setting_value( 'plugin_name' ) : plugin_lang_get( 'plugin_title' );

      return $plugin_name;
  }

  public static function config_get_setting_value( $column_name )
  {
      $setting_table = self::get_parameter_table( 'setting' );

      $query = "SELECT {$column_name} FROM {$setting_table}";
      $result = db_query( $query );
      $setting_value = db_fetch_array( $result );

      return $setting_value[ $column_name ];
  }


}

 ?>
