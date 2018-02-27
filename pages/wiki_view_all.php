<?php

html_robots_noindex();

layout_page_header_begin(plugin_lang_get('view_all_articles'));

layout_page_header_end();

layout_page_begin( __FILE__ );

if( wikiUser::user_is_registered( auth_get_current_user_id() ) ){


  $type = ( isset( $_GET[ 't' ] ) ) ? $_GET[ 't' ] : NULL;
  $id = ( isset( $_GET[ 'id' ] ) ) ? $_GET[ 'id' ] : NULL;
  $sub_type = ( isset( $_GET[ 'st' ] ) ) ? $_GET[ 'st' ] : NULL;
  $sub_id = ( isset ( $_GET[ 'sid' ] ) ) ? $_GET[ 'sid' ] : NULL;
  $order = ( isset( $_GET[ 'ord'] ) ) ? $_GET[ 'ord' ] : 'product';

  include 'wiki_sidebar.php';

?>

<div class="col-md-9 col-xs-9 w3-animate-opacity">
  <div class="space-10"></div>
    <div class="widget-box no-padding widget-color-blue">
      <div class="widget-header widget-header-small">
      <center>
        <h4 class="widget-title lighter" style="color:#FFFFFF;">
          <i class="ace icon fa fa-newspaper-o"></i>
          <?php echo plugin_lang_get('view_all_articles'); ?>
        </h4>
      </center>
      </div>
      <div class="widget-toolbox clearfix no-padding">

      <?php

      if( !is_null($type) && !is_null($id) && is_null($sub_type) && is_null($sub_id) ){

      echo wikiArticle::get_articles_and_display_all( $order, $type, $id );

      } elseif( !is_null($type) && !is_null($id) && !is_null($sub_type) && !is_null($sub_id) ){

      echo wikiArticle::get_articles_and_display_all( $order, $type, $id, $sub_type, $sub_id );

      } elseif ( !is_null( $type ) && !is_null( $id ) && $type === 'id_client' )
      {
      echo wikiArticle::get_articles_and_display_all( $order, NULL, NULL, NULL, NULL, $id );
      } else {

      echo wikiArticle::get_articles_and_display_all( $order );

      }

      ?>

    </div>
  </div>
</div>

<?php

} else {

  echo wikiUser::print_user_not_registered_message();

}

layout_page_end();

 ?>
