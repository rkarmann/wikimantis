<?php

$id_product = isset( $_POST[ 'productId'] ) ? $_POST[ 'productId' ] : NULL;
$id_module = isset( $_POST[ 'moduleId'] ) ? $_POST[ 'moduleId' ] : NULL;
$user_email = isset( $_POST[ 'user_mail' ] ) ? strtolower($_POST[ 'user_mail' ]) : NULL;
$user_username = isset( $_POST[ 'user_username' ] ) ? strtolower($_POST[ 'user_username' ] ) : NULL;
$keywords = isset( $_POST[ 'keywords' ] ) ? $_POST[ 'keywords' ] : NULL;
$option_array = array();

# Connection à la base de données
try {

  $db = new PDO('mysql:host=localhost;dbname=bugtracker;charset=utf8', 'root', '');

} catch (Exception $e) {

  die('Error : ' . $e->getMessage());

}

  if( !is_null( $id_product ) || !is_null( $id_module ) )
  {
    # Si c'est le produit
    if( !is_null( $id_product )){

      $result = $db->query("SELECT * FROM mantis_plugin_wikiCylande_module_table WHERE id_parameter=" . $id_product . " ORDER BY module_name ASC");

      $option_array[] = array("id" => 0, "name" => "Aucun");

      while( $data = $result->fetch())
      {
        $option_array[] = array("id" => $data['id'], "name" => $data['module_name']);
      }

      $result->closeCursor();

      # Si c'est le module
    } elseif ( !is_null( $id_module) ){

      $result = $db->query("SELECT * FROM mantis_plugin_wikiCylande_type_table WHERE id_parameter=" . $id_module . " ORDER BY type_name ASC");

      $option_array[] = array("id" => 0, "name" => "Aucun");

      while( $data = $result->fetch())
      {
        $option_array[] = array("id" => $data['id'], "name" => $data['type_name']);
      }

      $result->closeCursor();

      # Sinon... rien
    } else {
      $option_array[] = array("id" => 0, "name" => "Aucun");
    }


    echo json_encode($option_array);
  }

  if( !is_null( $user_email ) && !empty( $user_email ) )
  {
    $result = $db->query("SELECT email FROM mantis_user_table WHERE email LIKE '" . $user_email . "%' ORDER BY email ASC LIMIT 0,6");

    if( !empty( $result ) ) {
      ?>
      <ul id="email-list" class="list-group">
      <?php
      foreach ($result as $mail ) {
      ?>
      <li onClick="selectMail('<?php echo $mail[ "email" ] ?>');" class="list-group-item"><?php echo $mail[ "email" ] ?></li>
      <?php
      }
      ?>
      </ul>
      <?php
    }
  }

?>
