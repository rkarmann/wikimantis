<?php

  /**
  * Contact API
  * @package wikiCylande/api
  * */

class wikiContact{

  # Id du contact
  public $contact_id;

  # Prénom du contact
  public $firstname;

  # Nom du contact
  public $lastname;

  # Téléphone (fixe)
  public $phone;

  # Téléphone(mobile)
  public $mobile;

  # Adresse postale
  public $address;

  # Informations Complémentaires
  public $info;

  # Adresse mail
  public $mail;

  # Fonction du contact
  public $function;

  # ID entreprise (en lien avec la table "consumer")
  public $id_client;

  # Type de contact ( Cylande ou Client )
  public $contact_type;

  # Constructeur
  function __construct( $fn="", $ln="", $ph="", $mb="", $mail="", $adr="", $info="", $fct="", $id_client=0, $ct="" )
  {
    $this->firstname = $fn;
    $this->lastname = $ln;
    $this->phone = $ph;
    $this->mobile = $mb;
    $this->address = $adr;
    $this->info = $info;
    $this->mail= $mail;
    $this->function = $fct;
    $this->id_client = $id_client;
    $this->contact_type = $ct;
  }

 # Fonction de sauvegarde
 public function save()
 {
   # Sélection de la table "contact"
   $t_contact_table = plugin_table( "contact", "wikiCylande" );

   # Construction de la requête
   $t_query = "INSERT INTO {$t_contact_table}
               (
                 firstname,
                 lastname,
                 phone,
                 mobile,
                 mail,
                 address,
                 info,
                 function,
                 id_client,
                 contact_type
               ) VALUES (
                 " . db_param() .",
                 " . db_param() .",
                 " . db_param() .",
                 " . db_param() .",
                 " . db_param() .",
                 " . db_param() .",
                 " . db_param() .",
                 " . db_param() .",
                 " . db_param() .",
                 " . db_param() ."
               )";

   # Envoi de la requête et transmission des paramètres
   db_query( $t_query, array(
       $this->firstname,
       $this->lastname,
       $this->phone,
       $this->mobile,
       $this->mail,
       $this->address,
       $this->info,
       $this->function,
       $this->id_client,
       $this->contact_type
   ) );

   # Insertion de l'ID dans la table (PRIMARY KEY)
   $this->id = db_insert_id( $t_contact_table );

   # Renvoyer l'ID
   return $this->id;
 }

 # Fonction de mise à jour
 public function update( $id )
 {
   # Sélection de la table "contact"
   $t_contact_table = plugin_table( "contact", "wikiCylande" );

   # Concstruction de la requête de mise à jour
   $t_query = "UPDATE {$t_contact_table} SET
             firstname=" . db_param() . ",
             lastname=" . db_param() . ",
             phone=" . db_param() . ",
             mobile=" . db_param() . ",
             mail=" . db_param() . ",
             address=" . db_param() . ",
             info=" . db_param() . ",
             function=" . db_param() . ",
             id_client=" . db_param() . ",
             contact_type=" . db_param() . "
             WHERE id=" . db_param();

   # Envoi de la requête et transmission des paramètres
   db_query( $t_query, array(
     $this->firstname,
     $this->lastname,
     $this->phone,
     $this->mobile,
     $this->mail,
     $this->address,
     $this->info,
     $this->function,
     $this->id_client,
     $this->contact_type,
     $id
   ) );

   # Renvoyer l'ID
   return $id;
 }

 public function contact_delete_by_id( $id )
 {
   $t_contact_table = plugin_table( "contact", "wikiCylande" );

   $t_query = "DELETE FROM {$t_contact_table} WHERE id=" . db_param();
   $t_result = db_query( $t_query, array( $id ) );

   return ( !is_null($t_result) ) ? $id : NULL;
 }

 # Fonction d'affichage des contacts existants dans un tableau
 static function print_all_contacts_in_table( $consumer_id = NULL, $type )
 {
   # Sélection de la table "contact"
   $t_contact_table = plugin_table( "contact", "wikiCylande" );

   if( $type != 'all' ){
   # Construction de la requête et transmission
   $t_query = "SELECT * FROM {$t_contact_table} WHERE id_client=" . db_param() . " AND contact_type=" . db_param() . " ORDER BY firstname ASC";
   $t_results = db_query( $t_query, array( $consumer_id, $type ) );
   } else {
   $t_query = "SELECT * FROM {$t_contact_table} WHERE id_client=" . db_param() . " ORDER BY firstname ASC";
   $t_results = db_query( $t_query, array( $consumer_id ) );
   }

   $t_consumer = new wikiConsumer();

   $t_display = self::print_view_mode_bar( NULL, $consumer_id, $type );

   # Fabrication du tableau contenant les information clients
   $t_display .= '<div class="col-md-9 col-xs-9 no-padding">
                  <table>
                   <tr>
                     <td class="w3-gray" width="25%" style="text-align:center;">
                      <span class="w3-text-white"><i class="ace-icon fa fa-search">&nbsp;</i></span>
                     </td>
                     <td width="75%">
                      <input class="form-control w3-card-2" style="width:100%;" size="100%" id="myInputContact" type="text" placeholder="' . plugin_lang_get( 'search_contact' ) . '"/>
                     </td>
                   </tr>
                  </table>
                  <div class="" style="overflow-y: scroll !important; max-height: 500px !important;">
                  <table class="w3-table-all w3-hoverable w3-center">
                    <thead>
                       <tr>
                         <th>
                         ' . plugin_lang_get( 'name' ) . '
                         </th>
                         <th>
                         ' . plugin_lang_get( 'mail' ) . '
                         </th>
                         <th>
                         ' . plugin_lang_get( 'phone' ) . '
                         </th>
                         <th>
                         ' . plugin_lang_get( 'function' ) . '
                         </th>';

        if( is_null( $consumer_id ) )
        {

        $t_display .=    '<th>
                         ' . plugin_lang_get( 'consumer_id' ) . '
                         </th>';
        }
        $t_display .=   '<th style="text-align:center;">
                         <i class="ace-icon fa fa-pencil"></i>
                         </th>
                         <th style="text-align:center;">
                         <i class="ace-icon fa fa-trash"></i>
                         </th>
                       <tr>
                    </thead>
                    <tbody id="myTableContact"';

   # Parcours de la base de donnée et fabrication des lignes
   while( $t_datas = db_fetch_array( $t_results) )
   {
     $t_display .= '<tr>
                      <td>
                          <a href="' . plugin_page( 'view_contact' ) . '&amp;id_client=' . $t_datas[ 'id_client' ] . '&amp;id=' . $t_datas[ 'id' ] . '">'
                          . $t_datas[ 'firstname' ] . '&nbsp;' . $t_datas[ 'lastname' ] . '</a>
                      </td>
                      <td>
                          <a href="mailto:' . $t_datas[ 'mail' ] . '">
                         ' . $t_datas[ 'mail' ] . '</a>
                      </td>
                      <td>
                         ' . $t_datas[ 'phone' ] . '
                      </td>
                      <td>
                         ' . $t_datas[ 'function' ] . '
                      </td>';

      if( is_null( $consumer_id ) )
      {
      $t_display .=   '<td>
                          <a href="' . plugin_page( 'view_contact' ) . '&amp;id_client=' . $t_datas[ 'id_client' ] . '">'
                          . wikiConsumer::get_consumer_name( $t_datas[ 'id_client' ] ) . '
                      </td>';
      }

      $modify_button = ( wikiUser::user_can_create_contacts() ) ? '#ModalModifyContact-' . $t_datas[ 'id' ] : '';
      $delete_button = ( wikiUser::user_can_create_contacts() ) ? '#ModalDeleteContact-' . $t_datas[ 'id' ] : '';
      $t_display .=   '<td style="text-align:center;">
                      <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="' . $modify_button . '">OK</button>
                      </td>
                      <td style="text-align:center;">
                      <button type="button" class="btn w3-hover-red btn-sm" data-toggle="modal" data-target="' . $delete_button . '">OK</button>
                      </td>
                    </tr>';

    # Modal modification
    $t_phone = explode(' ', $t_datas[ 'phone' ]);
    $ind_phone = $t_phone[0];
    $end_phone = $t_phone[1];

    $t_mobile = explode(' ', $t_datas[ 'mobile' ]);
    $ind_mobile = $t_mobile[0];
    $end_mobile = $t_mobile[1];

    $mail = explode('@', $t_datas[ 'mail' ] );
    $ind_mail = $mail[0];
    $end_mail = $mail[1];

    $t_display .= '<div id="ModalModifyContact-' . $t_datas[ 'id' ] . '" class="modal w3-animate-zoom" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <form action="" method="post">
          <div class="modal-header" style="background-color:#307ECC;">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title" style="background-color:#307ECC;color:#FFFFFF;"><i class="ace-icon fa fa-plus-square"></i>&nbsp;' . plugin_lang_get( 'modify_contact' ) . '</h4>
          </div>
          <div class="modal-body">
            <h4 class="modal-title" style="color:#307ECC;">' . plugin_lang_get( 'firstname' ) . '</h4><br />
            <input type="text" id="firstname" name="firstname" size="20" maxlength="99" value="' . $t_datas[ 'firstname' ] . '"/>
            <hr />
            <h4 class="modal-title" style="color:#307ECC;">' . plugin_lang_get( 'lastname' ) . '</h4><br />
            <input type="text" id="lastname" name="lastname" size="20" maxlength="99" value="' . $t_datas[ 'lastname' ] . '"/>
            <hr />
            <h4 class="modal-title" style="color:#307ECC;">' . plugin_lang_get( 'phone' ) . '</h4><br />
            <select class="autofocus input-sm" name="indicatif_ph">
              <option value="+33" selected>FR +33</option>
              <option value="+32">BE +32</option>
              <option value="+49">DE +49</option>
              <option value="+44">GB +44</option>
              <option value="+34">ES +34</option>
              <option value="+48">PL +48</option>
            </select>
            <input type="tel" id="phone" name="phone" size="20" maxlength="10" value="' . trim( $end_phone ) . '"/>
            <hr />
            <h4 class="modal-title" style="color:#307ECC;">' . plugin_lang_get( 'mobile' ) . '</h4><br />
            <select class="autofocus input-sm" name="indicatif_mb">
              <option value="+33" selected>FR +33</option>
              <option value="+32">BE +32</option>
              <option value="+49">DE +49</option>
              <option value="+44">GB +44</option>
              <option value="+34">ES +34</option>
              <option value="+48">PL +48</option>
            </select>
            <input type="tel" id="c_mobile" name="mobile" size="20" maxlength="10" value="' . trim( $end_mobile ) . '"/>
            <hr />
            <h4 class="modal-title" style="color:#307ECC;">' . plugin_lang_get( 'mail' ) . '</h4><br />
            <input type="text" id="mail_p" name="mail_p" size="20" maxlength="99" value="' . $ind_mail . '"/>
            &nbsp;@&nbsp;
            <input type="text" id="mail_s" name="mail_s" size="20" maxlength="99" value="' . $end_mail . '"/>
            <hr />
            <h4 class="modal-title" style="color:#307ECC;">' . plugin_lang_get( 'address' ) . '</h4><br />
            <textarea class="form-control" id="address" name="address" cols="40" rows="3">' . $t_datas[ 'address' ] . '</textarea>
            <hr />
            <h4 class="modal-title" style="color:#307ECC;">' . plugin_lang_get( 'info' ) . '</h4><br />
            <textarea class="form-control" id="info" name="info" cols="40" rows="3">' . $t_datas[ 'info' ] . '</textarea>
            <hr />
            <h4 class="modal-title" style="color:#307ECC;">' . plugin_lang_get( 'function' ) . '</h4><br />
            <input type="text" id="function" name="function" size="20" maxlength="99" value="' . $t_datas[ 'function' ] . '"/>
            <hr />
          <h4 class="modal-title" style="color:#307ECC;">' . plugin_lang_get( 'consumer_id' ) . '</h4><br />
            ' . $t_consumer->print_all_consumers_ids_in_selectbox( $t_datas[ 'id_client' ] ) . '
            <hr />
            <h4 class="modal-title" style="color:#307ECC;">' . plugin_lang_get( 'contact_type' ) . '</h4><br />
            <select class="autofocus input-sm" name="contact_type">
              <option value="consumer" selected>' . plugin_lang_get( 'consumer_contact' ) . '</option>
              <option value="cylande">' . plugin_lang_get( 'consumer_cylande' ) . '</option>
            </select>
            <hr />
            <input type="hidden" class="hidden" id="parameters" name="parameters" value="contact,modify,' . $t_datas[ 'id' ] . '">
            </div>
          <div class="modal-footer">
            <button formaction="' . plugin_page( 'view_contact' ) . '" class="w3-btn w3-green w3-round-smalln" style="text-decoration:none;">' . plugin_lang_get( 'save' ) . '</button>
            <a href="" class="w3-btn w3-red w3-round-small" data-dismiss="modal" style="text-decoration:none;">' . plugin_lang_get( 'close' ) . '</a>
          </div>
        </form>
        </div>
      </div>
    </div>';

    # Modal suppression
    $t_display .= '<div id="ModalDeleteContact-' . $t_datas[ 'id' ] . '" class="modal w3-animate-zoom" role="dialog">
              <div class="modal-dialog modal-md">
                 <div class="modal-content">
                   <form action="" method="post">
                     <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                          <h4 class="modal-title"><i class="ace-icon fa fa-cogs"></i>&nbsp;' . plugin_lang_get( 'delete_contact' ) . '</h4>
                     </div>
                     <div class="modal-body">
                       <p>' . plugin_lang_get( 'confirm_delete_contact' ) . '</p>
                       <input type="hidden" class="hidden" id="parameters" name="parameters" value="contact,delete,' . $t_datas[ 'id' ] . '" />
                     </div>
                     <div class="modal-footer">
                       <button formaction="' . plugin_page( 'view_contact' ) . '" class="btn btn-default">'. plugin_lang_get( 'delete' ) . '</button>
                       <button type="button" class="btn btn-default" data-dismiss="modal">' . plugin_lang_get( 'cancel' ) . '</button>
                    </div>
                    </form>
                  </div>
                </div>
              </div>';


   }

   $t_display .= '</tbody></table></div></div>';

   # Renvoyer le tableau
   return $t_display;
 }

 public function contact_default_welcome_message()
 {
   $t_display = '
                   <div class="col-md-9 col-xs-9 w3-animate-opacity">
                   <div class="space-10"></div>
                   <div class="w3-card-2">
                       <header class="w3-container w3-light-grey">
                         <h4><i class="ace-icon fa fa-info-circle"></i>&nbsp;' . plugin_lang_get( 'no_contacts' ) . '&nbsp;</h4>
                       </header>
                       <div class="w3-container w3-margin">
                       <p>' . plugin_lang_get( 'no_contacts_for_this_consumer' ) .'</p>
                       </div>
                   </div>
                </div>';

   return $t_display;

 }

 public function print_all_contacts_by_consumer_id( $consumer_id, $contact_type, $id = NULL )
 {
   $t_contact_table = plugin_table( "contact", "wikiCylande" );

   if( is_null( $id )  && $contact_type === 'all'){
     $t_query = "SELECT * FROM {$t_contact_table} WHERE id_client=" . db_param() . " ORDER BY firstname ASC";
     $t_results = db_query( $t_query, array( $consumer_id ) );
   } elseif( !is_null( $contact_type ) && $contact_type != 'all' ) {
     $t_query = "SELECT * FROM {$t_contact_table} WHERE id_client=" . db_param() . " AND contact_type=" . db_param() . " ORDER BY firstname ASC";
     $t_results = db_query( $t_query, array( $consumer_id, $contact_type ) );
   } else {
     $t_query = "SELECT * FROM {$t_contact_table} WHERE id_client=" . db_param() . " AND id=" . db_param() . " ORDER BY firstname ASC";
     $t_results = db_query( $t_query, array( $consumer_id, $id ) );
   }

   $t_display = self::print_view_mode_bar( NULL, $consumer_id, $contact_type );

   $t_display .= '<div class="space-10"></div><div class="col-md-9 col-xs-9 no-padding">';

   $t_consumer = new wikiConsumer();

   while( $t_contacts = db_fetch_array( $t_results ) )
   {
     $t_display .= '
                <div class="w3-card-2 w3-animate-opacity">

                <header class="w3-container w3-light-grey">
                <h3>' . $t_contacts[ 'firstname'] . '&nbsp;' . $t_contacts[ 'lastname' ] . '</h3>
                <h5>'. wikiConsumer::get_consumer_name( $t_contacts[ 'id_client'] ) . ' - ' . $t_contacts[ 'function' ]
                . '&nbsp;( ' . plugin_lang_get( $t_contacts[ 'contact_type' ] ) . ' )' .'</h5>
                </header>
                <div class="space-10"></div>
                <div class="w3-container">
                <p><strong>Tél:</strong>&nbsp;' . $t_contacts[ 'phone' ] . '</p>
                ';
     # Si le mobile n'est pas renseigné
     if( $t_contacts[ 'mobile' ] !== '+33 ' )
     {
     $t_display .= '<p><strong>Mobile:</strong>&nbsp;' . $t_contacts[ 'mobile' ] . '</p>';
     }
     # Si l'adresse n'est pas renseignée
     if( $t_contacts[ 'address' ] != '')
     {
       $t_display .= '<p><strong>' . plugin_lang_get( 'address' ) . ' :</strong>&nbsp;' . $t_contacts[ 'address' ] . '</p>';
     }

     if( $t_contacts[ 'info' ] != '' || !is_null( $t_contacts[ 'info'] ) )
     {
       $t_display .= '<p><strong>' . plugin_lang_get( 'info' ) . ' :</strong>&nbsp;' . $t_contacts[ 'info' ] . '</p>';
     }

     $t_display .= '
                </div>
                <div class="space-10"></div>
                <a href="mailto:' . $t_contacts[ 'mail' ] . '" style="text-decoration:none;">
                <div class="w3-button w3-block" style="background-color:#307ECC;color:#FFFFFF;">
                <i class="ace-icon fa fa-envelope-o"></i>&nbsp;Contacter
                </div>
                </a>
                </div><div class="space-10"></div>';
   }

   $t_display .= '</div>';

   return $t_display;

 }

 public static function contact_exists( $consumer_id, $contact_type )
{
  $t_contact_table = plugin_table( "contact", "wikiCylande" );

  $t_query = "SELECT count(*) FROM {$t_contact_table} WHERE id_client=" . db_param() . " AND contact_type=" . db_param();
  $t_result = db_query( $t_query, array( $consumer_id, $contact_type ) );

  $t_array = db_fetch_array( $t_result );

  $t_count = (int)( $t_array[ 'count(*)' ] );

  if( $t_count > 0){

    $exists = true;

  } else {

    $exists = false;

  }

  return $exists;
}

 public function contact_count_by_consumer( $id_client, $contact_type )
 {
   $t_contact_table = plugin_table( "contact", "wikiCylande" );

   $t_query = "SELECT count(*) FROM {$t_contact_table} WHERE id_client=" . db_param() . " AND contact_type=" . db_param();
   $t_result = db_query( $t_query, array( $id_client, $contact_type ) );
   $t_count = db_fetch_array( $t_result );

   $t_display = '<span class="label w3-round-xxlarge w3-right"><p>' . $t_count[ 'count(*)' ] . '</p></span>';

   return $t_display;
 }

 public static function print_view_mode_bar( $param = NULL, $id_client, $type )
 {
   $display = '';

   $display .= '<div class="col-md-9 col-xs-9 no-padding">
                  <div class="space-10"></div>
                    <div class="w3-bar w3-card-2" style="background-color: #307ECC;">
                      <a href="' . plugin_page( 'view_contact' ) . '&amp;id_client=' . $id_client
                                                                    . '&amp;type=' . $type . '"
                      class="w3-bar-item w3-center w3-button" style="color: #FFFFFF; text-decoration: none;">
                      ' . plugin_lang_get( 'view_in_cards' ) . '</a>
                      <a href="' . plugin_page( 'view_contact' ) . '&amp;id_client=' . $id_client
                                                                    . '&amp;type=' . $type . '&amp;mode=1"
                      class="w3-bar-item w3-center w3-button" style="color: #FFFFFF; text-decoration: none;">
                      ' . plugin_lang_get( 'view_in_table' ) . '</a>';

    if( wikiUser::user_can_create_contacts() )
    {
    $display .=   '<a href="" data-toggle="modal" data-target="#ModalAddContact"
                      class="w3-bar-item w3-center w3-button" style="color: #FFFFFF; text-decoration: none;">
                      ' . plugin_lang_get( 'add_contact' ) . '</a>';
    }

    $display .=   '</div>
                  <div class="space-10"></div>
                  </div>';

    return $display;
 }
}
