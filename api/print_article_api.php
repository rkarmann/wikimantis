<?php

  /**
   * Print article API
   * @package wikiCylande/api
   * */

class wikiArticleCard
{

  public function print_article_by_id( $article_id, $database_choice )
  {
    # Utiliser la base de données correspondantes au type d'article (article ou brouillon)
  $wiki_table = plugin_table( $database_choice, 'wikiCylande' );

    # Requête permettant de récupérer tous les champs en transmettant l'ID de l'article
  $query = "SELECT * FROM {$wiki_table} WHERE id=" . db_param();
  $result = db_query( $query, array( $article_id ) );

    # Création du début de la carte
  $display = '<div class="col-md-9 col-xs-9 no-padding">
                <div class="col-md-12 col-xs-12 w3-animate-opacity">
                <form id="modify_article_form" method="post" action="' . plugin_page( 'wiki_modify_article' ) . '">
                <div class="space-10"></div>
                <div class="w3-card-2" style="width:100%;">';

    # Parcourir le tableau contenant le résultat de la requête
  $data = db_fetch_array( $result );

    # Définition du bouton de modification en fonction du type d'article (article ou brouillon)
  $modify_button = plugin_page( 'wiki_article_box' ) . '&amp;t=' . $database_choice . '&amp;id=' . $data[ 'id' ] . '&amp;a=modify';

    # Définition du bouton de publication en fonction du type d'article
  $publicate_type = ( $database_choice === 'article' ) ? 'draft' : 'article';
    # Si le type d'article est un article, alors l'action publier renverra vers les brouillons et inverse s'il s'agit d'un brouillon
  $publicate_button = plugin_page( 'wiki_article_box' ) . '&amp;t=' . $publicate_type . '&amp;a=create';

    # Construction du header de l'article, avec le titre, la date de création et son auteur
  $display .= '<header class="w3-container text-center" style="background-color:#307ECC;" >
                <blockquote>
                  <p style="color:#FFFFFF;">
                    ' . $data[ 'object' ] . '
                  </p>
                  <small style="color:#ccccb3;">' . plugin_lang_get( "edited_by" ) . '
                    <cite title="' . plugin_lang_get( 'author' ) . '" style="color:#FFFFFF">'
                    . user_get_name( $data[ 'author_id' ] ) . '&nbsp;
                    </cite>
                    ' . plugin_lang_get( 'on_date' ) . '&nbsp;' . date('d-m-Y', $data[ 'timestamp' ] ) . '
                  </small>
                </blockquote>
               </header>
               ';

    # SI l'utilisateur est autorisé à éditer des articles
  if( wikiUser::user_can_modify_article( $data[ 'id' ] ) )
      {

    # Ajouter les boutons de modifications de l'article
  $display .=   '<div class="w3-container w3-margin w3-center">
                      <a href="#ModalModify-' . $data[ 'id' ] . '" class="w3-btn w3-grey w3-hover-green" data-toggle="modal" data-target="#ModalModify-' . $data[ 'id' ] . '" style="text-decoration:none;">' . plugin_lang_get( 'modify' ) . '</a>
                      <a href="#ModalSetcoauthor-' . $data[ 'id' ] . '" class="w3-btn w3-grey w3-hover-deep-orange" data-toggle="modal" data-target="#ModalSetcoauthor-' . $data[ 'id' ] . '" style="text-decoration:none;">' . plugin_lang_get( 'set_coauthor' ) . '</a>
                      <a href="#ModalDelete-' . $data[ 'id' ] . '" class="w3-btn w3-grey w3-hover-red" data-toggle="modal" data-target="#ModalDelete-' . $data[ 'id' ] . '" style="text-decoration:none;">' . plugin_lang_get( 'delete' ) . '</a>
                 </div>';
  }

    # Construction du corps de l'article, contenant la description de celui-ci
  $display .=  '<div class="w3-container">
                  <div class="col-md-12 col-xs-12">
                    <br />
                    <div class="">
                    ' . $data[ 'description' ] . '
                    </div>
                    <div class="space-10"></div>
                    <hr />
                  </div>
                </div>';


    # Construction du footer de l'article, contenant les catgéories associées ainsi que le client
  $display .= ' <center>
                <footer class="w3-container w3-dark-gray w3-padding w3-margin">&nbsp;Tags&nbsp;:&nbsp;
                ' . self::print_labels( $data[ 'product' ], $data[ 'module' ], $data[ 'type' ], $data[ 'id_client' ], false ) . '
                </footer>
                <div class="space-10"></div>
                </div>
                </center>
                </form>
                </div>
                </div>
                ';

    # Ajout de la fenêtre de confirmation de la suppresion de l'article (lié au bouton de suppresion) MODAL BOOTSTRAP
  $display .= '<div id="ModalDelete-' . $data[ 'id' ] . '" class="modal w3-animate-zoom" role="dialog">
                     <div class="modal-dialog modal-md">
                       <div class="modal-content">
                         <div class="modal-header">
                           <button type="button" class="close" data-dismiss="modal">&times;</button>
                             <h4 class="modal-title">' . plugin_lang_get( 'delete_article' ) . '</h4>
                         </div>
                         <div class="modal-body">
                           <p>' . plugin_lang_get( 'confirm_delete' ) . '</p>
                         </div>
                         <div class="modal-footer">
                         <form action="" method="post">.
                           <a href="' . plugin_page( 'wiki_article_box' ) . '&amp;t=' . $database_choice . '&amp;id=' . $data[ 'id' ] . '&amp;a=delete'
                           . '" class="btn btn-default">' . plugin_lang_get( 'delete' ) . '</a>
                           <button type="button" class="btn btn-default" data-dismiss="modal">' . plugin_lang_get( 'cancel' ) . '</button>
                         </form>
                         </div>
                       </div>
                     </div>
                   </div>';

      # Ajout de la fenêtre de partage de l'article (pour qu'un autre utilisateur non-admin puisse modifier l'article)
   $display .= self::print_coauthor_modal( $data, $database_choice );


    # Ajout de la fenêtre de modification de l'article
  $display .= '<div id="ModalModify-' . $data[ 'id' ] . '" class="modal w3-animate-zoom" role="dialog">
                     <form id="modify_draft_form" method="post" action="">
                     <div class="modal-dialog modal-lg">
                       <div class="modal-content form-group">
                         <div class="modal-header w3-center">
                           <button type="button" class="close" data-dismiss="modal">&times;</button>
                             <h4 class="modal-title"><i class="ace-icon fa fa-pencil-square-o"></i>&nbsp;' . plugin_lang_get( 'modify_article' ) . '</h4>
                               <hr />
                           <h4 class="modal-title" style="color:#307ECC;">' . plugin_lang_get( 'title' ) . '</h4><br />
                           <input class="form-control" type="text" id="object" name="object" size="50" maxlength="128" value="' . $data[ 'object' ] . '" required />
                         </div>
                         <div class="modal-body w3-center">
                         <h4 class="modal-title" style="color:#307ECC;">' . plugin_lang_get( 'product' ) . '</h4><br />'
                         . wikiArticleSetting::config_print_all_parameters_in_selectbox( 'product', $data[ 'product' ] ) . '<br />' .
                         '<h4 class="modal-title" style="color:#307ECC;">' . plugin_lang_get( 'module' ) . '</h4><br />'
                         . wikiArticleSetting::config_print_all_parameters_in_selectbox( 'module', $data[ 'module' ] ) . '<br />' .
                         '<h4 class="modal-title" style="color:#307ECC;">' . plugin_lang_get( 'type' ) . '</h4><br />'
                         . wikiArticleSetting::config_print_all_parameters_in_selectbox( 'type', $data[ 'type' ] ) . '<br />' .
                         '<h4 class="modal-title" style="color:#307ECC;">' . plugin_lang_get( 'consumer_id' ) . '</h4><br />'
                         . wikiConsumer::print_all_consumers_ids_in_selectbox( $data[ 'id_client'] ) . '<hr />' . '<br />' .
                         '<h4 class="modal-title" style="color:#307ECC;">' . plugin_lang_get( 'description' ) . '</h4><br />' .
                         '<textarea class="editor" id="description" name="description" cols="60" rows="10" value="">' . htmlspecialchars( $data[ 'description' ] ) . '</textarea><hr />' .
                         '<h4 class="modal-title" style="color:#307ECC;">' . plugin_lang_get( 'keywords' ) . '</h4><br />
                         <input type="text" class="form-control" name="keyword" id="" value="' . $data[ 'keyword' ] . '" placeholder="' . plugin_lang_get( 'type_keywords_separated_by_comas' ) . '" />
                         </div>
                         <input type="hidden" name="id" id="id" value="' . $data[ 'id' ] . '"/>
                         <div class="modal-footer">
                         <button type="submit" formaction="' . $modify_button . '" class="w3-btn w3-green" >' . plugin_lang_get( 'modify' ) . '</button>
                         <button type="submit" formaction="' . $publicate_button . '" class="w3-btn w3-green" >' . plugin_lang_get( 'publicate_as_' . $database_choice ) . '</button>
                           <button type="button" class="w3-btn w3-red" data-dismiss="modal">' . plugin_lang_get( 'cancel' ) . '</button>
                         </div>
                       </div>
                     </div>
                     </form>
                   </div>';

    # Renvoyer l'article construit qu'il suffit d'appeler par un 'echo'
  return $display;

  }

public static function print_coauthor_modal( $data, $database_choice )
{
  # Si l'article n'est pas déjà partagé
  $display = '<div id="ModalSetcoauthor-' . $data[ 'id' ] . '" class="modal w3-animate-zoom" role="dialog">
                     <div class="modal-dialog modal-md">
                       <div class="modal-content">
                         <form action="" method="post">
                           <div class="modal-header wiki-background-color">
                             <button type="button" class="close" data-dismiss="modal">&times;</button>
                             <h4 class="modal-title"><i class="fa fa-user"></i>&nbsp;' . plugin_lang_get( 'set_coauthor' ) . '</h4>
                           </div>
                           <div class="w3-container wiki-color">
                            ' . wikiArticle::print_coauthors_users( $data[ 'id' ] ) . '
                             <br />
                             <h4 class="modal-title">' . plugin_lang_get( 'select_user_to_add' ) . '</h4><br />
                             ' . wikiUser::print_user_group_in_selectbox( $data[ 'id' ] ) . '<br />
                           </div>
                           <div class="modal-footer">
                             <button formaction="' . plugin_page( 'wiki_article_box' ) . '&amp;t=' . $database_choice . '&amp;id=' . $data[ 'id' ] . '&amp;a=set_coauthor"
                              class="btn btn-default">' . plugin_lang_get( 'save' ) . '
                             </button>
                             <button type="button" class="btn btn-default" data-dismiss="modal">
                              ' . plugin_lang_get( 'cancel' ) . '
                             </button>
                           </div>
                         </form>
                       </div>
                     </div>
                   </div>';

   return $display;
}
/**
  * Fonction d'affichage du Carousel (les 5 derniers articles publiés)
  * Ne prends pas de paramètres, nécessite que 5 articles au moins aient été créés
  * Pour d'afficher
  **/

  # Fonction d'affichage des labels
  /**
    * @param $product_id (doit être renseigné pour afficher le label contenant le produit)
    * @param $module_id (doit être renseigné pour afficher le label contenant le module)
    * @param $type_id (doit être renseigné pour afficher le label contenant le sous-module)
    * @param $consumer_id (doit être renseigné pour arficher le label contenant le client associé)
    * @param $is_centered (pour centrer les labels les uns en dessous des autres si l'affchage de l'article est sous forme de carte)
    * @return $display;
    **/



  public static function print_labels( $product_id, $module_id, $type_id, $consumer_id, $is_centered = false )
  {

    # Product
    $p_color = wikiArticleSetting::get_color_parameter_by_id( 'product', $product_id );
    $p_name = wikiArticleSetting::get_parameter_by_id( 'product', $product_id );

    # Module
    $m_color = wikiArticleSetting::get_color_parameter_by_id( 'module', $module_id );
    $m_name = wikiArticleSetting::get_parameter_by_id( 'module', $module_id );

    # Type
    $t_color = wikiArticleSetting::get_color_parameter_by_id( 'type', $type_id );
    $t_name = wikiArticleSetting::get_parameter_by_id( 'type', $type_id );

    # Client
    $c_color = '';
    $c_name = '';

    $display = '';

    # Consumer
    if( !empty( $consumer_id ) )
    {
    $c_color = '#307ECC';
    $c_name = wikiConsumer::get_consumer_name( $consumer_id );
    }

    if( $is_centered ){ $display .= '<center>'; }

    $display .= '<span class="w3-btn btn-sm w3-round-xxlarge w3-hover-gray" style="background-color:' . $p_color . ';">
                  <a href="' . plugin_page( 'wiki_view_all' ). '&amp;t=product&amp;id=' . $product_id . '" class="" style="text-decoration:none;color:#FFFFFF;">
                  ' . $p_name . '
                  </a>
                </span>&nbsp';

    if( $is_centered ){ $display .= '<br /><br />'; }

    $display .= '<span class="w3-btn btn-sm w3-round-xxlarge w3-hover-gray" style="background-color:' . $m_color . ';">
                  <a href="' . plugin_page( 'wiki_view_all' ). '&amp;t=module&amp;id=' . $module_id . '" class="" style="text-decoration:none;color:#FFFFFF;">
                  ' . $m_name . '
                  </a>
                </span>&nbsp';

    if( $is_centered ){ $display .= '<br /><br />'; }

    $display .= '<span class="w3-btn btn-sm w3-round-xxlarge w3-hover-gray" style="background-color:' . $t_color . ';">
                  <a href="' . plugin_page( 'wiki_view_all' ). '&amp;t=type&amp;id=' . $type_id . '" class="" style="text-decoration:none;color:#FFFFFF;">
                  ' . $t_name . '
                  </a>
                </span>&nbsp';

    if( $consumer_id != 999999 )
    {
    if( $is_centered ){ $display .= '<br /><br />'; }

    $display .= '<span class="w3-btn btn-sm w3-round-xxlarge w3-hover-gray" style="background-color:' . $c_color . ';">
                  <a href="' . plugin_page( 'wiki_view_all' ). '&amp;type=id_client&amp;id=' . $consumer_id . '" class="" style="text-decoration:none;color:#FFFFFF;">
                  ' . $c_name . '
                  </a>
                </span>&nbsp';
    }

    if( $is_centered ){ $display .= '</center>'; }

    return $display;
  }

  /**
    * @param $article_type ( type d'article : article ou brouillon )
    * @return $display;
    * Ecrit un message informant qu'il n'y pas d'articles / brouillons si l'utilisateur n'en a pas encore créé
    * Le message s'affiche dans la section (mes Articles) ou (mes Brouillons)
    **/

  public static function print_default_welcome_message( $article_type )
  {
      # Préfix correspondant au fichier de traduction
      # Défini en fonction du type d'article
    switch( $article_type )
    {
      case 'article':
      $prefix = 'a_';
      break;

      case 'draft':
      $prefix = 'd_';
      break;
    }


      # Construction de la carte contenant le message
    $display = '<div class="space-10"></div>
                    <div class="col-md-9 col-xs-9 w3-animate-opacity">
                    <div class="w3-card-2">
                        <header class="w3-container w3-light-grey">
                          <h4><i class="ace-icon fa fa-info-circle"></i>&nbsp;' . plugin_lang_get( $prefix . 'no_articles') . '&nbsp;</h4>
                        </header>
                        <div class="w3-container w3-margin">
                        <p>' . plugin_lang_get( $prefix . 'no_articles_for_this_user' ) .'</p>
                        </div>
                    </div>
                 </div>';

    return $display;

  }

  # Fonction d'affichage des articles / brouillons dans des cartes en mode prévisualiqsation
  /**
   * @param $database_choice >>> indique quel type afficher 'draft', 'article'
   * @param $order >>> choix du critère de tri
   */

  public static function print_all_articles_in_cards( $database_choice, $order )
  {
    # Appel de la base de données
    $t_wiki_table = plugin_table( $database_choice, "wikiCylande" );

    $user_id = auth_get_current_user_id();

    $display = self::print_view_mode_bar( $database_choice );

    if( wikiArticle::article_count_exists_by_user( $database_choice, $user_id ) )
    {
    # Construction de la requête
    $query = "SELECT * FROM {$t_wiki_table} WHERE author_id=" . db_param() . " ORDER BY " . db_param() . " ASC";
    $result = db_query( $query, array( $user_id, $order ) );

    $display .= '<div class="col-md-9 col-xs-9 no-padding" style="top:-16px;"><div class="space-10"></div>';

    $modify_button_string = ( $database_choice === 'article' ) ? plugin_lang_get( 'modify_article' ) : plugin_lang_get( 'modify_draft' );

    # Construction de la carte
    while( $data = db_fetch_array( $result ) )
    {
      if( $database_choice === 'draft' )
      {
        $publicate_button = ' <button type="submit" formaction="' . plugin_page( 'wiki_article_box' ) . '&amp;t=article' . '&amp;a=create" class="w3-btn w3-green" >' . plugin_lang_get( 'publicate' ) . '</button>';
      } else {
        $publicate_button = '';
      }

      $display .= '<div class="col-md-6 col-xs-6 w3-animate-opacity w3-margin" style="width:27rem; height:33rem;">
                      <div class="w3-card-2">
                          <header class="w3-container w3-light-grey w3-padding" style="display:block;">
                            <h4 class="w3-left">' . plugin_lang_get( $database_choice . '_from') . '&nbsp;' . date('d-m-Y', $data[ 'timestamp' ] ) . '</h4>
                          </header>
                          <div class="w3-container w3-margin">
                          <h4 class="modal-title" style="color:#307ECC;">' . plugin_lang_get( 'object' ) . '</h4>
                          <br />
                            <p>' . $data[ 'object' ] . '</p>
                          <hr />
                          <center>
                            ' . self::print_labels( $data['product'], $data['module'], $data['type'], $data['id_client'], true ) . '
                            <hr />
                            <a class="btn w3-hover-teal w3-round-small btn-sm" href="' . plugin_page( 'view') . '&amp;type=' . $database_choice . '&amp;id=' . $data[ 'id' ] . '" style="text-decoration:none;">' . plugin_lang_get( 'view') . '</a>
                          <a class="btn w3-hover-teal w3-round-small btn-sm" data-toggle="modal" data-target="#ModalModify-' . $data[ 'id' ] . '" style="text-decoration:none;">' . plugin_lang_get( 'modify') . '</a>
                          <a class="btn w3-hover-red w3-round-small btn-sm" data-toggle="modal" data-target="#ModalDelete-' . $data[ 'id' ] . '" style="text-decoration:none;">' . plugin_lang_get( 'delete' ) . '</a>
                          </center>
                          <div class="space-10"></div>
                      </div>
                    </div>';


         $display .= '  <div id="ModalDelete-' . $data[ 'id' ] . '" class="modal w3-animate-zoom" role="dialog">
                            <div class="modal-dialog modal-md">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">' . plugin_lang_get( 'delete_article' ) . '</h4>
                                </div>
                                <div class="modal-body">
                                  <p>' . plugin_lang_get( 'confirm_delete' ) . '</p>
                                </div>
                                <div class="modal-footer">
                                  <a href="' . plugin_page( 'wiki_article_box' ) . '&amp;t=' . $database_choice . '&amp;id=' . $data[ 'id' ] . '&amp;a=delete'
                                  . '" class="w3-btn w3-gray w3-hover-green">' . plugin_lang_get( 'delete' ) . '</a>
                                  <button type="button" class="w3-btn w3-gray w3-hover-red" data-dismiss="modal">' . plugin_lang_get( 'cancel' ) . '</button>
                                </div>
                              </div>
                            </div>
                          </div>

                        </div>';

          $display .= '<div id="ModalModify-' . $data[ 'id' ] . '" class="modal w3-animate-zoom" role="dialog">
                             <form id="modify_draft_form" method="post" action="">
                             <div class="modal-dialog modal-lg">
                               <div class="modal-content form-group">
                                 <div class="modal-header w3-center">
                                   <button type="button" class="close" data-dismiss="modal">&times;</button>
                                     <h4 class="modal-title"><i class="ace-icon fa fa-pencil-square-o"></i>&nbsp;' . plugin_lang_get( 'modify_article' ) . '</h4>
                                       <hr />
                                   <h4 class="modal-title" style="color:#307ECC;">' . plugin_lang_get( 'object' ) . '</h4><br />
                                   <input class="form-control" type="text" id="object" name="object" size="50" maxlength="128" value="' . $data[ 'object' ] . '" required />
                                 </div>
                                 <div class="modal-body w3-center">
                                 <h4 class="modal-title" style="color:#307ECC;">' . plugin_lang_get( 'product' ) . '</h4><br />'
                                 . wikiArticleSetting::config_print_all_parameters_in_selectbox( 'product', $data[ 'product' ] ) . '<br />' .
                                 '<h4 class="modal-title" style="color:#307ECC;">' . plugin_lang_get( 'module' ) . '</h4><br />'
                                 . wikiArticleSetting::config_print_all_parameters_in_selectbox( 'module', $data[ 'module' ] ) . '<br />' .
                                 '<h4 class="modal-title" style="color:#307ECC;">' . plugin_lang_get( 'type' ) . '</h4><br />'
                                 . wikiArticleSetting::config_print_all_parameters_in_selectbox( 'type', $data[ 'type' ] ) . '<br />' .
                                 '<h4 class="modal-title" style="color:#307ECC;">' . plugin_lang_get( 'consumer_id' ) . '</h4><br />'
                                 . wikiConsumer::print_all_consumers_ids_in_selectbox( $data[ 'id_client'] ) . '<hr />' . '<br />' .
                                 '<h4 class="modal-title" style="color:#307ECC;">' . plugin_lang_get( 'description' ) . '</h4><br />' .
                                 '<textarea class="editor" id="description" name="description" cols="60" rows="10" value="">' . htmlspecialchars( $data[ 'description' ] ) . '</textarea><hr />' .
                                 '<h4 class="modal-title" style="color:#307ECC;">' . plugin_lang_get( 'keywords' ) . '</h4><br />
                                 <input type="text" class="form-control" name="keyword" id="" value="' . $data[ 'keyword' ] . '" placeholder="' . plugin_lang_get( 'type_keywords_separated_by_comas' ) . '" />
                                 </div>
                                 <input type="hidden" name="id" id="id" value="' . $data[ 'id' ] . '"/>
                                 <div class="modal-footer">
                                 <button type="submit" formaction="' . plugin_page( 'wiki_article_box' ) . '&amp;t=' . $database_choice . '&amp;id=' . $data[ 'id' ]  . '&amp;a=modify" class="w3-btn w3-green" >' . plugin_lang_get( 'modify' ) . '</button>
                                 ' . $publicate_button . '
                                 <button type="button" class="w3-btn w3-red" data-dismiss="modal">' . plugin_lang_get( 'cancel' ) . '</button>
                                 </div>
                               </div>
                             </div>
                             </form>
                           </div>';

    }

    $display .= '</div>';

    return $display;

    } else {

    return self::print_default_welcome_message( $database_choice );
    }
  }

  # Afficher tous les articles dont l'utilisateur est co-auteur dans des cartes
  /** @param user_id > ID de l'utilisateur actuel
    * @param order > Critère de tri des articles
    * @param database_choice > type d'article (article ou brouillon )
    * @return display
    **/

  public static function print_all_articles_in_cards_coauthor( $order )
  {
    $t_wiki_table = wikiArticle::get_article_table();

    if( wikiUser::user_is_coauthor() )
    {
      $current_user_id = auth_get_current_user_id();

      $coauthor_id = '#' . $current_user_id . ',';

      $query = "SELECT * FROM {$t_wiki_table} WHERE coauthor_list LIKE '%" . $coauthor_id . "%' ORDER BY " . db_param() . " ASC";
      $result = db_query( $query, array( $order ) );

      $display = self::print_view_mode_bar( 'article' );

      $display .= '<div class="col-md-9 col-xs-9 no-padding" style="top:-16px;"><div class="space-10"></div>';

      $modify_button_string = plugin_lang_get( 'modify_article' );

      $publicate_button = '';

      # Construction de la carte
      while( $data = db_fetch_array( $result ) )
      {

        $display .= '<div class="col-md-6 col-xs-6 w3-animate-opacity w3-margin" style="width:27rem; height:33rem;">
                        <div class="w3-card-2">
                            <header class="w3-container w3-light-grey w3-padding" style="display:block;">
                              <h4 class="w3-left">' . plugin_lang_get( 'shared_article_from') . '&nbsp;' . date('d-m-Y', $data[ 'timestamp' ] ) . '</h4><br />
                              <p class="">' . plugin_lang_get( 'author' ) . '&nbsp;:&nbsp;' . user_get_name( (int)$data[ 'author_id'] ) . '</p>
                            </header>
                            <div class="w3-container w3-margin">
                            <h4 class="modal-title" style="color:#307ECC;">' . plugin_lang_get( 'object' ) . '</h4>
                            <br />
                              <p>' . $data[ 'object' ] . '</p>
                            <hr />
                            <center>
                              ' . self::print_labels( $data['product'], $data['module'], $data['type'], $data['id_client'], true ) . '
                              <hr />
                              <a class="btn w3-hover-teal w3-round-small btn-sm" href="' . plugin_page( 'view') . '&amp;type=article&amp;id=' . $data[ 'id' ] . '" style="text-decoration:none;">' . plugin_lang_get( 'view') . '</a>
                            <a class="btn w3-hover-teal w3-round-small btn-sm" data-toggle="modal" data-target="#ModalModify-' . $data[ 'id' ] . '" style="text-decoration:none;">' . plugin_lang_get( 'modify') . '</a>
                            </center>
                            <div class="space-10"></div>
                        </div>
                      </div>';


            $display .= '<div id="ModalModify-' . $data[ 'id' ] . '" class="modal w3-animate-zoom" role="dialog">
                               <form id="modify_draft_form" method="post" action="">
                               <div class="modal-dialog modal-lg">
                                 <div class="modal-content form-group">
                                   <div class="modal-header w3-center">
                                     <button type="button" class="close" data-dismiss="modal">&times;</button>
                                       <h4 class="modal-title"><i class="ace-icon fa fa-pencil-square-o"></i>&nbsp;' . plugin_lang_get( 'modify_article' ) . '</h4>
                                         <hr />
                                     <h4 class="modal-title" style="color:#307ECC;">' . plugin_lang_get( 'object' ) . '</h4><br />
                                     <input class="form-control" type="text" id="object" name="object" size="50" maxlength="128" value="' . $data[ 'object' ] . '" required />
                                   </div>
                                   <div class="modal-body w3-center">
                                   <h4 class="modal-title" style="color:#307ECC;">' . plugin_lang_get( 'product' ) . '</h4><br />'
                                   . wikiArticleSetting::config_print_all_parameters_in_selectbox( 'product', $data[ 'product' ] ) . '<br />' .
                                   '<h4 class="modal-title" style="color:#307ECC;">' . plugin_lang_get( 'module' ) . '</h4><br />'
                                   . wikiArticleSetting::config_print_all_parameters_in_selectbox( 'module', $data[ 'module' ] ) . '<br />' .
                                   '<h4 class="modal-title" style="color:#307ECC;">' . plugin_lang_get( 'type' ) . '</h4><br />'
                                   . wikiArticleSetting::config_print_all_parameters_in_selectbox( 'type', $data[ 'type' ] ) . '<br />' .
                                   '<h4 class="modal-title" style="color:#307ECC;">' . plugin_lang_get( 'consumer_id' ) . '</h4><br />'
                                   . wikiConsumer::print_all_consumers_ids_in_selectbox( $data[ 'id_client'] ) . '<hr />' . '<br />' .
                                   '<h4 class="modal-title" style="color:#307ECC;">' . plugin_lang_get( 'description' ) . '</h4><br />' .
                                   '<textarea class="editor" id="description" name="description" cols="60" rows="10" value="">' . htmlspecialchars( $data[ 'description' ] ) . '</textarea><hr />' .
                                   '</div>
                                   <input type="hidden" name="id" id="id" value="' . $data[ 'id' ] . '"/>
                                   <div class="modal-footer">
                                   <button type="submit" formaction="' . plugin_page( 'wiki_article_box' ) . '&amp;t=article&amp;id=' . $data[ 'id' ]  . '&amp;a=modify" class="w3-btn w3-green" >' . plugin_lang_get( 'modify' ) . '</button>
                                   ' . $publicate_button . '
                                   <button type="button" class="w3-btn w3-red" data-dismiss="modal">' . plugin_lang_get( 'cancel' ) . '</button>
                                   </div>
                                 </div>
                               </div>
                               </form>
                             </div>';

      }

      $display .= '</div></div>';

      return $display;
    }
  }

  public static function print_view_mode_bar( $database_choice )
  {

  $user_id = auth_get_current_user_id();

  $display = '<div class="col-md-9 col-xs-9 no-padding">
                 <div class="space-10"></div>
                 <div class="w3-bar w3-card-2" style="background-color: #307ECC;">
                 <a href="' . plugin_page( 'wiki_article_box' ) . '&amp;t=' . $database_choice . '&amp;mode=self"
                  class="w3-bar-item w3-center w3-button" style="color: #FFFFFF; text-decoration: none;">
                  ' . plugin_lang_get( 'view_my_articles' ) . '&nbsp;' . wikiArticle::article_count_author_articles( $user_id, $database_choice ) . '
                 </a>';

if( wikiUser::user_is_coauthor() && $database_choice === 'article' )
    {
      $display .= '  <a href="' . plugin_page( 'wiki_article_box' ) . '&amp;t=' . $database_choice . '&amp;mode=shared"
                      class="w3-bar-item w3-center w3-button" style="color: #FFFFFF; text-decoration: none;">
                      ' . plugin_lang_get( 'view_shared_articles' ) . '&nbsp;' . wikiArticle::article_count_coauthor_articles() . '
                     </a>';

    }

    # Si l'utilisateur est autorisé à créer des articles, ajouter le lien vers la page de création
if( wikiUser::user_can_create_articles() )
   {

   $display .=   '<a href="" data-toggle="modal" data-target="#ModalAddArticle"
                   class="w3-bar-item w3-center w3-button" style="color: #FFFFFF; text-decoration: none;">
                   ' . plugin_lang_get( 'add_article' ) . '
                 </a>';
  }

  $display .= ' </div>
                <div class="space-10"></div>
              </div>';

  return $display;
  }

}



 ?>
