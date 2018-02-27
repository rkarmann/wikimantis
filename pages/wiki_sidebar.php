<?php

/**
 * Sidebar du wiki
 * @uses article_api.php
 *
 **/

$t_setting = new wikiArticleSetting();

$t_consumer = new wikiConsumer();

 ?>
 <div class="col-md-3 col-xs-3">
   <div class="space-10"></div>
   <div class="widget-box widget-color-blue w3-card-2">
     <div class="widget-header widget-header-small" >
       <h4 class="widget-title lighter" style="color:#FFFFFF">
         <a data-toggle="collapse" href="#collapseMenu" role="button" aria-expanded="true" aria-controls="collapse" style="text-decoration:none;color:#FFFFFF;">
         <i class="ace icon fa fa-bars"></i>
         <?php echo plugin_lang_get('menu_header')?>
         </a>
       </h4>
     </div>
     <div class="collapse in" id="collapseMenu">
       <ul class="nav nav-list">
         <li>
           <a href="<?php echo plugin_page('wiki_main')?>">
             <i class="menu-icon fa fa-home"></i>
             <span class="menu-text"><?php echo plugin_lang_get('menu_start')?></span>
           </a>
         </li>
         <?php if( wikiUser::user_can_create_articles())
         {
         ?>
         <li>
             <a href="#ModalAddArticle" data-toggle="modal" data-target="#ModalAddArticle">
             <i class="menu-icon fa fa-plus-square"></i>
             <span class="menu-text"><?php echo plugin_lang_get('add_article')?></span>
           </a>
         </li>
         <?php }
         if( wikiUser::user_can_create_connections())
         {
         ?>
         <li>
             <a href="#ModalAddConnection" data-toggle="modal" data-target="#ModalAddConnection">
             <i class="menu-icon fa fa-plus-square"></i>
             <span class="menu-text"><?php echo plugin_lang_get('add_connection')?></span>
           </a>
         </li>
         <?php }
         if( wikiUser::user_can_create_contacts())
         {
         ?>
         <li>
             <a href="#ModalAddContact" data-toggle="modal" data-target="#ModalAddContact">
             <i class="menu-icon fa fa-plus-square"></i>
             <span class="menu-text"><?php echo plugin_lang_get('add_contact')?></span>
           </a>
         </li>
         <?php }
         if( wikiUser::user_can_read_articles())
         {
         ?>
           <li>
           <a href="<?php echo plugin_page('wiki_view_all')?>">
           <i class="menu-icon fa fa-newspaper-o"></i>
           <span class="menu-text"><?php echo plugin_lang_get('view_all_articles')?></span>
           </a>
           </li>
         <?php }
         if( wikiUser::user_can_create_articles())
         {
         ?>
           <li>
             <a href="<?php echo plugin_page( 'wiki_article_box' ) . '&amp;t=article&amp;mode=self'; ?>">
               <i class="menu-icon fa fa-files-o"></i>
               <span class="menu-text"><?php echo plugin_lang_get('my_articles')?></span>
             </a>
           </li>
           <li>
             <a href="<?php echo plugin_page( 'wiki_article_box' ) . '&amp;t=draft&amp;mode=self'; ?>">
               <i class="menu-icon fa fa-pencil-square-o"></i>
               <span class="menu-text"><?php echo plugin_lang_get('my_drafts')?></span>
             </a>
           </li>
         <?php }
         if( wikiUser::user_is_admin() )
         {
         ?>
          <li>
             <a href="<?php echo plugin_page( 'config_page' ); ?>">
               <i class="menu-icon fa fa-cogs"></i>
               <span class="menu-text"><?php echo plugin_lang_get( 'config' ); ?></span>
             </a>
           </li>
          <?php
        }
        ?>
       </ul>
     </div>
     </div>
     <br />
     <div class="space-10"></div>
     <?php
     if( wikiUser::user_can_read_articles())
     {
       echo $t_setting->config_get_sidebar_items_and_display_it( 'product', 'fa-barcode' );
       echo $t_setting->config_get_sidebar_items_and_display_it( 'module', 'fa-magnet' );
       echo $t_setting->config_get_sidebar_items_and_display_it( 'type', 'fa-sitemap' );
     }
     ?>
     <div class="space-10"></div>
     <?php
     if( wikiUser::user_can_read_articles() || wikiUser::user_can_read_connections() || wikiUser::user_can_read_contacts())
     {
     echo $t_consumer->consumer_get_sidebar_items_and_display_it( 'fa-users' );
     }
     ?>
   </div>

   <!-- Formulaire d'ajout d'un article -->

   <div id="ModalAddArticle" class="modal w3-animate-zoom" role="dialog">
      <form id="add_article_form" method="post" action="">
          <div class="modal-dialog modal-lg">
              <div class="modal-content">
                  <div class="modal-header w3-center" style="background-color:#307ECC">
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                          <h4 class="modal-title" style="color:#FFFFFF;"><i class="ace-icon fa fa-plus-square"></i>&nbsp;<?php echo plugin_lang_get( 'add_article' ); ?></h4>
                           <hr />
                              <h4 class="modal-title" style="color:#FFFFFF;"><?php echo plugin_lang_get( 'object' ); ?></h4><br />
                              <input style="top:100px;" type="text" id="object" name="object" size="50" maxlength="128" value="" required />
                </div>
                <div class="modal-body w3-center">
                  <center>
                    <table class="w3-center" cellspacing="16">
                      <tr>
                        <td class="w3-padding">
                          <h4 class="modal-title" style="color:#307ECC;"><?php echo plugin_lang_get( 'product' ); ?></h4>
                        </td>
                        <td class="w3-padding">
                          <h4 class="modal-title" style="color:#307ECC;"><?php echo plugin_lang_get( 'module' ); ?></h4>
                        </td>
                        <td class="w3-padding">
                          <h4 class="modal-title" style="color:#307ECC;"><?php echo plugin_lang_get( 'type' ); ?></h4>
                        </td>
                        <td class="w3-padding">
                          <h4 class="modal-title" style="color:#307ECC;"><?php echo plugin_lang_get( 'consumer_id' ); ?></h4>
                        </td>
                      </tr>
                      <tr>
                        <td class="w3-padding">
                          <?php echo $t_setting->config_print_all_parameters_in_selectbox( 'product', null ); ?>
                        </td>
                        <td class="w3-padding">
                          <?php echo $t_setting->config_print_all_parameters_in_selectbox( 'module', null ); ?>
                        </td>
                        <td class="w3-padding">
                          <?php echo $t_setting->config_print_all_parameters_in_selectbox( 'type', null ); ?>
                        </td>
                        <td class="w3-padding">
                          <?php echo wikiConsumer::print_all_consumers_ids_in_selectbox(); ?>
                        </td>
                      </tr>
                    </table>
                  </center>
                    <hr />
                    <h4 class="modal-title" style="color:#307ECC;"><?php echo plugin_lang_get( 'description' ); ?></h4><br />
                    <textarea class="editor" id="editor" name="description" cols="60" rows="10" value=""></textarea><hr />
                    <br />
                    <h4 class="modal-title" style="color:#307ECC;"><?php echo plugin_lang_get( 'keywords' ); ?></h4><br />
                    <input type="text" class="form-control" name="keyword" id="" value="" placeholder="<?php echo plugin_lang_get( 'type_keywords_separated_by_comas' ); ?>" />
                  </div>
                  <input type="hidden" id="parameters" name="parameters" value="article,create,0" />
                  <div class="modal-footer">
                    <button type="submit" formaction="<?php echo plugin_page( 'wiki_article_box' );?>&amp;t=draft&amp;a=create" class="w3-btn w3-blue w3-left" ><?php echo plugin_lang_get( 'save_as_draft' ); ?></button>
                    <button type="submit" formaction="<?php echo plugin_page( 'wiki_article_box' );?>&amp;t=article&amp;a=create" class="w3-btn w3-green" ><?php echo plugin_lang_get( 'publicate' ); ?></button>
                    <button type="button" class="w3-btn w3-red" data-dismiss="modal"><?php echo plugin_lang_get( 'cancel' ); ?></button>
                  </div>
            </div>
          </div>
        </form>
      </div>

      <div id="ModalAddConnection" class="modal w3-animate-zoom" role="dialog">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <form action="<?php echo plugin_page( 'view_connection' ); ?>" method="post">
            <div class="modal-header" style="background-color:#307ECC;">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title" style="background-color:#307ECC;color:#FFFFFF;"><i class="ace-icon fa fa-plus-square"></i>&nbsp;<?php echo plugin_lang_get( 'add_connection' ); ?></h4>
            </div>
            <div class="modal-body">
              <h4 class="modal-title" style="color:#307ECC;"><?php echo plugin_lang_get( 'product' ) ; ?></h4><br />
              <?php echo wikiArticleSetting::config_print_all_parameters_in_selectbox( 'product', null ); ?>
              <hr />
              <h4 class="modal-title" style="color:#307ECC;"><?php echo plugin_lang_get( 'consumer_id' ) ; ?></h4><br />
              <?php echo wikiConsumer::print_all_consumers_ids_in_selectbox() ; ?>
              <hr />
              <h4 class="modal-title" style="color:#307ECC;"><?php echo plugin_lang_get( 'title' ) ; ?></h4><br />
              <input class="form-control" type="text" id="title" name="title" size="50" maxlength="99" value=""/>
              <hr />
              <center>
              <h4 class="modal-title" style="color:#307ECC;"><?php echo plugin_lang_get( 'description' ) ; ?></h4><br />
              <textarea class="editor" id="description" name="description" cols="40" rows="5" required></textarea><hr />
              </center>
              <input type="hidden" class="hidden" id="parameters" name="parameters" value="connection,create,0" />
              </div>
            <div class="modal-footer">
              <button type="submit" formaction="<?php echo plugin_page( 'view_connection' ); ?>" class="w3-btn w3-green w3-round-small" style="text-decoration:none;"><?php echo plugin_lang_get( 'save' ); ?></button>
              <a href="" class="w3-btn w3-red w3-round-small" data-dismiss="modal" style="text-decoration:none;"><?php echo plugin_lang_get( 'close' ); ?></a>
            </div>
          </form>
          </div>
        </div>
        </div>

        <div id="ModalAddContact" class="modal w3-animate-zoom" role="dialog">
          <div class="modal-dialog">
            <div class="modal-content">
              <form action="" method="post">
              <div class="modal-header" style="background-color:#307ECC;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" style="background-color:#307ECC;color:#FFFFFF;"><i class="ace-icon fa fa-plus-square"></i>&nbsp;<?php echo plugin_lang_get( 'add_contact' ); ?></h4>
              </div>
              <div class="modal-body">
                <h4 class="modal-title" style="color:#307ECC;"><?php echo plugin_lang_get( 'firstname' ) ; ?></h4><br />
                <input type="text" id="firstname" name="firstname" size="20" maxlength="99" value=""/>
                <hr />
                <h4 class="modal-title" style="color:#307ECC;"><?php echo plugin_lang_get( 'lastname' ) ; ?></h4><br />
                <input type="text" id="lastname" name="lastname" size="20" maxlength="99" value=""/>
                <hr />
                <h4 class="modal-title" style="color:#307ECC;"><?php echo plugin_lang_get( 'phone' ) ; ?></h4><br />
                <select class="autofocus input-sm" name="indicatif_ph">
                  <option value="+33" selected>FR +33</option>
                  <option value="+32">BE +32</option>
                  <option value="+49">DE +49</option>
                  <option value="+44">GB +44</option>
                  <option value="+34">ES +34</option>
                  <option value="+48">PL +48</option>
                </select>
                <input type="tel" id="c_phone" name="phone" size="20" maxlength="10" value=""/>
                <hr />
                <h4 class="modal-title" style="color:#307ECC;"><?php echo plugin_lang_get( 'mobile' ) ; ?></h4><br />
                <select class="autofocus input-sm" name="indicatif_mb">
                  <option value="+33" selected>FR +33</option>
                  <option value="+32">BE +32</option>
                  <option value="+49">DE +49</option>
                  <option value="+44">GB +44</option>
                  <option value="+34">ES +34</option>
                  <option value="+48">PL +48</option>
                </select>
                <input class="input-sm" type="tel" id="c_mobile" name="mobile" size="20" maxlength="10" value=""/>
                <hr />
                <h4 class="modal-title" style="color:#307ECC;"><?php echo plugin_lang_get( 'mail' ) ; ?></h4><br />
                <input type="text" id="mail_p" name="mail_p" size="20" maxlength="99" value=""/>
                &nbsp;@&nbsp;
                <input type="text" id="mail_s" name="mail_s" size="20" maxlength="99" value=""/>
                <hr />
                <h4 class="modal-title" style="color:#307ECC;"><?php echo plugin_lang_get( 'address' ) ; ?></h4><br />
                <textarea class="form-control" id="address" name="address" cols="40" rows="3"></textarea>
                <hr />
                <h4 class="modal-title" style="color:#307ECC;"><?php echo plugin_lang_get( 'info' ) ; ?></h4><br />
                <textarea class="form-control" id="info" name="info" cols="40" rows="3"></textarea>
                <hr />
                <h4 class="modal-title" style="color:#307ECC;"><?php echo plugin_lang_get( 'function' ) ; ?></h4><br />
                <input type="text" id="function" name="function" size="20" maxlength="99" value=""/>
                <hr />
                <h4 class="modal-title" style="color:#307ECC;"><?php echo plugin_lang_get( 'consumer_id' ) ; ?></h4><br />
                <?php echo wikiConsumer::print_all_consumers_ids_in_selectbox(); ?>
                <hr />
                <h4 class="modal-title" style="color:#307ECC;"><?php echo plugin_lang_get( 'contact_type' ) ; ?></h4><br />
                <select class="autofocus input-sm" name="contact_type">
                  <option value="consumer" selected><?php echo plugin_lang_get( 'consumer_contact' ); ?></option>
                  <option value="cylande"><?php echo plugin_lang_get( 'consumer_cylande' ); ?></option>
                </select>
                <hr />
                <input type="hidden" class="hidden" id="parameters" name="parameters" value="contact,create,0" />
                </div>
              <div class="modal-footer">
                <button formaction="<?php echo plugin_page( 'view_contact' ); ?>" class="w3-btn w3-green w3-round-smalln" style="text-decoration:none;"><?php echo plugin_lang_get( 'save' ); ?></button>
                <a href="" class="w3-btn w3-red w3-round-small" data-dismiss="modal" style="text-decoration:none;"><?php echo plugin_lang_get( 'close' ); ?></a>
              </div>
            </form>
            </div>
          </div>
        </div>
