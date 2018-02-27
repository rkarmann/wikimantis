<?php

html_robots_noindex();

layout_page_header( plugin_lang_get( 'plugin_title' ) );

layout_page_begin( __FILE__ );

include 'wiki_sidebar.php';

$type = ( isset( $_GET[ 'type' ] ) ) ? $_GET[ 'type' ] : '';

$article_id = ( isset( $_GET[ 'id' ] ) ) ? $_GET[ 'id' ] : 0;

$t_article = new wikiArticle();

$article_exists = $t_article->article_exists( $article_id, $type );

if( $type === 'article' && $article_exists ){

  $t_article = new wikiArticleCard();

  echo $t_article->print_article_by_id( $article_id, 'article' );

} elseif ($type === 'draft' && $article_exists ) {

  $t_draft = new wikiArticleCard();

  echo $t_draft->print_article_by_id( $article_id, 'draft' );

} else {


}



?>

<?php

layout_page_end();

?>
