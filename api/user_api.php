<?php

class wikiUser
{
  /**
    * Propriété pour les utilisateurs
    * Table = "user"
    **/

  # ID de l'utilisateur enregistré dans le wiki
  protected $id;
  # ID de l'utilisateur dans Mantis
  protected $user_id;
  # Groupe de l'utilisateur
  protected $user_group;
  # Privilège de l'utilisateur dans le wiki ( 0 = non ; 1 = oui )
  protected $user_is_admin;
  # Droits d'accès (lecture/écriture) articles
  protected $user_article_rights;
  # Droits d'accès (lecture/écriture) connexions
  protected $user_connection_rights;
  # Droits d'accès (lecture/écriture) contacts
  protected $user_contact_rights;

  /**
    * Propriété pour les groupes d'utilisateurs
    * table "group"
    **/

  # ID du groupe d'utilisateurs
  protected $group_id;
  # Nom du groupe d'utilisateurs
  protected $group_name;

  public function __construct( $user_id=0, $user_group=0, $user_is_admin=0, $user_article_rights=0, $user_connection_rights=0, $user_contact_rights=0 ){
    $this->user_id = $user_id;
    $this->user_group = $user_group;
    $this->user_is_admin = $user_is_admin;
    $this->user_article_rights = $user_article_rights;
    $this->user_connection_rights = $user_connection_rights;
    $this->user_contact_rights = $user_contact_rights;
  }

  # Récupération du nom formaté de la table Utilisateur
  public static function get_user_table()
  {
    return plugin_table( "user", "wikiCylande" );
  }

  # Récupération du nom formaté de la table Groupe
  public static function get_group_table()
  {
    return plugin_table( "group", "wikiCylande" );
  }

  # Get plugin user_pref table name
  public static function get_user_pref_table()
  {
    return plugin_table( "user_pref", "WikiCylande" );
  }

  public function save_user()
  {
    $user_table = self::get_user_table();

    if( !is_null( $this->user_id ) && !is_null( $this->user_group ) && !is_null( $this->user_is_admin ) )
    {
      if ( !self::user_is_registered( $this->user_id ) ){
      $t_query = "INSERT INTO {$user_table}
                    (
                      user_id,
                      user_group,
                      user_is_admin,
                      user_article_rights,
                      user_connection_rights,
                      user_contact_rights
                    ) VALUES (
                      ". db_param() .",
                      ". db_param() .",
                      ". db_param() .",
                      ". db_param() .",
                      ". db_param() .",
                      ". db_param() ."
                    )";

      db_query( $t_query, array(
        $this->user_id,
        $this->user_group,
        $this->user_is_admin,
        $this->user_article_rights,
        $this->user_connection_rights,
        $this->user_contact_rights
      ) );

      $this->id = db_insert_id( $user_table );

    } else {

      $this->id = NULL;

    }

    }


    return $this->id;
  }


  public static function save_group( $group_name )
  {
    $group_table = self::get_group_table();

    if( !empty( $group_name ) )
    {
      $query = "INSERT INTO {$group_table}
                (
                  group_name
                ) VALUES (
                  " . db_param() . "
                )";

    db_query( $query, array( $group_name ) );

    $group_id = db_insert_id( $group_table );

    return $group_id;

    } else {

      return NULL;

    }
  }

  public static function update_group( $group_name, $group_id )
  {
    $group_table = self::get_group_table();

    if( !empty( $group_id ) ){

      $t_query = "UPDATE {$group_table} SET
                  group_name=" . db_param() ."
                  WHERE id=" . db_param();

      db_query( $t_query, array( $group_name, $group_id ) );

      return $group_id;

    } else {

      return NULL;

    }
  }

  public function update_user( $id )
  {
    $user_table = self::get_user_table();

    $this->id = $id;

    if( !is_null( $this->id ) && !is_null( $this->user_id ) && !is_null( $this->user_group ) && !is_null( $this->user_is_admin ) )
    {
      $t_query = "UPDATE {$user_table} SET
                      user_id=" . db_param() .",
                      user_group=" . db_param() .",
                      user_is_admin=" . db_param() .",
                      user_article_rights=" . db_param() .",
                      user_connection_rights=" . db_param() .",
                      user_contact_rights=" . db_param() ."
                      WHERE id=". db_param();

      db_query( $t_query, array(
        $this->user_id,
        $this->user_group,
        $this->user_is_admin,
        $this->user_article_rights,
        $this->user_connection_rights,
        $this->user_contact_rights,
        $this->id
      ) );

    } else {

      $this->id = NULL;

    }

    return $this->id;
  }

    public static function group_delete_by_id( $id )
    {
      $group_table = self::get_group_table();

      if( !empty( $id ) )
      {
        $t_query = "DELETE FROM {$group_table} WHERE id=" . db_param();
        db_query( $t_query, array( $id ) );
      }

      return $id;
    }

    public static function user_delete_by_id( $id )
    {
      $user_table = self::get_user_table();

      if( !empty( $id ) )
      {
        $t_query = "DELETE FROM {$user_table} WHERE id=" . db_param();
        db_query( $t_query, array( $id ) );
      }

      return $id;
    }

  public static function user_is_allowed( $user_id )
  {
    $user_table = self::get_user_table();

    if( !empty( $user_id ) ){

      $query = "SELECT count(*) FROM {$suser_table} WHERE user_id=" . db_param();
      $result = db_query( $query, array( $user_id ) );
      $data = db_fetch_array( $result );

      $is_allowed = ( !empty( $data[ 'count(*)' ] ) ) ? true : false;

    } else {

      $is_allowed = false;

    }

    return $is_allowed;
  }

  # Vérifie si l'utilisateur est l'auteur de l'article
  # Retourne 'true' si l'utilisateur est l'auteur de l'article
  # l'id de l'utilisateur est optionnel, s'il n'est pas transmis l'id de l'utilisateur de session est récupéré
  public static function user_is_author( $article_id, $user_id = NULL )
  {
    $article_table = wikiArticle::get_article_table();

    if ( is_null( $user_id ) )
      {
        $user_id = auth_get_current_user_id();
      }

    $query = "SELECT author_id FROM {$article_table} WHERE id=" . db_param() . " AND author_id=" . db_param();
    $result = db_query( $query, array( $article_id, $user_id ) );
    $data = db_fetch_array( $result );

    return ( (int)$data[ 'author_id' ] === $user_id ) ? true : false;
  }

  # Vérifier si l'utilisateur est co-auteur d'au moins un article (affichage wiki_article_box)
  # ou d'un article en particulier ( $article_id )
  public static function user_is_coauthor( $current_user_id = NULL, $article_id = NULL )
  {
    $article_table = wikiArticle::get_article_table();

    if( is_null( $current_user_id ) )
      {
        $current_user_id = auth_get_current_user_id();
      }

    $user_id = '#' . $current_user_id . ',';

    if( !is_null( $article_id ) )
      {
        $query = "SELECT count(*) FROM {$article_table} WHERE coauthor_list LIKE '%" . $user_id . "%' AND id=" . db_param();
        $result = db_query( $query, array( $article_id ) );
      }
    else
      {
        $query = "SELECT count(*) FROM {$article_table} WHERE coauthor_list LIKE '%" . $user_id . "%'";
        $result = db_query( $query );
      }

    $data = db_fetch_array( $result );

    return $data[ 'count(*)' ] > 0 ? true : false;
  }

  # Vérifier si l'utilisateur peut modifier un article
  # - S'il est admnistrateur
  # - S'il est autheur de l'article
  # - S'il est co-autheur de l'article
  public static function user_can_modify_article( $article_id )
  {

    if( self::user_is_admin() || self::user_is_author( $article_id ) || self::user_is_article_coauthor( $article_id ) )
      {
        return true;
      }
    else
      {
        return false;
      }
  }

  # Vérifie si l'utilisateur est co-auteur de l'article
  # Retourne 'true' si l'utilisateur est co-auteur
  public static function user_is_article_coauthor( $article_id, $user_id = NULL )
  {
    $article_table = wikiArticle::get_article_table();

    if( is_null( $user_id ) )
      {
        $user_id = auth_get_current_user_id();
      }

    $query = "SELECT coauthor_list FROM {$article_table} WHERE id=" . db_param();
    $result = db_query( $query, array( $article_id ) );
    $data = db_fetch_array( $result );

    $replace_hashtag = str_replace( '#', '', $data[ 'coauthor_list' ] );
    $coauthor_list = explode( ',', $replace_hashtag );


    return in_array( $user_id, $coauthor_list );
  }

  public static function user_is_admin( $user_id = NULL )
  {
    $user_table = self::get_user_table();

    if( is_null( $user_id ) )
      {
        $user_id = auth_get_current_user_id();
      }

    if( !empty( $user_id ) )
      {
        $query = "SELECT user_is_admin FROM {$user_table} WHERE user_id=" . db_param();
        $result = db_query( $query, array( $user_id ) );
        $data = db_fetch_array( $result );

        $is_admin = ( ((int)$data[ 'user_is_admin' ]) === 1 ) ? true : false;
      }
    else
      {
        $is_admin = false;
      }

    return $is_admin;
  }

  # Récupère le nom du groupe auquel est rattaché un utilisateur après transmission de l'id utilisateur
  # Retoure NULL si l'id de group est vide
  public static function user_get_group( $user_id )
  {
    $user_table = self::get_user_table();

    if( !empty( $user_id ) )
    {
      $query = "SELECT * FROM {$user_table} WHERE user_id=" . db_param();
      $result = db_query( $query( $user_id ) );
      $data = db_fetch_array( $result );

      $group = self::group_get_name( $data[ 'user_group' ] );

    } else {

      $group = NULL;

    }

    return $group;
  }

  # Récuère le nom du groupe de l'uilisateur en fonction de l'id transmit
  public static function group_get_name( $group_id )
  {
    $group_table = self::get_group_table();

    if( !empty( $group_id ) )
      {
        $query = "SELECT * FROM {$group_table} WHERE id=" . db_param();
        $result = db_query( $query, array( $group_id ) );
        $data = db_fetch_array( $result );

        $group_name = $data[ 'group_name' ];
      }
    else
      {
        $group_name = NULL;
      }

    return $group_name;
  }

  public static function print_registered_users_by_group( $group_id )
  {
    $user_table = self::get_user_table();

    $query = "SELECT * FROM {$user_table} WHERE user_group=" . db_param() . " ORDER BY user_is_admin DESC";
    $result = db_query( $query, array( $group_id ) );

    $display = '<div class="col-md-12 col-xs-12">';

    while( $data = db_fetch_array( $result ) )
      {
        $icon_user = ( self::user_is_admin( $data[ 'user_id' ] ) ) ? '<i class="fa fa-star"></i>' : '<i class="fa fa-user"></i>';

        $display .= '<div class="w3-panel w3-white w3-card-2 w3-padding w3-display-container">
                      <h5 class="wiki-color text-left">
                      ' . $icon_user . '&nbsp;
                      ' . user_get_name( $data[ 'user_id' ] ) . '
                      </h5>
                      <h5 class="text-right">
                        <form action="" method="post">
                        <input type="hidden" id="parameters" name="parameters" value="user,delete,' . $data[ 'id' ] . '"/>
                        <button type="submit" formaction="' . plugin_page( 'config_page' ) . '" class="btn w3-hover-red w3-round-small">' . plugin_lang_get( 'put_off' ) . '</button>
                        </form>
                      </h5>
                     </div>';
      }

    $display .='</div>';

    return $display;
  }

  public static function print_user_group_in_selectbox( $article_id )
  {
    $user_id = auth_get_current_user_id();

    $user_table = self::get_user_table();

    $query = "SELECT user_group FROM {$user_table} WHERE user_id=" . db_param();
    $result = db_query( $query, array( $user_id ) );
    $data = db_fetch_array( $result );

    $user_group = $data[ 'user_group' ];

    $query = "SELECT * FROM {$user_table} WHERE user_group=" . db_param();
    $result = db_query( $query, array( $user_group ) );

    $display = '<select name="username" id="username" class="autofocus form-control">';

    while( $data = db_fetch_array( $result ) )
      {
          # Si l'utilisatur n'est déjà coauteur de l'article et s'il n'est pas l'auteur
            if( $data[ 'user_id' ] != $user_id && wikiUser::user_is_coauthor( $data[ 'user_id' ], $article_id ) === false )
              {
                $display .= '<option value="#' . $data[ 'user_id' ] . ',">
                            ' . user_get_name( $data[ 'user_id' ] ) . '
                            </option>';
              }
      }

    $display .= '</select>';

    return $display;
  }

  public static function print_groups_in_table()
  {
    $group_table = self::get_group_table();

    $t_query = "SELECT * FROM {$group_table} ORDER BY group_name ASC";
    $t_result = db_query( $t_query );

    $t_display = '  <table>
                      <tr>
                        <td class="w3-gray" width="25%" style="text-align:center;">
                          <span class="w3-text-white"><i class="ace-icon fa fa-search">&nbsp;</i></span>
                        </td>
                        <td width="75%">
                          <input class="form-control w3-card-2" style="width:100%;" size="100%" id="myInputGroup" type="text" placeholder="' . plugin_lang_get( 'search_user' ) . '"/>
                        </td>
                      </tr>
                    </table>
                    <table class="w3-table-all w3-hoverable" style="overflow-y: scroll !important; max-height: 400px !important;">
                      <thead>
                        <tr>
                           <th>
                              ' . plugin_lang_get( 'id' ) . '
                           </th>
                           <th>
                           ' . plugin_lang_get( 'name' ) . '
                           </th>
                           <th class="w3-center">
                              <i class="ace-icon fa fa-list"></i>
                           </th>
                           <th class="w3-center">
                              <i class="ace-icon fa fa-pencil"></i>
                           </th>
                           <th class="w3-center">
                              <i class="ace-icon fa fa-trash"></i>
                           </th>
                        </tr>
                      </thead>
                      <tbody id="myTableGroup">';

      while( $t_data = db_fetch_array( $t_result ) )
      {
        $t_display .= '<tr>
                          <td>
                            ' . $t_data[ 'id' ] . '
                          </td>
                          <td>
                            ' . $t_data[ 'group_name' ] . '
                          </td>
                          <td class="w3-center">
                            <button type="button" data-toggle="modal" data-target="#ModalAddUser-' . $t_data['id'] . '" class="btn btn-sm btn-info">' . plugin_lang_get( 'see_users' ) . '</button>
                          </td>
                          <td class="w3-center">
                            <button type="button" data-toggle="modal" data-target="#ModalModifyGroup-' . $t_data[ 'id' ] . '" class="btn btn-sm btn-info">OK</button>
                          </td>
                          <td class="w3-center">
                            <button type="button" data-toggle="modal" data-target="#ModalDeleteGroup-' . $t_data[ 'id' ] . '" class="btn btn-sm w3-hover-red">OK</button>
                          </td>
                       </tr>
                      ';

        $t_display .= '<div id="ModalModifyGroup-' . $t_data[ 'id' ] . '" class="modal w3-animate-zoom" role="dialog">
                          <div class="modal-dialog">
                            <div class="modal-content">
                              <form action="" method="post">
                              <div class="modal-header" style="background-color:#307ECC;">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title" style="background-color:#307ECC;color:#FFFFFF;"><i class="ace-icon fa fa-plus-square"></i>&nbsp;' . plugin_lang_get( 'modify_group' ) . '</h4>
                              </div>
                              <div class="modal-body">
                              <h4 class="modal-title">' . plugin_lang_get( 'name' ) . '</h4><br />
                                <input type="text" id="name" name="name" size="50" maxlength="99" value="' . $t_data[ 'group_name' ] . '" placeholder="' . plugin_lang_get( 'placeholder_group_name' ) . '"/>
                                <hr />
                                <input type="hidden" class="hidden" id="parameters" name="parameters" value="group,modify,' . $t_data[ 'id' ] . '" />
                              </div>
                              <div class="modal-footer">
                                <button formaction="' . plugin_page( 'config_page' ) . '" class="w3-btn w3-green w3-round-small" style="text-decoration:none;">' . plugin_lang_get( 'save' ) . '</button>
                                <a href="" class="w3-btn w3-red w3-round-small" data-dismiss="modal" style="text-decoration:none;">' . plugin_lang_get( 'close' ) . '</a>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>';

        $t_display .= '<div id="ModalDeleteGroup-' . $t_data[ 'id' ] . '" class="modal w3-animate-zoom" role="dialog">
                          <div class="modal-dialog modal-md">
                             <div class="modal-content">
                               <form action="" method="post">
                                 <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                      <h4 class="modal-title"><i class="ace-icon fa fa-cogs"></i>&nbsp;' . plugin_lang_get( 'delete_group' ) . '</h4>
                                 </div>
                                 <div class="modal-body">
                                   <p>' . plugin_lang_get( 'confirm_delete_group' ) . '</p>
                                   <input type="hidden" class="hidden" id="parameters" name="parameters" value="group,delete,' . $t_data[ 'id' ] . '" />
                                 </div>
                                 <div class="modal-footer">
                                   <button formaction="' . plugin_page( 'config_page' ) . '" class="btn btn-default">' . plugin_lang_get( 'delete' ) . '</button>
                                   <button type="button" class="btn btn-default" data-dismiss="modal">' . plugin_lang_get( 'cancel' ) . '</button>
                                 </div>
                              </form>
                          </div>
                        </div>
                    </div>';

        $t_display .= '<div id="ModalAddUser-' . $t_data[ 'id' ] . '" class="modal" role="dialog">
                        <div class="modal-dialog modal-lg">
                          <div class="modal-content">
                              <form action="" method="post">
                              <div class="modal-header" style="background-color:#307ECC;">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title" style="background-color:#307ECC;color:#FFFFFF;"><i class="ace-icon fa fa-plus-square"></i>&nbsp;' . plugin_lang_get( 'add_user' ) . '</h4>
                              </div>
                              <div class="modal-body">
                              ' . self::print_registered_users_by_group( $t_data[ 'id' ] ) . '
                              </div>
                              <hr />
                              <div class="w3-container w3-center">
                              <form action="" method="post">
                              <h4 class="wiki-color">' . plugin_lang_get( 'add_user_to_this_group' ) . '</h4><br />
                              <input class="form-control autofocus" type="text" id="searchEmail" name="user_email" value="" placeholder="' . plugin_lang_get( 'search_by_email') . '" /><br />
                              <div class="" id="resultEmail">
                              </div>
                              <h4 class="wiki-color">' . plugin_lang_get( 'user_profile' ) . '</h4><br />
                              <select class="form-control" id="user_is_admin" name="user_is_admin">
                              <option value="0">' . plugin_lang_get( 'normal_user' ) . '</option>
                              <option value="1">' . plugin_lang_get( 'administrator' ) . '</option>
                              </select><br />
                              <h4 class="wiki-color">' . plugin_lang_get( 'article_rights' ) . '</h4><br />
                              <select class="form-control" id="article_rights" name="article_rights">
                              <option value="0">' . plugin_lang_get( 'no_access' ) . '</option>
                              <option value="1">' . plugin_lang_get( 'read_only' ) . '</option>
                              <option value="2">' . plugin_lang_get( 'all_rights' ) . '</option>
                              </select><br />
                              <h4 class="wiki-color">' . plugin_lang_get( 'connection_rights' ) . '</h4><br />
                              <select class="form-control" id="connection_rights" name="connection_rights">
                              <option value="0">' . plugin_lang_get( 'no_access' ) . '</option>
                              <option value="1">' . plugin_lang_get( 'read_only' ) . '</option>
                              <option value="2">' . plugin_lang_get( 'all_rights' ) . '</option>
                              </select><br />
                              <h4 class="wiki-color">' . plugin_lang_get( 'contact_rights' ) . '</h4><br />
                              <select class="form-control" id="contact_rights" name="contact_rights">
                              <option value="0">' . plugin_lang_get( 'no_access' ) . '</option>
                              <option value="1">' . plugin_lang_get( 'read_only' ) . '</option>
                              <option value="2">' . plugin_lang_get( 'all_rights' ) . '</option>
                              </select><br />
                              <input type="hidden" class="hidden" name="parameters" value="user,create,' . $t_data['id'] . '" />
                              </div>
                              <br />
                              <center>
                              <button type="submit" formaction="' . plugin_page( 'config_page' ) . '" class="w3-btn w3-green w3-round-small">' . plugin_lang_get( 'add_user' ) . '</button>
                              </form>
                              </center>
                              <br />
                              <div class="modal-footer">
                                <a href="" class="w3-btn w3-red w3-round-small" data-dismiss="modal" style="text-decoration:none;">' . plugin_lang_get( 'close' ) . '</a>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>';

      }

    $t_display .= '</tbody></table>';

    return $t_display;
  }

  # Afficher un message d'information à tout utilisateur non enregistré dans le wiki
  public static function print_user_not_registered_message()
  {
    $title = plugin_lang_get( 'user_not_allowed_title' );
    $message = plugin_lang_get( 'user_not_allowed_message' );

    $display = '<div class="col-md-12 col-xs-12 alert alert-info">
                <table >
                <tr style="background-color: #d9edf7;">
                <td>' . $title . '</td>
                <td>' . $message . '</td>
                </tr>
                </table>

                </div>';


    return $display;
  }

  public static function user_is_registered( $user_id )
  {
    $t_user_table = plugin_table( "user", "wikiCylande" );

    $query = "SELECT count(*) FROM {$t_user_table} WHERE user_id=" . db_param();
    $result = db_query( $query, array( $user_id ) );

    $data = db_fetch_array( $result );

    $is_registered = ( $data[ 'count(*)'] > 0 ) ? true : false;

    return $is_registered;
  }

  public static function user_can_create_articles()
  {
    $user_table = self::get_user_table();

    $user_id = auth_get_current_user_id();

    $query = "SELECT user_article_rights FROM {$user_table} WHERE user_id=" . db_param();
    $result = db_query( $query, array( $user_id ) );
    $data = db_fetch_array( $result );

    if( ((int)$data['user_article_rights']) === 2 )
    {
      return true;
    } else {
      return false;
    }
  }

  public static function user_can_create_connections()
  {
    $user_table = self::get_user_table();

    $user_id = auth_get_current_user_id();

    $query = "SELECT user_connection_rights FROM {$user_table} WHERE user_id=" . db_param();
    $result = db_query( $query, array( $user_id ) );
    $data = db_fetch_array( $result );

    if( ((int)$data['user_connection_rights']) === 2 )
    {
      return true;
    } else {
      return false;
    }
  }

  public static function user_can_create_contacts()
  {
    $user_table = self::get_user_table();

    $user_id = auth_get_current_user_id();

    $query = "SELECT user_contact_rights FROM {$user_table} WHERE user_id=" . db_param();
    $result = db_query( $query, array( $user_id ) );
    $data = db_fetch_array( $result );

    if( ((int)$data['user_contact_rights']) === 2 )
    {
      return true;
    } else {
      return false;
    }
  }

  public static function user_can_read_articles()
  {
    $user_table = self::get_user_table();

    $user_id = auth_get_current_user_id();

    $query = "SELECT user_article_rights FROM {$user_table} WHERE user_id=" . db_param();
    $result = db_query( $query, array( $user_id ) );
    $data = db_fetch_array( $result );

    if( ((int)$data['user_article_rights']) >= 1 )
    {
      return true;
    } else {
      return false;
    }
  }

  public static function user_can_read_connections()
  {
    $user_table = self::get_user_table();

    $user_id = auth_get_current_user_id();

    $query = "SELECT user_connection_rights FROM {$user_table} WHERE user_id=" . db_param();
    $result = db_query( $query, array( $user_id ) );
    $data = db_fetch_array( $result );

    if( ((int)$data['user_connection_rights']) >= 1 )
    {
      return true;
    } else {
      return false;
    }
  }

  public static function user_can_read_contacts()
  {
    $user_table = self::get_user_table();

    $user_id = auth_get_current_user_id();

    $query = "SELECT user_contact_rights FROM {$user_table} WHERE user_id=" . db_param();
    $result = db_query( $query, array( $user_id ) );
    $data = db_fetch_array( $result );

    if( ((int)$data['user_contact_rights']) >= 1 )
    {
      return true;
    } else {
      return false;
    }
  }

  public static function get_user_pref_menu()
  {
    $user_id = auth_get_current_user_id();

    $user_pref_table = self::get_user_pref_table();

    $query = "SELECT user_home_pref FROM {$user_pref_table} WHERE user_id=" . db_param();
    $result = db_query( $query, array( $user_id ) );
    $data = db_fetch_array( $result );

    return !is_null( $data[ 'user_home_pref' ] ) ? $data[ 'user_home_pref' ] : plugin_lang_get( 'default_home_enum' );
  }
  
}
 ?>
