<?php

html_robots_noindex();

layout_page_header( plugin_lang_get( 'plugin_title' ) );

layout_page_begin( __FILE__ );

if ( wikiUser::user_is_registered( auth_get_current_user_id() ) ){

# Appel de l'objet wikiArticleSetting
$t_setting = new wikiArticleSetting();

# Appel de l'objet wikiConsumer
$t_consumer = new wikiConsumer();

# Appel de l'objet wikiContact
$t_contact = new wikiContact();

# Appel de l'objet wikiConnection
$t_connection = new wikiConnection();

  # Choix de la configuration
  /**
    * Les paramètres sont retournés dans une chaine de caractère divisée par des ','
    * et cachée dans un input (config_api/get_categories_and_display_it). Le reste est récupéré dans la form (method POST)
    * @param #1 -> array [0] = action (product, module, type, consumer, connection, contact)
    * @param #2 -> array [1] = type d'action (delete, modify, create)
    * @param #3 -> array [2] = id (par défaut 0)
    **/

  $parameters = ( isset($_POST[ 'parameters' ]) ) ? $_POST[ 'parameters' ] : NULL;

  $t_result = '';

  $_div_product = '';
  $_div_module = '';
  $_div_type = '';
  $_div_contact = '';
  $_div_consumer = '';
  $_div_connection = '';
  $_div_item = '';
  $_div_group = '';


    if( !is_null($parameters) )
    {
      $array = wikiArticleSetting::config_explode_parameters( $parameters );

      $type   = $array[0]; # type de paramètre
      $action = $array[1]; # action à réaliser
      $id     = $array[2]; # ID du paramètre

      switch( $type ){

        case 'product': $_div_product = 'in';       break;

        case 'module': $_div_module = 'in';         break;

        case 'type': $_div_type = 'in';             break;

        case 'contact': $_div_contact = 'in';       break;

        case 'connection': $_div_connection = 'in'; break;

        case 'consumer': $_div_consumer = 'in';     break;

        case 'item': $_div_item = 'in';             break;

        case 'group' : $_div_group = 'in';          break;
      }

      if( $type != NULL)
      {
        # Si l'ID est différent de 0 alors c'est une mise à jour
        if( $type === 'product' || $type === 'module' || $type === 'type' )
        {
          $name = ( isset($_POST[ 'name' ]) ) ? $_POST[ 'name' ] : NULL;
          $color = ( isset($_POST[ 'color']) ) ? '#' . $_POST[ 'color' ] : NULL;

          switch( $type )
          {
            case 'product':
            $id_parameter = NULL;
            break;
            case 'module':
            $id_parameter = ( isset( $_POST[ 'product' ] ) ) ? $_POST[ 'product' ] : NULL;
            break;
            case 'type':
            $id_parameter = ( isset( $_POST[ 'module' ] ) ) ? $_POST[ 'module' ] : NULL;
            break;
          }

          switch ( $action )
          {
            case 'create':

            $setting = new wikiArticleSetting( $type, $name, $color, $id_parameter );
            $t_id = $setting->save();
            $t_result = ( !empty($t_id) ) ? 'created' : 'error_creation';
            echo wikiArticleSetting::print_successful_message( $t_result, $type );

            break;
            case 'modify':

            $setting = new wikiArticleSetting( $type, $name, $color, $id_parameter );
            $t_id = $setting->update( $id );
            $t_result = ( !empty($t_id) ) ? 'modified' : 'error_modification';
            echo wikiArticleSetting::print_successful_message( $t_result, $type );

            break;
            case 'delete':

            $t_id = $t_setting->config_delete_parameter_by_id( $type, $id );
            $t_result = ( !empty($t_id) ) ? 'deleted' : 'error_deletion';
            echo wikiArticleSetting::print_successful_message( $t_result, $type );

            break;
            default:
            break;
          }

        } elseif( $type === 'consumer' )
        {
          $name = ( isset($_POST[ 'name' ]) ) ? $_POST[ 'name' ] : NULL;
          $logo = 'Aucun';

          switch ( $action )
          {
            case 'create':

            $consumer = new wikiConsumer( $name, $logo );
            $t_id = $consumer->save();
            $t_result = ( !empty($t_id) ) ? 'created' : 'error_creation';
            echo wikiArticleSetting::print_successful_message( $t_result, $type );

            break;
            case 'modify':

            $consumer = new wikiConsumer( $name, $logo );
            $t_id = $consumer->update( $id );
            $t_result = ( !empty($t_id) ) ? 'modified' : 'error_modification';
            echo wikiArticleSetting::print_successful_message( $t_result, $type );

            break;
            case 'delete':

            $t_id = $t_consumer->consumer_delete_by_id( $id );
            $t_result = ( !empty($t_id) ) ? 'deleted' : 'error_deletion';
            echo wikiArticleSetting::print_successful_message( $t_result, $type );

            break;
          }

        } elseif( $type === 'connection' )
        {
          $product = ( isset($_POST[ 'product' ]) ) ? $_POST[ 'product' ] : NULL;
          $id_client = ( isset($_POST[ 'id_client' ]) ) ? $_POST[ 'id_client' ] : NULL;
          $category = ( isset($_POST[ 'category' ]) ) ? $_POST[ 'category' ] : NULL;
          $title = ( isset($_POST[ 'title' ]) ) ? $_POST[ 'title' ] : NULL;
          $description = ( isset($_POST[ 'description' ]) ) ? $_POST[ 'description' ] : NULL;

          switch ( $action )
          {
            case 'create':

            $connection = new wikiConnection( $product, $id_client, $category, $title, $description );
            $t_id = $connection->save();
            $t_result = ( !empty( $t_id ) ) ? 'created' : 'error_creation';
            echo wikiArticleSetting::print_successful_message( $t_result, $type );

            break;
            case 'modify':

            $connection = new wikiConnection( $product, $id_client, $category, $title, $description );
            $t_id = $connection->update( $id );
            $t_result = ( !empty( $t_id ) ) ? 'modified' : 'error_modification';
            echo wikiArticleSetting::print_successful_message( $t_result, $type );

            break;
            case 'delete':

            $t_id = $t_connection->connection_delete_by_id( $id );
            $t_result = ( !empty( $t_id ) ) ? 'deleted' : 'error_deletion';
            echo wikiArticleSetting::print_successful_message( $t_result, $type );

            break;
          }

        } elseif( $type === 'contact' )
        {
          $firstname = ( isset($_POST[ 'firstname' ]) ) ? trim($_POST[ 'firstname' ] ): NULL;
          $lastname = ( isset($_POST[ 'lastname' ]) ) ? trim($_POST[ 'lastname' ]) : NULL;
          $phone = (( isset($_POST[ 'indicatif_ph' ]) ) ? trim($_POST[ 'indicatif_ph' ]) : NULL) . ' ' .
                   (( isset($_POST[ 'phone' ]) ) ? trim($_POST[ 'phone' ]) : NULL);
          $mobile = (( isset($_POST[ 'indicatif_mb' ]) ) ? trim($_POST[ 'indicatif_mb' ]) : NULL) . ' ' .
                   (( isset($_POST[ 'mobile' ]) ) ? trim($_POST[ 'mobile' ]) : NULL);
          $address = ( isset($_POST[ 'address' ]) ) ? trim($_POST[ 'address' ]) : NULL;
          $mail = (( isset($_POST[ 'mail_p' ]) ) ? trim($_POST[ 'mail_p' ]) : NULL) . '@' .
                  (( isset($_POST[ 'mail_s' ]) ) ? trim($_POST[ 'mail_s' ]) : NULL);
          $function = ( isset($_POST[ 'function' ]) ) ? trim($_POST[ 'function' ]) : NULL;
          $id_client = ( isset($_POST[ 'id_client' ]) ) ? trim($_POST[ 'id_client' ]) : NULL;
          $contact_type = ( isset( $_POST[ 'contact_type' ] ) ) ? trim($_POST[ 'contact_type'] ) : NULL;

          switch ( $action )
          {
            case 'create':

            $contact = new wikiContact( $firstname, $lastname, $phone, $mobile, $mail, $address, $function, $id_client, $contact_type );
            $t_id = $contact->save();
            $t_result = ( !empty($t_id) ) ? 'created' : 'error_creation';
            echo wikiArticleSetting::print_successful_message( $t_result, $type );

            break;
            case 'modify':

            $contact = new wikiContact( $firstname, $lastname, $phone, $mobile, $mail, $address, $function, $id_client, $contact_type );
            $t_id = $contact->update( $id );
            $t_result = ( !empty($t_id) ) ? 'modified' : 'error_modification';
            echo wikiArticleSetting::print_successful_message( $t_result, $type );

            break;
            case 'delete':

            $t_id = $t_contact->contact_delete_by_id( $id );
            $t_result = ( !empty($t_id) ) ? 'deleted' : 'error_deletion';
            echo wikiArticleSetting::print_successful_message( $t_result, $type );

            break;
          }

        } elseif( $type === 'item' ) {

          $name = ( isset( $_POST[ 'name' ] ) ) ? trim( $_POST[ 'name' ] ) : NULL;
          $icon = ( isset( $_POST[ 'icon' ] ) ) ? trim( $_POST[ 'icon' ] ) : NULL;

          switch ( $action )
          {
            case 'create':

            $item = new wikiDynamicItem( $name, $icon );
            $t_id = $item->save_item();
            $t_result = ( !empty( $t_id ) ) ? 'created' : 'error_creation';
            echo wikiArticleSetting::print_successful_message( $t_result, $type );

            break;
            case 'modify':

            $item = new wikiDynamicItem( $name, $icon );
            $t_id = $item->update_item( $id );
            $t_result = ( !empty( $t_id ) ) ? 'modified' : 'error_modification';
            echo wikiArticleSetting::print_successful_message( $t_result, $type );

            break;
            case 'delete':

            $t_id = $t_item->item_delete_by_id( $id );
            $t_result = ( !empty( $t_id ) ) ? 'deleted' : 'error_deletion';
            echo wikiArticleSetting::print_successful_message( $t_result, $type );

            break;
          }
        } elseif( $type === 'group' ){

          $name = ( isset( $_POST[ 'name' ] ) ) ? trim( $_POST[ 'name' ] ) : NULL;

          switch( $action )
          {

            case 'create':

            $t_id = wikiUser::save_group( $name );
            $t_result = ( !empty( $t_id ) ) ? 'created' : 'error_creation';
            echo wikiArticleSetting::print_successful_message( $t_result, $type );

            break;
            case 'modify':

            $t_id = wikiUser::update_group( $name, $id );
            $t_result = ( !empty( $t_id ) ) ? 'modified' : 'error_modification';
            echo wikiArticleSetting::print_successful_message( $t_result, $type );

            break;
            case 'delete':

            $t_id = wikiUser::group_delete_by_id( $id );
            $t_result = ( !empty( $t_id ) ) ? 'deleted' : 'error_deletion';
            echo wikiArticleSetting::print_successful_message( $t_result, $type );

            break;
          }
        } elseif( $type === 'user' ){

        $group_id = ( !empty( $id ) ) ? $id : NULL;
        $user_article_rights = ( isset( $_POST[ 'article_rights' ] ) ) ? $_POST[ 'article_rights' ] : NULL;
        $user_connection_rights = ( isset( $_POST[ 'connection_rights' ] ) ) ? $_POST[ 'connection_rights' ] : NULL;
        $user_contact_rights = ( isset( $_POST[ 'contact_rights' ] ) ) ? $_POST[ 'contact_rights' ] : NULL;
        $user_email = ( isset( $_POST[ 'user_email' ] ) ) ? trim($_POST[ 'user_email'] ) : NULL;
        $user_id = !is_null( $user_email) ? user_get_id_by_email( $user_email ) : NULL;
        $user_is_admin = ( isset( $_POST[ 'user_is_admin' ] ) ) ? $_POST[ 'user_is_admin' ] : NULL;

        switch( $action )
        {
            case 'create':

            $user = new wikiUser( $user_id, $group_id, $user_is_admin, $user_article_rights, $user_connection_rights, $user_contact_rights );
            $t_id = $user->save_user();
            $t_result = ( !empty( $t_id ) ) ? 'created' : 'error_creation';
            echo wikiArticleSetting::print_successful_message( $t_result, $type );

            break;
            case 'modify':

            $user = new wikiUser( $user_id, $group_id, $user_is_admin, $user_article_rights, $user_connection_rights, $user_contact_rights );
            $t_id = $user->update( $user_id );
            $t_result = ( !empty( $t_id ) ) ? 'created' : 'error_modification';
            echo wikiArticleSetting::print_successful_message( $t_result, $type );

            break;
            case 'delete':

            $t_id = wikiUser::user_delete_by_id( $id );
            $t_result = ( !empty( $t_id ) ) ? 'deleted' : 'error_deletion';
            echo wikiArticleSetting::print_successful_message( $t_result, $type );

            break;
        }

        } else {
          # Ne rien faire...
        }
      }

    } else {
      # Ne rien faire
    }

?>

  <!-- Header de la page -->

<div class="col-md-12 col-xs-12 no-padding" >
  <div class="space-10"></div>
  	<div class="widget-box widget-color-blue w3-card-2">
  		<div class="widget-header widget-header-small w3-display-container">
        <h4 class="widget-title lighter w3-animate-opacity w3-display-left w3-padding" style="color:#FFFFFF;">
          <a href="<?php echo plugin_page( 'wiki_main' ); ?>" style="text-decoration:none;color:#FFFFFF;">
            <i class="ace-icon fa fa-home"></i>&nbsp;<?php echo plugin_lang_get( 'home_link' ); ?>
          </a>
  			</h4>
        <center>
  			<h4 class="widget-title lighter w3-animate-opacity w3-display-center" style="color:#FFFFFF;">
  				<i class="ace-icon fa fa-cogs"></i>
  				<?php echo plugin_lang_get( 'config_title' ) ?>
  			</h4>
      </center>
  		</div>
    </div>
  </div>

  <div class="space-10"></div>

    <div class="col-md-12 col-xs-12 no-padding panel-group" id="collapseControl">

      <div class="space-10"></div>

      <!-- Module des produits -->
      <div class="col-md-12 col-xs-12 no-padding">
      <div class="col-md-4 col-xs-4 no-padding">
        <div class="widget-box widget-color-blue w3-card-2">
      	<div class="widget-header widget-header-small">
          <center>
            <a data-toggle="collapse" data-parent="#collapseControl" href="#collapseProduct" role="button" aria-expanded="false" aria-controls="collapseProduct" style="text-decoration:none;color:#FFFFFF;">
      			<h4 class="widget-title lighter w3-animate-opacity" style="color:#FFFFFF;">
      				<i class="ace-icon fa fa-barcode" style="color:#FFFFFF;"></i>
      				<?php echo plugin_lang_get( 'my_products' ) ?>
      			</h4>
            </a>
          </center>
      	</div>
      <div class="collapse panel-collapse in" id="collapseProduct">
        <div class="" style="overflow-y: scroll !important; max-height: 400px !important;">
          <?php echo $t_setting->get_categories_and_display_it( 'product' ); ?>
        </div>
        <div class="space-10"></div>
        <div class="w3-container w3-center">
          <a class="w3-btn w3-green w3-round-small" style="text-decoration:none;" data-toggle="modal" data-target="#ModalAddProduct"><?php echo plugin_lang_get( 'add_product' ); ?></a>
          <div class="space-10"></div>
        </div>
    </div>
  </div>
  <div id="ModalAddProduct" class="modal w3-animate-zoom" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <form action="" method="post">
        <div class="modal-header" style="background-color:#307ECC;">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title" style="color:#FFFFFF;"><i class="ace-icon fa fa-plus-square"></i>&nbsp;<?php echo plugin_lang_get( 'add_product' ); ?></h4>
        </div>
        <div class="modal-body">
          <h4 class="modal-title" style="color:#307ECC;"><?php echo plugin_lang_get( 'name' ); ?></h4><br />
          <input type="text" id="name" name="name" size="50" maxlength="128" value="" required />
          <hr />
          <h4 class="modal-title" style="color:#307ECC;"><?php echo plugin_lang_get( 'color' ); ?></h4><br />
          <input class="jscolor" type="text" id="color" name="color" size="50" maxlength="128" value="" required />
          <input type="hidden" class="hidden" id="parameters" name="parameters" value="product,create,0" />
          </div>
        <div class="modal-footer">
          <button formaction="<?php echo plugin_page( 'config_page' ) ?>" class="w3-btn w3-green w3-round-small" style="text-decoration:none;"><?php echo plugin_lang_get( 'save' ); ?></button>
          <a href="" class="w3-btn w3-red w3-round-small" data-dismiss="modal" style="text-decoration:none;"><?php echo plugin_lang_get( 'close' ); ?></a>
        </div>
      </form>
      </div>
    </div>
  </div>
  </div>



    <!-- Module des modules -->

      <div class="col-md-4 col-xs-4">
        <div class="widget-box widget-color-blue w3-card-2">
          <div class="widget-header widget-header-small">
            <center>
            <a data-toggle="collapse" data-parent="#collapseControl" href="#collapseModule" role="button" aria-expanded="false" aria-controls="collapseModule" style="text-decoration:none;color:#FFFFFF;">
            <h4 class="widget-title lighter w3-animate-opacity" style="color:#FFFFFF;">
              <i class="ace-icon fa fa-magnet" style="color:#FFFFFF;"></i>
              <?php echo plugin_lang_get( 'my_modules' ) ?>
            </h4>
          </a>
          </center>
        </div>
        <div class="collapse panel-collapse in" id="collapseModule" >
          <div class="" style="overflow-y: scroll !important; max-height: 400px !important;">
            <?php echo $t_setting->get_categories_and_display_it( 'module' ); ?>
          </div>
          <div class="space-10"></div>
          <div class="w3-container w3-center">
            <a class="w3-btn w3-green w3-round-small" style="text-decoration:none;" data-toggle="modal" data-target="#ModalAddModule"><?php echo plugin_lang_get( 'add_module' ); ?></a>
            <div class="space-10"></div>
          </div>
      </div>
    </div>
    <div id="ModalAddModule" class="modal w3-animate-zoom" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <form action="" method="post">
          <div class="modal-header" style="background-color:#307ECC;">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title" style="color:#FFFFFF;"><i class="ace-icon fa fa-plus-square"></i>&nbsp;<?php echo plugin_lang_get( 'add_module' ); ?></h4>
          </div>
          <div class="modal-body">
            <h4 class="modal-title" style="color:#307ECC;"><?php echo plugin_lang_get( 'name' ); ?></h4><br />
            <input type="text" id="name" name="name" size="50" maxlength="128" value="" required />
            <hr />
            <h4 class="modal-title" style="color:#307ECC;"><?php echo plugin_lang_get( 'associated_to' ); ?></h4><br />
            <?php echo wikiArticleSetting::config_print_all_parameters_in_selectbox( 'product', NULL ); ?>
            <hr />
            <h4 class="modal-title" style="color:#307ECC;"><?php echo plugin_lang_get( 'color' ); ?></h4><br />
            <input class="jscolor" type="text" id="color" name="color" size="50" maxlength="128" value="" required />
            <input type="hidden" class="hidden" id="parameters" name="parameters" value="module,create,0">
            </div>
          <div class="modal-footer">
            <button formaction="<?php echo plugin_page( 'config_page' ); ?>" class="w3-btn w3-green w3-round-smalln" style="text-decoration:none;"><?php echo plugin_lang_get( 'save' ); ?></button>
            <a href="" class="w3-btn w3-red w3-round-small" data-dismiss="modal" style="text-decoration:none;"><?php echo plugin_lang_get( 'close' ); ?></a>
          </div>
        </form>
        </div>
      </div>
    </div>
    </div>

  <!-- Module des types -->

    <div class="col-md-4 col-xs-4 no-padding">
      <div class="widget-box widget-color-blue w3-card-2">
        <div class="widget-header widget-header-small">
          <center>
            <a data-toggle="collapse" data-parent="#collapseControl" href="#collapseType" role="button" aria-expanded="false" aria-controls="collapseType" style="text-decoration:none;color:#FFFFFF;">
            <h4 class="widget-title lighter w3-animate-opacity" style="color:#FFFFFF;">
              <i class="ace-icon fa fa-sitemap" style="color:#FFFFFF;"></i>
              <?php echo plugin_lang_get( 'my_types' ) ?>
            </h4>
            </a>
          </center>
        </div>
        <div class="collapse panel-collapse in" id="collapseType" >
          <div class="" style="overflow-y: scroll !important; max-height: 400px !important;">
            <?php echo $t_setting->get_categories_and_display_it( 'type' ); ?>
          </div>
          <div class="space-10"></div>
          <div class="w3-container w3-center">
            <a class="w3-btn w3-green w3-round-small" style="text-decoration:none;" data-toggle="modal" data-target="#ModalAddType"><?php echo plugin_lang_get( 'add_type' ); ?></a>
            <div class="space-10"></div>
          </div>
      </div>
    </div>
    <div id="ModalAddType" class="modal w3-animate-zoom" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <form action="" method="post">
          <div class="modal-header" style="background-color:#307ECC;">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title" style="color:#FFFFFF;"><i class="ace-icon fa fa-plus-square"></i>&nbsp;<?php echo plugin_lang_get( 'add_type' ); ?></h4>
          </div>
          <div class="modal-body">
            <h4 class="modal-title" style="color:#307ECC;"><?php echo plugin_lang_get( 'name' ); ?></h4><br />
            <input type="text" id="name" name="name" size="50" maxlength="128" value="" required />
            <hr />
            <h4 class="modal-title" style="color:#307ECC;"><?php echo plugin_lang_get( 'associated_to' ); ?></h4><br />
            <?php echo wikiArticleSetting::config_print_all_parameters_in_selectbox( 'module', NULL ); ?>
            <hr />
            <h4 class="modal-title" style="color:#307ECC;"><?php echo plugin_lang_get( 'color' ); ?></h4><br />
            <input class="jscolor" type="text" id="color" name="color" size="50" maxlength="128" value="" required />
            <input type="hidden" class="hidden" id="parameters" name="parameters" value="type,create,0">
            </div>
          <div class="modal-footer">
            <button formaction="<?php echo plugin_page( 'config_page' ); ?>" class="w3-btn w3-green w3-round-smalln" style="text-decoration:none;"><?php echo plugin_lang_get( 'save' ); ?></button>
            <a href="" class="w3-btn w3-red w3-round-small" data-dismiss="modal" style="text-decoration:none;"><?php echo plugin_lang_get( 'close' ); ?></a>
          </div>
        </form>
        </div>
      </div>
    </div>
    </div>
  </div>

    <!-- Module des contacts -->


        <div class="col-md-12 col-xs-12 no-padding">
          <div class="space-10"></div>

    <!-- Module des clients -->

  <div class="col-md-6 col-xs-6 no-padding">
    <div class="widget-box widget-color-blue w3-card-2">
      <div class="widget-header widget-header-small">
        <center>
            <a data-toggle="collapse" data-parent="#collapseControl" href="#collapseConsumers" role="button" aria-expanded="false" aria-controls="collapseShow" style="text-decoration:none;color:#FFFFFF;">
          <h4 class="widget-title lighter w3-animate-opacity" style="color:#FFFFFF;">
            <i class="ace-icon fa fa-user-plus" style="color:#FFFFFF;"></i>
            <?php echo plugin_lang_get( 'my_consumers' ) ?>
          </h4>
          </a>
        </center>
      </div>
      <div class="collapse panel-collapse <?php echo $_div_consumer; ?>" id="collapseConsumers" >
        <div class="" style="overflow-y: scroll !important; max-height: 400px !important;">
          <?php echo $t_consumer->consumer_print_all_in_table(); ?>
        </div>
        <div class="space-10"></div>
        <div class="w3-container w3-center w3-round-small">
          <a class="w3-btn w3-green w3-round-small" style="text-decoration:none;" data-toggle="modal" data-target="#ModalAddConsumer"><?php echo plugin_lang_get( 'add_consumer' ); ?></a>
          <div class="space-10"></div>
        </div>
    </div>
    </div>
    <div id="ModalAddConsumer" class="modal w3-animate-zoom" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <form action="" method="post">
        <div class="modal-header" style="background-color:#307ECC;">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title" style="background-color:#307ECC;color:#FFFFFF;"><i class="ace-icon fa fa-plus-square"></i>&nbsp;<?php echo plugin_lang_get( 'add_consumer' ); ?></h4>
        </div>
        <div class="modal-body">
          <h4 class="modal-title" style="color:#307ECC;"><?php echo plugin_lang_get( 'name' ); ?></h4><br />
          <input type="text" id="name" name="name" size="50" maxlength="128" value="" required />
          <hr />
          <input type="hidden" class="hidden" id="parameters" name="parameters" value="consumer,create,0" />
          </div>
        <div class="modal-footer">
          <button formaction="<?php echo plugin_page( 'config_page' ); ?>" class="w3-btn w3-green w3-round-smalln" style="text-decoration:none;"><?php echo plugin_lang_get( 'save' ); ?></button>
          <a href="" class="w3-btn w3-red w3-round-small" data-dismiss="modal" style="text-decoration:none;"><?php echo plugin_lang_get( 'close' ); ?></a>
        </div>
      </form>
      </div>
    </div>
    </div>
    </div>

    <!-- Module des utilisateurs / groupes -->

      <div class="col-md-6 col-xs-6 no-padding">
        <div class="widget-box widget-color-blue w3-card-2">
          <div class="widget-header widget-header-small">
            <center>
              <a data-toggle="collapse" data-parent="#collapseControl" href="#collapseConnection" role="button" aria-expanded="false" aria-controls="collapseShow" style="text-decoration:none;color:#FFFFFF;">
                <h4 class="widget-title lighter w3-animate-opacity" style="color:#FFFFFF;">
                <i class="ace-icon fa fa-chain" style="color:#FFFFFF;"></i>
                <?php echo plugin_lang_get( 'my_users' ) ?>
                </h4>
              </a>
            </center>
          </div>
          <div class="collapse panel-collapse <?php echo $_div_connection; ?>" id="collapseConnection">
            <div class="" style="overflow-y: scroll !important; max-height: 400px !important;">
              <?php echo wikiUser::print_groups_in_table(); ?>
            </div>
            <div class="space-10"></div>
            <div class="w3-center">
              <a class="w3-btn w3-green w3-round-small" style="text-decoration:none;" data-toggle="modal" data-target="#ModalAddGroup"><?php echo plugin_lang_get( 'add_group' ); ?></a>
              <div class="space-10"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div id="ModalAddGroup" class="modal w3-animate-zoom" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <form action="" method="post">
            <div class="modal-header" style="background-color:#307ECC;">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title" style="background-color:#307ECC;color:#FFFFFF;"><i class="ace-icon fa fa-plus-square"></i>&nbsp;<?php echo plugin_lang_get( 'add_group' ); ?></h4>
            </div>
            <div class="modal-body">
              <h4 class="modal-title" style="color:#307ECC;"><?php echo plugin_lang_get( 'name' ) ; ?></h4>
              <input type="text" id="name" name="name" size="50" maxlength="99" value="" placeholder="<?php echo plugin_lang_get( 'placeholder_group_name' ); ?>"/>
              <hr />
              <input type="hidden" class="hidden" id="parameters" name="parameters" value="group,create,0" />
            </div>
            <div class="modal-footer">
            <button formaction="<?php echo plugin_page( 'config_page' ); ?>" class="w3-btn w3-green w3-round-smalln" style="text-decoration:none;"><?php echo plugin_lang_get( 'save' ); ?></button>
            <a href="" class="w3-btn w3-red w3-round-small" data-dismiss="modal" style="text-decoration:none;"><?php echo plugin_lang_get( 'close' ); ?></a>
            </div>
          </form>
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
