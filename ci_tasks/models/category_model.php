<?php
class Category_model extends CI_Model {

  public function get_category_info($user_id, $layout) {

    $rows = $this->db
      ->select('id, category, column, display, order')
      ->from('categories')
      ->where(array(
        'user_id' => $user_id,
      ))
      ->get()
      ->result();

    $data = array();

    if($layout == 1) {

      $data[1] = $rows;

    } else {

      $cats = array();

      foreach( $rows as $row ) {
        $cats[$row->column][] = $row;
      }

      $col_1 = isset($cats[1]) ? $cats[1] : array();
      $col_2 = isset($cats[2]) ? $cats[2] : array();

      $data[1] = $col_1;
      $data[2] = $col_2;

    }

    ksort($data);

    return $data;
  }

  public function get_categories($user_id) {
    $categories = $this->db
      ->get_where('categories', array(
        'user_id' => $user_id,
        'display' => 1
      ))
      ->result();

    $data = array(
      'ids' => array(),
      'cats' => array()
    );

    foreach($categories as $category) {
      $data['ids'][] = $category->id;
      $data['cats'][$category->id] = $category->category;
    }

    return $data;

  }

  public function create_category($new_cat) {
    $this->db->insert('categories', $new_cat);
    $id = $this->db->insert_id();
    $q = $this->db->get_where( 'categories', array( 'id' => $id ) );
    return $q->result();
  }

  public function update_category($data) {
    $this->db->where('id', $data['id']);
    $this->db->update('categories', $data);
  }

  public function update_category_order($categories) {
    foreach( $categories as $order => $id) {
      $this->db->where('id', $id);
      $this->db->update('categories', array( 'order' => $order ));
    }
  }

}
