<?php

html_robots_noindex();

layout_page_header_begin(plugin_lang_get('main_page_title'));

layout_page_header_end();

layout_page_begin( __FILE__ );

if( wikiUser::user_is_registered( auth_get_current_user_id() ) ){

include 'wiki_sidebar.php';

?>
<div class="col-md-9 col-xs-9 no-padding">

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

  <div class="col-md-12 col-xs-12 w3-animate-opacity no-padding">
  <div class="space-10"></div>
    <div class="widget-box widget-color-blue no-padding">
      <div class="widget-header widget-header-small">
      <center>
        <h4 class="widget-title lighter" style="color:#FFFFFF;">
          <i class="ace icon fa fa-wikipedia-w"></i>
          <?php echo plugin_lang_get( 'welcome_wiki_message' ); ?>
        </h4>
      </center>
      </div>
    <div class="widget-toolbox clearfix">
    <?php echo PrintAPI::print_carousel(); ?>
    </div>

  </div>

  <div class="space-10"></div>
  </div>
  <?php echo PrintAPI::print_user_articles(); ?>
</div>
<?php

} else {

  echo wikiUser::print_user_not_registered_message();

}

layout_page_end();

?>
