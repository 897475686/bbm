<?php

/**
 * School_model
 */

class School_model extends CI_Model {
	public function __construct() {
		parent::__construct();
		
	}

	/**
	 * [get_all 获取所有学校信息]
	 * @return [type] [description]
	 */
	public function get_all()
	{
		$result=$this->db->get('school')->result_array();
		if(!empty($result))
		{
			$school 	=	array();
			foreach ($result as $val) 
			{
				$city 		=	$val['city'];
				$city_first	=	$val['city_first'];
				$district 	= 	$val['district'];
				$id 		= 	$val['id'];
				$name 		= 	$val['name'];
				$count 		=	0;
				if(isset($school[$city]['dis'][$district])){
					$count=count($school[$city]['dis'][$district]);
				}
				$school[$city]['first']=$city_first;
				$school[$city]['dis'][$district][$count]['name']=$name;
				$school[$city]['dis'][$district][$count]['id']=$id;
			}
			$return['school']		=	$school;
			$return['onlyschool']	=	$result;
			return $return;			
		}
		else
		{
			return FALSE;
		}
	}
}
