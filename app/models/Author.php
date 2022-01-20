<?php

namespace Fir\Models;

class Author extends Model {

    /**
     * @var string
     */
    public $username;

    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $rememberToken;
		
    /**
     * Add Author
     *
     */
    public function addauthor($authorid, $name, $filename) {

			
	    $Insert = $this->db->insert('author', array(
		   'authorid' => $authorid,
		   'name' => $name,
		   'imagelocation' => $filename,
		   'date_added' => date('Y-m-d H:i:s'),
		));	
		  
		return $Insert->rowCount();  
    }
	
    /**
     * Gets Author details
     *
     * @return    array
     */
    public function authorDetails()
    {
		
        $author = $this->db->select('author', '*', []);

        return $author;
    }
	
    /**
     * Check if the author is available in the db
     *
     */
    public function authorHas($id) {

		$has = $this->db->has("author", ["id" => $id]);		
		  
		return $has;  
    }
	
    /**
     * Get the author details requested
     *
     */
    public function authorGet($id) {

		$query = $this->db->select("author", "*", ["id" => $id]);	
        foreach($query as $row){}		
		  
		return $row;  
    }
	
    /**
     * Update the Author Update
     *
     */
    public function authorUpdate($name, $id) {

		$Update = $this->db->update('author',[
		   'name' => $name
		],[
		    'id' => $id
		  ]);
		  
		return $Update->rowCount();  
    }
	
    /**
     * Update the Author Update Image
     *
     */
    public function changeImage($filename, $id) {

		$Update = $this->db->update('author',[
		   'imagelocation' => $filename
		],[
		    'id' => $id
		  ]);
		  
		return $Update->rowCount();  
    }
	
	
}