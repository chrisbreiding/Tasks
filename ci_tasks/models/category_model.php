<?php
class Category_model extends CI_Model {

	public function get_category_info($user_id, $layout) {
		if($layout == 1) {
			$q = $this->db->order_by('order', 'asc')->get_where('categories', array('user_id' => $user_id));
			$data = array($q->result());
		} else {
			$q_1 = $this->db->order_by('order', 'asc')->get_where('categories', array(
				'user_id' => $user_id, 
				'column' => 1
			));
			$q_2 = $this->db->order_by('order', 'asc')->get_where('categories', array(
				'user_id' => $user_id, 
				'column' => 2
			));
			$data = array(
				$q_1->result(),
				$q_2->result()
			);
		}
		return $data;
	}

	public function get_categories($user_id) {
		$q = $this->db->order_by('order', 'asc')->get_where('categories', array(
			'user_id' => $user_id, 
			'display' => 1
		));
		$categories = $q->result();
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