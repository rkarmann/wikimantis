<?php

  /** Print_api
    * 
    *
    **/

Class PrintAPI
{
  /** User menu print function
    * @param database_user_pref
    *
    **/
  public static function print_user_menu()
  {
    $user_pref_string = wikiUser::get_user_pref_string();

    if( !is_null( $user_pref_string ) && !empty( $user_pref_string ) )
      {
        $user_pref_array = explode(',', $user_pref_string );

        for( $i = 0; $i < count( $user_pref_array ); $i++ )
          {
            if( $user_pref_array[ $i ] === 0 )
              {
                $display .= self::print_carousel();
              }
            else
              {

              } # End if
          } # End for
      } # End if
  }

  public static function print_user_articles()
  {
    $user_id = auth_get_current_user_id();

    # Get tables name
    $article_table = wikiArticle::get_article_table();
    $draft_table = wikiArticle::get_draft_table();

    # Get articles count
    $article_count = wikiArticle::article_count_user_articles( $user_id, 'article' );
    $draft_count = wikiArticle::article_count_user_articles( $user_id, 'draft' );

    $display = '<div class="col-md-12 col-xs-12 no-padding">';

    if( $article_count > 0 )
      {
        $query = "SELECT * FROM {$article_table} WHERE author_id=" . db_param() . " ORDER BY timestamp ASC";
        $articles = db_query( $query, array( $user_id ) );

        $display .= '<div class="widget-box widget-color-blue no-padding">
                      <div class="widget-header widget-header-small">
                      <center>
                        <h4 class="widget-title lighter" style="color:#FFFFFF;">
                          <i class="ace icon fa fa-files-o"></i>
                          ' . plugin_lang_get( 'my_articles' ) . '
                        </h4>
                      </center>
                      </div>
                      <table class="w3-table-all w3-hoverable" style="background-color: #307ECC;>
                        <thead color: #FFFFFF;">
                          <tr>
                            <td class="w3-center">
                              <h4 class="title title-lighter">' . plugin_lang_get( 'edited_on' ) . '</h4>
                            </td>
                            <td class="w3-center">
                              <h4 class="title title-lighter">' . plugin_lang_get( 'object' ) . '</h4>
                            </td>
                            <td class="w3-center">
                              <h4 class="title title-lighter">' . plugin_lang_get( 'view' ) . '</h4>
                            </td>
                          </tr>
                        </thead>
                        <div style="overflow-y: scroll !important;max-height: 50px;">
                        <tbody>
                      ';

        while( $data = db_fetch_array( $articles ) )
          {
            $display .= ' <tr>
                            <td class="w3-center">
                             <p>' . date('d-m-Y', $data[ 'timestamp' ] ) . '</p>
                            </td>
                            <td class="w3-center">
                              <p>' . $data[ 'object' ] . '</p>
                            </td>
                            <td class="w3-center">
                              <p>
                                <a href="' . plugin_page( 'view') . '&amp;type=article&amp;id=' . $data[ 'id' ] . '" class"btn primary-btn info-btn" style="text-decoraton: none;">
                                <i class="fa fa-eye"></i>
                                </a>
                              </p>
                            </td>
                          </tr>';
          } # End while

          $display .= ' </tbody></div></table></div><div class="space-10"></div>';
      } # End if

    if( $draft_count > 0 )
      {
        $query = "SELECT * FROM {$draft_table} WHERE author_id=" . db_param() . " ORDER BY timestamp ASC";
        $drafts = db_query( $query, array( $user_id ) );

        $display .= '<div class="widget-box widget-color-blue no-padding">
                        <div class="widget-header widget-header-small">
                        <center>
                          <h4 class="widget-title lighter" style="color:#FFFFFF;">
                            <i class="ace icon fa fa-files-o"></i>
                            ' . plugin_lang_get( 'my_drafts' ) . '
                          </h4>
                        </center>
                        </div>
                        <table class="w3-table-all w3-hoverable" style="background-color: #307ECC;>
                          <thead color: #FFFFFF;">
                            <tr>
                              <td class="w3-center">
                                <h4 class="title title-lighter">' . plugin_lang_get( 'edited_on' ) . '</h4>
                              </td>
                              <td class="w3-center">
                                <h4 class="title title-lighter">' . plugin_lang_get( 'object' ) . '</h4>
                              </td>
                              <td class="w3-center">
                                <h4 class="title title-lighter">' . plugin_lang_get( 'view' ) . '</h4>
                              </td>
                            </tr>
                          </thead>
                          <tbody style="overflow-y: scroll !important;max-height: 400px;">
                        ';

        while( $data = db_fetch_array( $drafts ) )
          {
            $display .= ' <tr>
                            <td class="w3-center">
                             <p>' . date('d-m-Y', $data[ 'timestamp' ] ) . '</p>
                            </td>
                            <td class="w3-center">
                              <p>' . $data[ 'object' ] . '</p>
                            </td>
                            <td class="w3-center">
                              <p>
                                <a href="' . plugin_page( 'view') . '&amp;type=article&amp;id=' . $data[ 'id' ] . '" class"btn btn-primary btn-info">
                                <p><i class="fa fa-eye"></i></p>
                                </a>
                              </p>
                            </td>
                          </tr>';
          } # End while

          $display .= ' </tbody></table></div>';
      } # End if

      $display .= '</div>';

      return $display;
  }

  public static function print_carousel()
  {
      # Select article table
    $article_table = wikiArticle::get_article_table();

      # if there is enough articles
    if( wikiArticle::article_count_all() > 4)
      {
        # Build SQL query to get the five last entries in the DB
        # Order by ID (desc)
      $query = "SELECT * FROM {$article_table} WHERE id > (SELECT MAX(id) - 5 FROM {$article_table}) ORDER BY id DESC";
      $result = db_query( $query );

        # Building the Carousel container
      $display = '<div id="myCarousel" class="carousel slide" data-ride="carousel">
                      <ol class="carousel-indicators">
                        <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
                        <li data-target="#myCarousel" data-slide-to="1"></li>
                        <li data-target="#myCarousel" data-slide-to="2"></li>
                        <li data-target="#myCarousel" data-slide-to="3"></li>
                        <li data-target="#myCarousel" data-slide-to="4"></li>
                      </ol>
                      <div class="carousel-inner">';

        # Add an iterator to set items
      $iterator = 0;

        # Fetching the array to create Carousel's items
      while( $data = db_fetch_array( $result ) )
        {
          # Get category name
          $t_parameter_name = wikiArticleSetting::get_parameter_by_id( 'product', $data[ 'product' ] );

          $iterator += 1;

          if ( $iterator === 1)
            {

              # Add the active class if it's the first loop
            $display .= '<div class="item active" style="text-align:center;">
                              <center>
                              <img src="' . plugin_file( 'image-1.jpg' ) . '" alt="Article" style="width:100%;height:100%;display:block;">
                              </center>
                            <div class="carousel-caption">
                              <h3>' . $t_parameter_name . '</h3>
                              <a href="' . plugin_page( 'view' ) . '&amp;type=article&amp;id=' . $data[ 'id' ] .'" style="text-decoration:none;">
                              <p>' . $data[ 'object' ] . '</p>
                              </a>
                            </div>
                            </div>
                            ';
            } else {

             # Define image name accordingly to the iterator value
          $image_name = 'image-' . $iterator . '.jpg';

            # Add 4 last items (4 last loops)
          $display .= '<div class="item">
                          <img src="' . plugin_file( $image_name ) . '" alt="Article" style="width:100%;height:100%;display:block;">
                          <div class="carousel-caption">
                          <h3>' . $t_parameter_name . '</h3>
                          <a href="' . plugin_page( 'view' ) . '&amp;type=article&amp;id=' . $data[ 'id' ] .'" style="text-decoration:none;">
                          <p>' . $data[ 'object' ] . '</p>
                          </a>
                          </div>
                          </div>';
           }
        }

        # Add arrows on the image
      $display .= '</div>
                      <a class="left carousel-control" href="#myCarousel" data-slide="prev">
                      <span class="glyphicon glyphicon-chevron-left"></span>
                      <span class="sr-only">Previous</span>
                      </a>
                      <a class="right carousel-control" href="#myCarousel" data-slide="next">
                      <span class="glyphicon glyphicon-chevron-right"></span>
                      <span class="sr-only">Next</span>
                      </a>
                      </div>';

        # return Carousel once is built
      return $display;

      } else {


          # Carousel can't be displayed at this time
        return '<center><h4>' . plugin_lang_get( 'create_more_articles' ) . '</h4></center>';

      }
  }
}
