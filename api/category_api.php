<?php

/**
 * Category API
 */

class CategoryAPI
{

  # Category ID (int)
  protected $id;

  # Category name (string)
  protected $name;

  # Category color (string)
  protected $color;

  # Category sub-categories (srting)
  protected $sub;

  public function __construct( $name="", $color="", $sub="" )
  {
    $this->name = $name;
    $this->color = $color;
    $this->sub = $sub;
  }

  public function save()
  {
    $category_table = DatabaseAPI::get_category_table();

    if( !is_null( $this->name ) && !is_null( $this->color ) && !is_null( $this->sub ) )
      {
        $query = "INSERT INTO {$category_table} ( name, color, sub )
                  VALUES ( " . db_param() . "," . db_param() . "," . db_param() . " )";

        $result = db_query( $query, array( $this->name, $this->color, $this->sub ) );

        $this->id = db_insert_id( $category_table );

        return $this->id;
      }
    else
      {
        return NULL;
      }
  }

  /** Update category
   * @param category_id
   * @return category_id if update is successful
   */
  public function update( $category_id )
  {
    if( !is_null( $category_id ) )
      {
        $this->id = $category_id;

        $category_table = DatabaseAPI::get_category_table();

        $query = "UPDATE {$category_table} SET name=" . db_param() . ",color=" . db_param() . ",sub=" . db_param() . " WHERE id=" . db_param();
        $result = db_query( $query, array( $this->name, $this->color, $this->sub, $this->id ) );

        return !empty( $result ) ? $this->id : NULL;
      }
    else
      {
        return NULL;
      }
  }

  /**
   * Retrive category name
   * @param category_id
   * @return category_name
   */

  public static function get_category_name( $category_id )
  {
    $category_table = DatabaseAPI::get_category_table();

    $query = "SELECT name FROM {$category_table} WHERE id=" . db_param();
    $result = db_query( $query, array( $category_id ) );
    $data = db_fetch_array( $result );

    return $data[ 'name' ];
  }

  public function set_sub_category( $s_sub_category, $category_id )
  {
    if( !is_null( $s_sub_category) && !is_null( $category_id ) )
      {
        $category_table = DatabaseAPI::get_category_table();

        $this->sub = $s_sub_category;
        $this->id = $category_id;

        $query = "UPDATE {$category_table} SET sub=" . db_param() . " WHERE id=" . db_param();
        $result = db_query( $query, array( $this->sub, $this->id ) );

        return !empty( $result ) ? $this->id : NULL;
      }
    else
      {
        return NULL;
      }
  }

  public static function get_sub_category_array( $category_id )
  {
    $category_table = DatabaseAPI::get_category_table();

    $query = "SELECT sub FROM {$category_table} WHERE id=" . db_param();
    $result = db_query( $query, array( $catgory_id ) );
    $data = db_fetch_array( $result );

    $array_sub_category = explode( ',', $data[ 'sub' ] );

    return $array_sub_category;
  }

  public static function get_sub_category_string( $category_id )
  {
    $category_table = DatabaseAPI::get_category_table();

    $query = "SELECT sub FROM {$category_table} WHERE id=" . db_param();
    $result = db_query( $query, array( $catgory_id ) );
    $data = db_fetch_array( $result );

    return $data[ 'sub' ];
  }

  public function delete_sub_category( $sub_category, $category_id )
  {
    if( !is_null( $s_sub_category) && !is_null( $category_id ) )
      {
        $category_table = DatabaseAPI::get_category_table();

        $s_sub_category = self::get_sub_category( $category_id );
        $sub_category = ',' . $sub_category . ',';

        $s_sub_category = str_replace( $sub_category, ',', $s_sub_category );

        return $this->set_sub_category( $s_sub_category, $category_id );
      }
    else
      {
        return NULL;
      }
  }

} # End class
 ?>
