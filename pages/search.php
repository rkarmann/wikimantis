<?php

html_robots_noindex();

layout_page_header( plugin_lang_get( 'search' ) );

layout_page_begin();

if( wikiUser::user_is_registered( auth_get_current_user_id() ) ){

    $query = ( isset( $_POST[ 'keywords' ] ) && !empty( $_POST[ 'keywords' ] ) ) ? trim( strtolower( $_POST[ 'keywords' ] ) ) : NULL;

    include 'wiki_sidebar.php';
    ?>
    <div class="col-md-9 col-xs-9 w3-animate-opacity">
      <div class="space-10"></div>

      <div class="col-md-12 col-xs-12 no-padding">
        <div class="space-10"></div>
        <div class="w3-bar w3-card-2 wiki-background-color">
            <center>
              <table>
                <tr class="wiki-background-color">
                  <form action="<?php echo plugin_page( 'search'); ?>" method="post">
                  <td width="30%">
                    <h5 class="w3-bar-item"><i class="fa fa-search"></i>&nbsp;<?php echo plugin_lang_get( 'search_by_keyword' ); ?>&nbsp;:</h5>
                  </td>
                  <td width="70%">
                    <input class"form-control w3-bar-item" type="text" name="keywords" id="keywords" size="65" value="" placeholder="<?php echo plugin_lang_get( 'type_your_research') ?>" autocomplete="off"/>
                  </td>
                </form>
                </tr>
              </table>
            </center>
        </div>
        <div class="result" id"result" name="result">
        </div>
      </div>

      <div class="col-md-12 col-xs-12 no-padding">
        <div class="widget-box no-padding widget-color-blue">
          <div class="widget-header widget-header-small">
            <center>
              <h4 class="widget-title lighter" style="color:#FFFFFF;">
                <i class="ace icon fa fa-search"></i>
                <?php echo plugin_lang_get( 'search' ); ?>
              </h4>
            </center>
          </div>
          <div class="widget-toolbox clearfix no-padding">
            <?php
            if( !is_null( $query ) )
            {
              echo wikiSearch::print_search_table( $query );
            }
            ?>
          </div>
        </div>
      </div>
    </div>


<?php

} else {

  echo wikiUser::print_user_not_registered_message();
}

layout_page_end();

?>
