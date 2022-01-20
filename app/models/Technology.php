<?php

namespace Fir\Models;

class Technology extends Model {
		
    /**
     * Add Author
     *
     */
    public function addtechnology($name, $filename) {

			
	    $Insert = $this->db->insert('technologies', array(
		   'name' => $name,
		   'imagelocation' => $filename,
		   'date_added' => date('Y-m-d H:i:s'),
		));	
		  
		return $Insert->rowCount();  
    }
	
    /**
     * Gets Technology details
     *
     * @return    array
     */
    public function technologyDetails()
    {
		
        $technology = $this->db->select('technologies', '*', ["ORDER" => ["date_added" => "DESC"]]);

        return $technology;
    }
	
    /**
     * Gets Technology array
     *
     * @return    array
     */
    public function getarray()
    {
		
        $query = $this->db->select('technologies', '*', []);
		 foreach($query as $row) {
			$names_skills[] = [$row["name"], $row["imagelocation"]];
		 }	

        return $names_skills;
    }
	
    /**
     * Check if the technology is available in the db
     *
     */
    public function technologyHas($id) {

		$has = $this->db->has("technologies", ["id" => $id]);		
		  
		return $has;  
    }
	
    /**
     * Get the technology details requested
     *
     */
    public function technologyGet($id) {

		$query = $this->db->select("technologies", "*", ["id" => $id]);	
        foreach($query as $row){}		
		  
		return $row;  
    }
	
    /**
     * Update the Technology Update
     *
     */
    public function technologyUpdate($name, $id) {

		$Update = $this->db->update('technologies',[
		   'name' => $name
		],[
		    'id' => $id
		  ]);
		  
		return $Update->rowCount();  
    }
	
    /**
     * Update the Technology Update Image
     *
     */
    public function changeImage($filename, $id) {

		$Update = $this->db->update('technologies',[
		   'imagelocation' => $filename
		],[
		    'id' => $id
		  ]);
		  
		return $Update->rowCount();  
    }
	
	
}