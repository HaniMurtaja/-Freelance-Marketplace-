<?php

namespace Fir\Models;

class Downloads extends Model {

		
    /**
     * Add Download
     *
     */
    public function add($productid, $userid) {

			
	    $Insert = $this->db->insert('downloads', array(
		   'productid' => $productid,
		   'userid' => $userid,
		   'date_added' => date('Y-m-d H:i:s'),
		));	
		  
		return $Insert->rowCount();  
    }
	
    /**
     * Count Downloads
     *
     */
    public function countD() {

		$query = $this->db->count("downloads", []);			
		  
		return $query;    
    }
	
}