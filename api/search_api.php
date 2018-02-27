<?php


class wikiSearch
{

  public static function search_article( $keywords )
  {
    $article_table = wikiArticle::get_article_table();

    $key = explode( " ", $keywords );

    if( count( $key ) > 0 ) {

      $query = "SELECT * FROM {$article_table} WHERE ";

      for( $i = 0; $i < count( $key ); $i++ ) {

        $query .= "object LIKE '%" . $key[ $i ] . "%' OR keyword LIKE '%" . $key[ $i ] . "%'";

        if( $i < ( count( $key ) - 1 ) ) {

        $query .= " AND ";

        }

      }

        $query .= " ORDER BY id DESC";

        $result = db_query( $query );

        $display = '<div class="space-10"></div>
                    <table class="w3-table-all w3-hoverable">
                      <thead class="wiki-color">
                        <tr>
                          <th class="w3-center">
                          ' . plugin_lang_get( 'product' ) . '
                          </th>
                          <th class="w3-center">
                          ' . plugin_lang_get( 'module' ) . '
                          </th>
                          <th class="w3-center">
                          ' . plugin_lang_get( 'type') . '
                          </th>
                          <th class="w3-center">
                          ' . plugin_lang_get( 'object' ) . '
                          </th>
                        <tr>
                       </thead>
                       <tbody id="searchTable">';

          while( $data = db_fetch_array( $result) )
          {
            $display .= '<tr>
                          <td class="w3-center w3-padding-16">
                          ' . wikiArticleSetting::get_parameter_by_id( 'product' , $data[ 'product' ] ) . '
                          </td>
                          <td class="w3-center w3-padding-16">
                          ' . wikiArticleSetting::get_parameter_by_id( 'module' , $data[ 'module' ] ) . '
                          </td>
                          <td class="w3-center w3-padding-16">
                          ' . wikiArticleSetting::get_parameter_by_id( 'type' , $data[ 'type' ] ) . '
                          </td>
                          <td class="w3-center w3-padding-16">
                          <a href="' . plugin_page( 'view' ) . '&amp;type=article&amp;id=' . $data[ 'id' ] . '" style="text-decoration= none;">
                          ' . $data[ 'object' ] . '
                          </a>
                          </td>
                        </tr>
                          ';
          }

          $display .= '</tbody></table>';

          return $display;

        } else {

        $display = self::no_query_message();

        return $display;

        }
  }

  public static function print_search_table( $query )
  {

    $display = '<div class="col-md-12 col-xs-12 no-padding">';

    $display .= self::search_article( $query );

    $display .= '</div>';

    return $display;
  }

  public static function no_query_message()
  {
    $display = '<div class="col-md-12 col-xs-12 no-padding">
                  <div class="w3-center">
                    ' . plugin_lang_get( 'no_query' ) . '
                  </div>
                </div>';

    return $display;
  }

}


?>
