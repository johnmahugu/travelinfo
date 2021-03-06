<?php
class ModelRestaurantsRestaurants extends Model {
	
	public function getTotalRestaurants($data = array()){
		$category_str = "";
		if($data['category']){
			$category_str .= " and category='".$data['category']."' ";
		}
	
		$query = $this->db->query("SELECT a.*,c.title as category_title from aa_restaurants as a,aa_restaurants_categorys as c where a.category=c.id and a.language_id=1 ".$category_str." order by a.sort asc,a.update_date desc");
		$rows=$query->rows;

		return $rows;
	}
	
	public function getRestaurant($id) {
		$query = $this->db->query("SELECT * from aa_restaurants where item_id=".$id);
		$rows=$query->rows;
		$result=array();
		foreach($rows as $row){
			$result[$row['language_id']]=$row;
		}
				
		return $result;
	}
	
	public function addRestaurant($data=array()){
	// print_r($data);
		
		$languages = $this->session->data['languages'];
		
		$i=1;
		$item_id=0;
		foreach($languages as $language){
			if($i==1){
				$this->insert_action($data,$language,$item_id);
				$item_id=mysql_insert_id() ;
				$this->db->query("update aa_restaurants set item_id=".$item_id." where  id=".$item_id);
			}
			else{
				$this->insert_action($data,$language,$item_id);
			}
			$i++;
		}
	}
	
	public function deleteRestaurant($data=array()){
		foreach($data['selected'] as $id){
			$query=$this->db->query("select item_id from aa_restaurants where id=".$id);
			$row=$query->row;
			$this->db->query("delete from aa_restaurants where item_id=".$row['item_id']);
		}
	}
	
	public function update_restaurant($data=array()){
		
		$languages = $this->session->data['languages'];
		
		$query=$this->db->query("select item_id from aa_restaurants where id=".$data['restaurant_id']);
		$row=$query->row;
		$item_id=$row['item_id'];
		
		foreach($languages as $language){
			$query2=$this->db->query("select id from aa_restaurants where item_id=".$row['item_id']." and language_id=".$language['language_id']);
			if($query2->row){
				$this->db->query("update aa_restaurants set 
				title='".$this->db->escape($data['restaurant_title'][$language['language_id']])."',
				rating='".$this->db->escape($data['restaurant_rating'][$language['language_id']])."',
				restaurant_category='".$this->db->escape($data['restaurant_restaurant_category'][$language['language_id']])."',
				business_hours='".$this->db->escape($data['restaurant_business_hours'][$language['language_id']])."',
				address='".$this->db->escape($data['restaurant_address'][$language['language_id']])."',
				price='".$this->db->escape($data['restaurant_price'][$language['language_id']])."',
				intro='".$this->db->escape($data['restaurant_intro'][$language['language_id']])."',
				category='".$this->db->escape($data['restaurant_category'][$language['language_id']])."',
				custom_link='".$this->db->escape($data['restaurant_custom_link'][$language['language_id']])."',
				website='".$this->db->escape($data['restaurant_website'][$language['language_id']])."',
				content='".$this->db->escape($data['restaurant_content'][$language['language_id']])."',
				image_url='".$data['restaurant_image'][$language['language_id']]."',
				update_date=now()  
				where item_id=".$item_id." and language_id=".$language['language_id']);
			}
			else{
				$this->insert_action($data,$language,$item_id);
			}	
		}
	}
	
	public function insert_action($data,$language,$item_id){
		$this->db->query("insert into aa_restaurants (item_id,title,rating,restaurant_category,business_hours,address,price,intro,category,custom_link,website,content,image_url,language_id,create_date,update_date) values ('".
		$item_id."','".
		$this->db->escape($data['restaurant_title'][$language['language_id']])."','".
		$this->db->escape($data['restaurant_rating'][$language['language_id']])."','".
		$this->db->escape($data['restaurant_restaurant_category'][$language['language_id']])."','".
		$this->db->escape($data['restaurant_business_hours'][$language['language_id']])."','".
		$this->db->escape($data['restaurant_address'][$language['language_id']])."','".
		$this->db->escape($data['restaurant_price'][$language['language_id']])."','".
		$this->db->escape($data['restaurant_intro'][$language['language_id']])."','".
		$this->db->escape($data['restaurant_category'][$language['language_id']])."','".
		$this->db->escape($data['restaurant_custom_link'][$language['language_id']])."','".
		$this->db->escape($data['restaurant_website'][$language['language_id']])."','".
		$this->db->escape($data['restaurant_content'][$language['language_id']])."','".
		$data['restaurant_image'][$language['language_id']]."','".
		$language['language_id']."',now(),now())");
	}
	
	public function getCategorys(){
		$query = $this->db->query("SELECT item_id,title,language_id from aa_restaurants_categorys order by item_id asc");
		$rows=$query->rows;
		
		$ressult=array();
		$languages = $this->session->data['languages'];
		foreach($rows as $row){
			$ressult[$row['language_id']][]=array("item_id"=>$row['item_id'],"title"=>$row['title']);
		}

		return $ressult;
	}
	
	public function saveSort($data=array()){
		$sort_arr=json_decode(htmlspecialchars_decode($data['sort_string']));
		foreach($sort_arr as $key=>$id){
			$query=$this->db->query("update aa_restaurants set sort='".$key."' where item_id='".$id."'");
		
		}
	}
}
?>
