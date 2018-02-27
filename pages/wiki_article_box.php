<?php

html_robots_noindex();

layout_page_header( plugin_lang_get( 'my_articles' ) );

layout_page_begin( __FILE__ );

  if( wikiUser::user_is_registered( auth_get_current_user_id() ) )
    {

    # Récupération des paramètres du GET
    $t_type = ( isset( $_GET[ 't' ] ) ) ? $_GET[ 't' ] : NULL;
    $id = ( isset( $_GET[ 'id' ] ) ) ? $_GET[ 'id' ] : NULL;
    $action = ( isset( $_GET[ 'a' ] ) ) ? $_GET[ 'a' ] : NULL;
    $mode = ( isset( $_GET[ 'mode' ] ) ) ? $_GET[ 'mode' ] : NULL;

    # Mode affichage des articles
    if( !is_null( $t_type ) && !is_null( $mode ) && is_null( $action ) )
      {
        if( $mode === 'self' )
          {
          # Si le mode d'affichage est 'self', n'afficher que les articles dont l'utilisateur est l'auteur
          include 'wiki_sidebar.php';
          echo wikiArticleCard::print_all_articles_in_cards( $t_type, 'id' );
          }
        elseif( $mode === 'shared' )
          {
          # Si le mode d'affichage est 'shared', n'afficher que les articles dont l'utilisateur est le co-auteur
          include 'wiki_sidebar.php';
          echo wikiArticleCard::print_all_articles_in_cards_coauthor( 'id' );
          }

      }
    elseif( !is_null( $action ) && !is_null( $t_type ) )
      {

      switch( $action )
        {

          case 'create':

          # Récupération des champs du formulaire de création
          $product = ( isset( $_POST['product'] ) ) ? $_POST['product'] : NULL ;
          $module = ( isset( $_POST['module'] ) ) ? $_POST['module'] : NULL;
          $type = ( isset( $_POST['type'] ) ) ? $_POST['type'] : NULL;
          $object = ( isset( $_POST['object'] ) ) ? $_POST['object'] : NULL;
          $description = ( isset( $_POST[ 'description' ] ) ) ? trim( $_POST['description'] ) : NULL;
          $solution = ( isset( $_POST[ 'solution' ] ) ) ? trim( $_POST[ 'solution' ] ) : NULL;
          $private = ( isset( $_POST['private'] ) ) ? $_POST['private'] : 1 ;
          $id_client = ( isset( $_POST[ 'id_client' ] ) ) ? $_POST[ 'id_client' ] : 999999;
          $keyword = ( isset( $_POST[ 'keyword' ] ) ) ? trim( strtolower( $_POST[ 'keyword' ] ) ) : NULL;

          # Récupération de l'ID de l'auteur
          $author_id = auth_get_current_user_id();

          # Construction des données de l'article
          $t_article = new WikiArticle( $product, $module, $type, $object,
                                        $description, $solution, $author_id, $private, $id_client, $keyword );

          # Sauvegarde de l'article en base de données
          $t_article_id = $t_article->save( $t_type );
          $result = ( !empty($t_article_id) ) ? 'created' : 'error_creation';

          echo wikiArticleSetting::print_successful_message( $result, $t_type );
          include 'wiki_sidebar.php';
          echo wikiArticleCard::print_all_articles_in_cards( $t_type, 'id' );

          break;

          # Pour modifier l'article
          case 'modify':

          $object = (isset( $_POST[ 'object' ] ) ) ? $_POST[ 'object' ] : NULL;
          $product =(isset ( $_POST[ 'product' ] ) ) ? $_POST[ 'product' ] : NULL;
          $module = (isset( $_POST[ 'module' ] ) ) ? $_POST[ 'module' ] : NULL;
          $type = (isset( $_POST[ 'type' ] ) ) ? $_POST[ 'type' ] : NULL;
          $id_client = (isset( $_POST[ 'id_client'] ) ) ? $_POST[ 'id_client' ] : 999999;
          $description = (isset( $_POST[ 'description' ] ) ) ? $_POST[ 'description' ] : NULL;
          $solution = (isset( $_POST[ 'solution' ] ) ) ? $_POST[ 'solution' ] : NULL;
          $private = (isset( $_POST[ 'private' ] ) ) ? $_POST[ 'private' ] : NULL;
          $author_id = auth_get_current_user_id();
          $keyword = ( isset( $_POST[ 'keyword' ] ) ) ? trim( strtolower( $_POST[ 'keyword' ] ) ) : NULL;
          # Construction des données de l'article
          $t_article = new WikiArticle( $product, $module, $type, $object, $description,
                                        $solution, $author_id, $private, $id_client, $keyword );


          # Mise à jour de l'article en base de données
          $article_id = $t_article->update( $id, $t_type );
          $result = ( !is_null( $article_id ) ) ? 'modified' : 'error_modification';
          echo wikiArticleSetting::print_successful_message( $result, $t_type );


          include 'wiki_sidebar.php';
          echo wikiArticleCard::print_all_articles_in_cards( $t_type, 'id' );

          break;

          # Pour supprimer l'article
          case 'delete':

            if( !is_null( wikiArticle::delete_wiki_by_id( $t_type, $id ) ) )
            {
              echo wikiArticleSetting::print_successful_message( 'deleted', $t_type );
              include 'wiki_sidebar.php';
              echo wikiArticleCard::print_all_articles_in_cards( $t_type, 'id' );

            } else {

              echo wikiArticleSetting::print_successful_message( 'error_deletion', $t_type );
              include 'wiki_sidebar.php';
              echo wikiArticleCard::print_all_articles_in_cards( $t_type, 'id' );
            }

          break;

          case 'set_coauthor':

          $new_coauthor = ( isset( $_POST[ 'username' ] ) ) ? $_POST[ 'username' ] : NULL;
          $t_id = wikiArticle::article_set_coauthor( $id, $new_coauthor );
          $result = ( !is_null( $t_id ) ) ? 'shared' : 'error_sharing';
          echo wikiArticleSetting::print_successful_message( $result, $t_type );
          include 'wiki_sidebar.php';
          echo wikiArticleCard::print_all_articles_in_cards( $t_type, 'id' );

          break;

        case 'unset_coauthor':

          $coauthor_id = ( isset( $_POST[ 'coauthor_id' ] ) ) ? $_POST[ 'coauthor_id' ] : NULL;
          $t_id = wikiArticle::article_unset_coauthor( $coauthor_id, $id );
          $result = ( !is_null( $t_id ) ) ? 'unshared' : 'error_unsharing';
          echo wikiArticleSetting::print_successful_message( $result, $t_type );
          include 'wiki_sidebar.php';
          echo wikiArticleCard::print_all_articles_in_cards( $t_type, 'id' );

          break;
        } # Fin du switch

      }
    else
      {

      # Ne rien faire...
      }

    }
  else
    {

      echo wikiUser::print_user_not_registered_message();

    }

layout_page_end();

?>
