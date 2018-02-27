<?php

    /** @subpackage wiki_add_article.php
     * Page de récupération du formulaire
     * Prévisualisation de l'article
     * @uses article_api.php
     * @uses print_article_api.php
     * @uses wiki_sidebar.php
     **/
 if( wikiUser::user_is_registered( auth_get_current_user_id() ) ){

# Récupération du type de création ('article' ou 'draft')
$t_type = ( isset( $_GET[ 'type' ] ) ) ? $_GET[ 'type' ] : '';

  # Si c'est un article
  if( $t_type === 'article' ){

    # Récupération des champs du formulaire de création
    $product = ( isset( $_POST['product'] ) ) ? $_POST['product'] : NULL ;
    $module = ( isset( $_POST['module'] ) ) ? $_POST['module'] : NULL;
    $type = ( isset( $_POST['type'] ) ) ? $_POST['type'] : NULL;
    $object = ( isset( $_POST['object'] ) ) ? $_POST['object'] : NULL;
    $description = ( isset( $_POST[ 'description' ] ) ) ? $_POST['description'] : NULL;
    $private = ( isset( $_POST['private'] ) ) ? $_POST['private'] : 1 ;
    $id_client = ( isset( $_POST[ 'id_client' ] ) ) ? $_POST[ 'id_client' ] : NULL;

    # Récupération de l'ID de l'auteur
    $author_id = auth_get_current_user_id();

    if( $product != NULL && $module != NULL && $type != NULL && $object != NULL
        && $description != NULL)
    {
    # Construction des données de l'article
    $t_article = new WikiArticle( $product, $module, $type, $object,
                                  $description, $author_id, $private, $id_client );

    # Sauvegarde de l'article en base de données
    $t_article_id = $t_article->save();

    # Construction graphique de l'article
    $t_article_card = new wikiArticleCard();

    }

    # Si c'est un brouillon
    } elseif( $t_type === 'draft' ){

    # Récupération des champs du formulaire de création
    $product = ( isset( $_POST['product'] ) ) ? $_POST['product'] : NULL ;
    $module = ( isset( $_POST['module'] ) ) ? $_POST['module'] : NULL;
    $type = ( isset( $_POST['type'] ) ) ? $_POST['type'] : NULL;
    $object = ( isset( $_POST['object'] ) ) ? $_POST['object'] : NULL;
    $description = ( isset( $_POST['description_a'] ) ) ? $_POST['description_a'] : NULL;
    $private = ( isset( $_POST['private'] ) ) ? $_POST['private'] : 1 ;
    $id_client = ( isset( $_POST[ 'id_client' ] ) ) ? $_POST[ 'id_client' ] : NULL;

    # Récupération de l'ID de l'auteur
    $author_id = auth_get_current_user_id();

    if( $product != NULL && $module != NULL && $type != NULL
        && $object != NULL && $description != NULL )
    {
    # Construction des données de l'article
    $t_draft = new WikiArticle( $product, $module, $type, $object,
                                $description, $author_id, $private, $id_client);

    # Sauvegarde de l'article en base de données
    $t_article_id = $t_draft->save_as_draft();

    # Construction graphique de l'article
    $t_article_card = new wikiArticleCard();

    }

  }

  }

  html_robots_noindex();

  layout_page_header( plugin_lang_get( 'plugin_title' ) );

  layout_page_begin( __FILE__ );

  if( wikiUser::user_is_registered( auth_get_current_user_id() ) ){

  $article_label = ( $t_type === 'draft' ) ? 'Le brouillon&nbsp;' : 'L\'article&nbsp;';

?>
  <div class="col-md-12 col-xs-12 w3-animate-opacity">
    <div class="space-10"></div>
      <div class="alert alert-success">
        <strong><i class="ace-icon fa fa-plus-square"></i>&nbsp;Création terminée:</strong>  <?php echo $article_label; echo $t_article_id; ?> a bien été créé.
      </div>
  </div>
<?php

  include 'wiki_sidebar.php';

  # Appel de la fonction de prévisualisation
  echo $t_article_card->print_article_by_id( $t_article_id, $t_type );

  } else {

    echo wikiUser::print_user_not_registered_message();

  }

  layout_page_end();

 ?>
