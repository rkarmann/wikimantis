<?php

    /**
    * Article API
    * @package wikiCylande/api
    **/

class WikiArticle
{
  # Article ID (int)
  protected $id;

  # Article's author ID (int)
  protected $id_author;

  # Coauthor IDs (string)
  protected $id_coauthor;

  # Customer ID (int)
  protected $id_customer;

  # Article type (article[0] or draft[1]) (int)
  protected $type;

  # Article timestamp (int)
  protected $timestamp;

  # Article object (title) (string)
  protected $object;

  # Article category (string)
  protected $description;

  # Article's keywords (string)
  protected $keyword;

  # Article's view count (int)
  protected $views;

  # Article constructor
  public function __construct( $id_author=0, $id_coauthor="", $id_customer=0, $type="", $timestamp=0, $object="", $category="", $description="", $keyword="", $views=0 )
  {
    $this->id_author = $id_author;
    $this->id_coauthor = $id_coauthor;
    $this->id_customer = $id_customer;
    $this->type = $type;
    $this->timestamp = time();
    $this->object = $object;
    $this->category = $category;
    $this->description = $description;
    $this->keyword = $keyword;
    $this->views = $views;
  }

  # Save article in Database
  /** @param article_properties
   *  @return article_id if saving is successful
   */
  public function save()
  {
    if( !empty( $this->category ) && !empty( $this->object ) && !empty( $this->description ) )
      {
        $article_table = DatabaseAPI::get_article_table();

        $query = "INSERT INTO {$article_table}
                (
                  id_author, id_coauthor, id_customer,
                  type, timestamp, object, category,
                  description, keyword, views
                ) VALUES (
                  " . db_param() . "," . db_param() . "," . db_param() . ",
                  " . db_param() . "," . db_param() . "," . db_param() . "," . db_param() . ",
                  " . db_param() . "," . db_param() . "," . db_param() . "
                )";

        $result = db_query( $query, array(
          $this->id_author, $this->id_coauthor, $this->id_customer,
          $this->type, $this->timestamp, $this->object, $this->category,
          $this->description, $this->keyword, $this->views
        ) );

        $this->id = db_insert_id( $article_table );

        return $this->id;
      }
    else
      {
        return NULL;
      }
  }

  public static function init_set_coauthor_list( $article_id )
  {
    $article_table = self::get_article_table();

    $initialized_list = '#0,';

    $query = "UPDATE {$article_table} SET coauthor_list=" . db_param() . " WHERE id=" . db_param();
    $result = db_query( $query, array( $initialized_list, $article_id ) );

  }

  # Fonction de mise à jour de la base de données
  public function update( $article_id )
  {
    $article_table = DatabaseAPI::get_article_table();

    $this->id = $article_id;

    if( !empty( $this->id ) )
      {
        $query = "UPDATE {$article_table} SET
                  id_author=" . db_param() . ", id_coauthor=" . db_param() . ", id_customer=" . db_param() . ",
                  type=" . db_param() . ", timestamp=" . db_param() . ", object=" . db_param() . ", category=" . db_param() . ",
                  description=" . db_param() . ", keyword=" . db_param() . ", views=" . db_param() . " WHERE id=" . db_param();

        $result = db_query( $query, array(
          $this->id_author, $this->id_coauthor, $this->id_customer,
          $this->type, $this->timestamp, $this->object, $this->category,
          $this->description, $this->keyword, $this->views
        ) );

        return $this->id;
      }
    else
      {
        return NULL;
      }
  }

  public static function article_get_coauthor( $article_id )
  {
    $article_table = self::get_article_table();

    $query = "SELECT id_coauthor FROM {$article_table} WHERE id=" . db_param();
    $result = db_query( $query, array( $article_id ) );
    $data = db_fetch_array( $result );

    $coauthor_list = $data[ 'id_coauthor' ];

    return $coauthor_list;
  }

  public static function article_set_coauthor( $article_id, $new_coauthor )
  {
    $article_table = self::get_article_table();

    $old_coauthor_list = self::article_get_coauthor( $article_id );
    $coauthor_list = $old_coauthor_list . $new_coauthor;

    $query = "UPDATE {$article_table} SET id_coauthor=" . db_param() . " WHERE id=" . db_param();
    $result = db_query( $query, array( $coauthor_list, $article_id ) );

    return ( $result ) ? $article_id : NULL;
  }

  public static function print_coauthors_users( $article_id )
  {
    $article_table = self::get_article_table();

    $query = "SELECT coauthor_list FROM {$article_table} WHERE id=" . db_param();
    $result = db_query( $query, array( $article_id ) );
    $data = db_fetch_array( $result );

    if( $data[ 'coauthor_list' ] != '#0,' )
      {
        $replace_init = str_replace( '#0,', '', $data[ 'coauthor_list' ] );
        $user_array = explode( ',', $replace_init );

        $display = '<h5 class="title-lighter">' . plugin_lang_get( 'coauthor_list' ) . '&nbsp;:</h5><br />
                      <table>';

        if( count( $user_array ) >= 2 )
          {
            for( $i = 0; $i < count( $user_array ) - 1; $i++ )
              {
                $coauthor_id = str_replace( '#', '', $user_array[ $i ] );

                $display .= ' <tr>
                                <td class="w3-padding">
                                  <p>' . user_get_name( $coauthor_id ) . '</p>
                                </td>
                                <td class="w3-padding">
                                <form action="" method="post">
                                  <input type="hidden" value="' . $coauthor_id . '" name="coauthor_id" id="coauthor_id" />
                                  <button type="submit" formaction="' . plugin_page( 'wiki_article_box' ) . '&amp;t=article&amp;id=' . $article_id . '&amp;a=unset_coauthor"
                                   class="btn btn-info btn-small w3-hover-red"><i class="fa fa-times"></i></button>
                                </form>
                                </td>
                              </tr>';
              }

              $display .= '</table>';
          }
        else
          {
            $display = plugin_lang_get( 'this_article_is_not_shared' ) . '<br />';
          }

        return $display;
      }
  }

  public static function article_unset_coauthor( $old_coauthor, $article_id )
  {
    $article_table = self::get_article_table();

    if( self::article_is_shared( $article_id ) )
      {
        $query = "SELECT coauthor_list FROM {$article_table} WHERE id=" . db_param();
        $result = db_query( $query, array( $article_id ) );
        $data = db_fetch_array( $result );
        $old_coauthor_list = $data[ 'coauthor_list' ];

        $coauthor_to_replace = '#' . $old_coauthor . ',';

        $new_coauthor_list = str_replace( $coauthor_to_replace, '', $old_coauthor_list );

        $query = "UPDATE {$article_table} SET coauthor_list=" . db_param() . " WHERE id=" . db_param();
        $result = db_query( $query, array( $new_coauthor_list, $article_id ) );
      }

    return !empty( $result ) ? $article_id : NULL;
  }

  /** Fonction de vérification du partage de l'article
    * @param $article_id
    * Si l'article est partagé @return true
    * Sinon @return false
    **/

  public static function article_is_shared( $article_id )
  {
    $article_table = self::get_article_table();

    $query = "SELECT coauthor_list FROM {$article_table} WHERE id=" . db_param();
    $result = db_query( $query, array( $article_id ));
    $data = db_fetch_array( $result );

    $is_shared = ( $data[ 'coauthor_list' ] != '#0,' ) ? true : false;

    return $is_shared;

  }

  # Fonction de calcul du nombre d'article lié à un paramètre
  public static function article_count( $parameter_type, $parameter_id )
  {
    $article_table = self::get_article_table();

    $query = "SELECT count(*) FROM {$article_table} WHERE " . $parameter_type . "=" . db_param();
    $result = db_query( $query, array( $parameter_id ) );
    $data = db_fetch_array( $result );

    return $data[ 'count(*)' ];
  }

  # Fonction d'affichage du nombre d'article pour une catégorir donnée
  public function article_count_by_type( $parameter_type, $parameter_id )
  {
    $article_table = self::get_article_table();

    $query = "SELECT count(*) FROM {$article_table} WHERE " . $parameter_type . "=" . db_param();
    $result = db_query( $query, array( $parameter_id ) );
    $data = db_fetch_array( $result );

    $display = '<span class="label w3-round-xxlarge w3-right"><p>' . $data[ 'count(*)' ] . '</p></span>';

    return $display;
  }

  public static function article_count_coauthor_articles()
  {
    $article_table = self::get_article_table();

    $current_user_id = auth_get_current_user_id();

    $coauthor_id = '#' . $current_user_id . ',';

    $query = "SELECT count(*) FROM {$article_table} WHERE coauthor_list LIKE '%" . $coauthor_id . "%'";
    $result = db_query( $query );
    $data = db_fetch_array( $result );

    return '( ' . $data[ 'count(*)' ] . ' )';
  }

  public static function article_count_author_articles( $user_id, $database_choice )
  {
    $article_table = ( $database_choice === 'article' ) ? self::get_article_table() : self::get_draft_table();

    $query = "SELECT count(*) FROM {$article_table} WHERE author_id=" . db_param();
    $result = db_query( $query, array( $user_id ) );
    $data = db_fetch_array( $result );

    return '( ' . $data[ 'count(*)' ] . ' )';
  }

  public static function article_count_user_articles( $user_id = NULL, $database_choice )
  {
    $database_table = plugin_table( $database_choice, "wikiCylande" );

    $author_id = !is_null( $user_id ) ? $user_id : auth_get_current_user_id();

    $query = "SELECT count(*) FROM {$database_table} WHERE author_id=" . db_param() . " ORDER BY timestamp ASC";
    $result = db_query( $query, array( $author_id ) );
    $count = db_fetch_array( $result );

    return $count[ 'count(*)' ];
  }

  public static function article_count_exists_by_user( $database_choice, $user_id )
  {
    $wiki_table = plugin_table( $database_choice, "wikiCylande" );

    $query = "SELECT count(*) FROM {$wiki_table} WHERE author_id=" . db_param();
    $result = db_query( $query, array( $user_id ) );
    $data = db_fetch_array( $result );

    $exists = ( $data[ 'count(*)' ] > 0 ) ? true : false;

    if( $exists === false && $database_choice != 'draft' ){

      $coauthor_id = '#' . $user_id . ',';

      $query = "SELECT count(*) FROM {$wiki_table} WHERE coauthor_list LIKE '%" . db_param() . "%'";
      $result = db_query( $query, array( $coauthor_id ) );
      $data = db_fetch_array( $result );

      $exists = ( $data[ 'count(*)'] > 0 ) ? true : false;
    }

    return $exists;
  }

  # Fonction d'affichage du nombre d'article pour une catégorie donnée et un produit ciblé
  public static function article_count_by_type_and_target( $parameter_type, $id_type, $parameter_target, $id_target )
  {
    $article_table = self::get_article_table();

    $query = "SELECT count(*) FROM {$article_table} WHERE " . $parameter_type . "=" . $id_type . " AND " .  $parameter_target . "=" . $id_target;
    $result = db_query( $query );
    $data = db_fetch_array( $result );

    $display = '<span class="label w3-round-xxlarge w3-right"><p>' . $data[ 'count(*)' ] . '</p></span>';

    return $display;
  }

  # Afficher le nombre d'article lié à un produit, module ou sous-module (type)
  public function display_related_articles_count( $parameter_type, $parameter_id )
  {
    $article_table = self::get_article_table();

    $query = "SELECT count(*) FROM {$article_table} WHERE " . db_param() . '=' . db_param();
    $result = db_query( $query, array( $parameter_type, $parameter_id ) );
    $data = db_fetch_array( $result );

    $count = $data[ 'count(*)' ];

    $display = '';

      if( $count >= 1){

      $display .= '<p class="w3-text-red">' . plugin_lang_get( 'this_' . $parameter_type . '_is_related_to' ) . '&nbsp;';

      $display .= ( $count > 1 ) ? $count . '&nbsp;' . plugin_lang_get( 'articles' ) : $count . '&nbsp;' . plugin_lang_get( 'article' );

      $display .= '.</p>';

    } else {

      $display .= '<p class="w3-text-green">' . plugin_lang_get( 'no_related_article' ) . '.';

      $display .= '</p>';

    }

    return $display;

  }

  public static function get_associated_parameter( $parameter_type, $parameter_id )
  {
    if( $parameter_type != 'product')
    {
      switch( $parameter_type )
      {
        case 'module':
        $parameter_target = 'product';
        break;
        case 'type':
        $parameter_target = 'module';
        break;
      }

      $setting_table = self::get_parameter_table( $parameter_target );

      $query = "SELECT * FROM {$setting_table} WHERE id=" . db_param();
      $result = db_query( $query, array( $parameter_id ) );
      $data = db_fetch_array( $result );

      return '<p>' . $data[ $parameter_target . '_name' ] . '</p>';

    } elseif( $parameter_type != 'product' && ((int)$parameter_id) === 0 ){

      return '<p>' . plugin_lang_get( 'no_relation' ) . '<p>';

    } else {

      return;
    }
  }

  public function count_related_articles( $parameter_type, $parameter_id )
  {
    $article_table = self::get_article_table();

    $query = "SELECT count(*) FROM {$article_table} WHERE " . $parameter_type . '=' . $parameter_id;
    $result = db_query( $query );
    $data = db_fetch_array( $result );

    $count = $data[ 'count(*)' ];

    $display = '';

    if( $count >= 1 ){

      $display .= '<a href="' . plugin_page( 'wiki_view_all' ) . '&amp;type=' . $parameter_type . '&amp;id=' . $parameter_id . '" class="w3-text-green">';

      $display .= '<p class="w3-text-green">';

      $display .= ( $count > 1 ) ? $count . '&nbsp;' . plugin_lang_get( 'articles' ) : $count . '&nbsp;' . plugin_lang_get( 'article' );

      $display .= '</p></a>';

    } else {

      $display .= '<p class="w3-text-red">';

      $display .= plugin_lang_get( 'obsolete' );

      $display .= '</p>';

    }

    return $display;

  }

  # Fonction de vérification de l'existance d'un article
  public function article_exists( $article_id, $database_choice )
  {
    $wiki_table = plugin_table( $database_choice, "wikiCylande" );

    $query = "SELECT count(*) FROM {$wiki_table} WHERE id=" . db_param();
    $result = db_query( $query, array( $article_id ) );
    $data = db_fetch_array( $result );

    $exists = ( $data[ 'count(*)' ] > 0 ) ? true : false;

    return $exists;

  }

  # Fonction d'affichage de tous les articles dans un tableau
  public static function get_articles_and_display_all( $order = 'product', $parameter_type = NULL, $parameter_id = NULL, $sub_type = NULL, $sub_id = NULL, $id_client = NULL )
  {
      # Récupération de la table des articles
    $article_table = self::get_article_table();

      /**
        * Vérifier si $parameter_type & $parameter_id & $sub_type & $sub_id sont transmis
        * N'afficher que les articles qui correspondent aux critères
        * Vérifier si $parameter_type & $parameter_id sont transmis
        * N'afficher que les articles qui correspondent aux critères
        * Sinon, affichr tout les articles dans le tableau
        **/

    if( !is_null( $parameter_type ) && !is_null( $parameter_id ) && !is_null( $sub_type ) && !is_null( $sub_id ) )
      {
        $query = "SELECT * FROM {$article_table} WHERE " . $parameter_type . "=" . $parameter_id . " AND " . $sub_type . "=" . $sub_id . " ORDER BY " . $order . " ASC";
        $result = db_query( $query );
        $order_url = plugin_page( 'wiki_view_all' ) . '&amp;t=' . $parameter_type . '&amp;id=' . $parameter_id
                     . '&amp;st=' . $sub_type . '&amp;sid=' . $sub_id . '&amp;ord=';
      }
    elseif( !is_null( $parameter_type ) && !is_null( $parameter_id ) && is_null( $sub_type ) && is_null( $sub_id ) )
      {
        $query = "SELECT * FROM {$article_table} WHERE " . $parameter_type . "=" . $parameter_id . " ORDER BY " . $order . " ASC";
        $result = db_query( $query );
        $order_url = plugin_page( 'wiki_view_all' ) . '&amp;t=' . $parameter_type . '&amp;id=' . $parameter_id .  '&amp;ord=';
      }
    elseif( !is_null( $id_client ) && is_null( $parameter_type ) && is_null( $parameter_id) && is_null( $sub_type ) && is_null( $sub_id ))
      {
        $query = "SELECT * FROM {$article_table} WHERE id_client=" . db_param() . " ORDER BY " . $order . " ASC";
        $result = db_query( $query, array( $id_client ));
        $order_url = plugin_page( 'wiki_view_all' ) . '&amp;ord=';
      }
    else
      {
        $query = "SELECT * FROM {$article_table} ORDER BY " . $order . " ASC";
        $result = db_query( $query );
        $order_url = plugin_page( 'wiki_view_all' ) . '&amp;ord=';
      }

      # Construction de l'en tête du tableau avec la fonction de filtres Bootstrap
      # Voir filter_jquery.js

    $display = ' <div class="col-md-12 col-xs-12 no-padding">
                  <table>
                    <tr>
                      <td class="w3-gray" width="18%" style="text-align:center;">
                       <span class="w3-text-white"><i class="ace-icon fa fa-search">&nbsp;</i></span>
                      </td>
                      <td width="82%" style="background-color:#307ECC;">
                       <input class="form-control w3-card-2" style="width:100%;" size="100%" id="myInputView" type="text" placeholder="' . plugin_lang_get( 'search_article' ) . '"/>
                      </td>
                    </tr>
                   </table>
                 <div class="" style="overflow-y: scroll !important; max-height: 500px">
                  <table class="w3-table-all w3-hoverable">
                    <thead>
                      <tr>
                        <td class="w3-center" style="background-color:#FFFFFF; color:#307ECC;">
                          <a href="' . $order_url . 'product">
                          ' . plugin_lang_get( 'product' ) . '
                          </a>
                        </td>
                        <td class="w3-center" style="background-color:#FFFFFF; color:#307ECC;">
                          <a href="' . $order_url . 'module">
                          ' . plugin_lang_get( 'module' ) . '
                          </a>
                        </td>
                        <td class="w3-center" style="background-color:#FFFFFF; color:#307ECC;">
                          <a href="' . $order_url . 'type">
                          ' . plugin_lang_get( 'type' ) . '
                          </a>
                        </td>
                        <td class="w3-center" style="background-color:#FFFFFF; color:#307ECC;">
                          <a href="' . $order_url . 'id_client">
                          ' . plugin_lang_get( 'consumer_id' ) . '
                          </a>
                        </td>
                        <td class="w3-center" style="background-color:#FFFFFF; color:#307ECC;">
                          <a href="' . $order_url . 'object">
                          ' . plugin_lang_get( 'object' ) . '
                          </a>
                        </td>
                      </tr>
                    </thead>
                    <tbody id="myTableView">';

    while( $data = db_fetch_array( $result ) )
    {

      $display .=
        '<tr>' .
        '<td class="w3-center w3-padding-16">
            <a href="' . plugin_page( 'wiki_view_all' ) . '.php&amp;t=product&amp;id=' . $data[ 'product' ] . '">' .
            wikiArticleSetting::get_parameter_by_id( 'product', $data[ 'product' ] ) . '
         </a></td>' .
        '<td class="w3-center w3-padding-16">
            <a href="' . plugin_page( 'wiki_view_all' ) . '.php&amp;t=module&amp;id=' . $data[ 'module' ] . '">' .
            wikiArticleSetting::get_parameter_by_id( 'module', $data[ 'module' ] ) . '
         </a></td>' .
        '<td class="w3-center w3-padding-16">
            <a href="' . plugin_page( 'wiki_view_all' ) . '.php&amp;t=type&amp;id=' . $data[ 'type' ] . '">' .
            wikiArticleSetting::get_parameter_by_id( 'type', $data[ 'type' ] ) . '
         </a></td>' .
        '<td class="w3-center w3-padding-16">
           <a href="' . plugin_page( 'wiki_view_all' ) . '&amp;t=id_client' . '&amp;id=' . $data[ 'id_client' ] . '" data-toggle="tooltip" title="' . wikiConsumer::get_consumer_name($data[ 'id_client' ]) .'">'
           . wikiConsumer::get_consumer_name( $data[ 'id_client' ] ) . '
         </a></td>' .
        '<td class="w3-center w3-padding-16">
            <a href="' . plugin_page( 'view', false ) . '&amp;type=article&amp;id=' . $data[ 'id' ] . '" data-toggle="tooltip" title="' . wikiConsumer::get_consumer_name($data[ 'id_client' ]) .'">'
            . $data[ 'object' ] . '
         </a></td>';
    }


    $display .= '</tbody></table></div></div>';

    return $display;
  }

  # Fonctions de suppressions des articles de la base de données
  /**
    * @param $database_choice >>> 'draft', 'article' (brouillon ou article)
    * @param $article_id >>> ID de l'article ou du brouillon
    **/

  public static function delete_wiki_by_id( $database_choice ,$article_id )
  {
    $wiki_table = plugin_table( $database_choice, "wikiCylande" );

    $current_user_id = auth_get_current_user_id();

    # On vérifie que l'utilisateur est bien propriétaire de l'article ou qu'il est adminsitrateur Mantis
    $query = "SELECT author_id FROM {$wiki_table} WHERE id=" . db_param();
    $result = db_query( $query, array( $article_id ) );
    $data = db_fetch_array( $result );

    if( $current_user_id === ((int)$data[ 'author_id' ]) || user_is_administrator( $current_user_id ) || wikiUser::user_is_admin( $current_user_id ) )
    {
    $query = "DELETE FROM {$wiki_table} WHERE id=" . db_param();
    $result = db_query( $query, array( $article_id ) );

    return ( !is_null($result) ) ? true : NULL;

    } else {

      # Sinon on retourne NULL
      return NULL;
    }
  }

}
