<?php

namespace Fir\Models;

class Product extends Model {
		
    /**
     * Add User
     *
     */
    public function add($productid, $name, $slug, $price, $old_price, $version, $live_preview, $s3_link, $authorid, $technologies, $filename, $featured, $description) {

	    $Insert = $this->db->insert('product', array(
	   'productid' => $productid,
	   'description' => $description,
	   'name' => $name,
	   'slug' => $slug,
	   'price' => $price,
	   'old_price' => $old_price,
	   'version' => $version,
	   'live_preview' => $live_preview,
	   's3_link' => $s3_link,
	   'authorid' => $authorid,
	   'technologies' => $technologies,
	   'imagelocation' => $filename,
	   'featured' => $featured,
	   'released' => date('Y-m-d H:i:s'),
	   'updated' => date('Y-m-d H:i:s'),
		));	
		  
		return $Insert->rowCount();  
    }
	
    /**
     * Gets Product details
     *
     * @return    array
     */
    public function details()
    {
		
        $query = $this->db->select('product', '*', ["ORDER" =>["released" => "DESC"]]);

        return $query;
    }
	
    /**
     * Gets Product details
     *
     * @return    array
     */
    public function pagination($startpoint, $limit)
    {
		
        $query = $this->db->select('product', '*', ["ORDER" =>["released" => "DESC"], "LIMIT" => [$startpoint, $limit]]);

        return $query;
    }
	
    /**
     * Gets Total number of products
     *
     * @return    array
     */
    public function total()
    {
		
        $query = $this->db->count('product', []);

        return $query;
    }
	
    /**
     * Gets Total number of free products
     *
     * @return    array
     */
    public function totalFreebies()
    {
		
        $query = $this->db->count('product', ["free" => 1]);

        return $query;
    }
	
    /**
     * Gets Product Featured
     *
     * @return    array
     */
    public function featured()
    {
		
        $query = $this->db->select('product', '*', ["featured" => "2", "ORDER" =>["released" => "DESC"]]);

        return $query;
    }
	
    /**
     * Gets Freebie Products
     *
     * @return    array
     */
    public function freebies()
    {
		
        $query = $this->db->select('product', '*', ["free" => "1", "ORDER" =>["released" => "DESC"]]);

        return $query;
    }
	
    /**
     * Check if the product is available in the db
     *
     */
    public function has($id) {

		$has = $this->db->has("product", ["productid" => $id]);		
		  
		return $has;  
    }
	
    /**
     * Check if the product is available in the db
     *
     */
    public function slug($id) {

		$has = $this->db->has("product", ["slug" => $id]);		
		  
		return $has;  
    }
	
    /**
     * Get the user details requested
     *
     */
    public function get($id) {

		$query = $this->db->select("product", "*", ["productid" => $id]);	
        foreach($query as $row){}		
		  
		return $row;  
    }
	
    /**
     * Get the author
     *
     */
    public function getauthor($id) {

		$query = $this->db->select("product", "*", ["productid" => $id]);	
        foreach($query as $row){}

		$q1 = $this->db->select("author", "*", ["authorid" => $row['authorid']]);	
        foreach($q1 as $r1){}		
		  
		return $r1;  
    }
	
    /**
     * Update the User
     *
     */
    public function update($name, $slug, $price, $old_price, $version, $live_preview, $s3_link, $authorid, $technologies, $featured, $productid) {

		$Update = $this->db->update('product',[
		   'name' => $name,
		   'slug' => $slug,
		   'price' => $price,
		   'old_price' => $old_price,
		   'version' => $version,
		   'live_preview' => $live_preview,
		   's3_link' => $s3_link,
		   'authorid' => $authorid,
		   'technologies' => $technologies,
		   'featured' => $featured,
		   'updated' => date('Y-m-d H:i:s'),
		],[
		    'productid' => $productid
		  ]);
		  
		return $Update->rowCount();  
    }
	
    /**
     * Update description
     *
     */
    public function updateDescription($description, $productid) {

		$Update = $this->db->update('product',[
		   'description' => $description,
		   'updated' => date('Y-m-d H:i:s'),
		],[
		    'productid' => $productid
		  ]);
		  
		return $Update->rowCount();  
    }
	
    /**
     * Update the Image
     *
     */
    public function changeImage($filename, $productid) {

		$Update = $this->db->update('product',[
		   'imagelocation' => $filename
		],[
		    'productid' => $productid
		  ]);
		  
		return $Update->rowCount();  
    }
	
    /**
     * Count products
     *
     */
    public function cproducts() {

		$query = $this->db->count("product", []);			
		  
		return $query;  
    }
	
    /**
     * Count products
     *
     */
    public function cfreebies() {

		$query = $this->db->count("product", ["free" => 1]);			
		  
		return $query;  
    }
	
    /**
     * Update views
     *
     */
    public function updateViews($id) {

		$query = $this->db->select("product", "*", ["productid" => $id]);	
        foreach($query as $row){}
		
		$view = $row["views"] + 1;

		$Update = $this->db->update('product',[
		   'views' => $view,
		],[
		    'productid' => $id
		  ]);
		  
		return $Update->rowCount();  
    }
	
    /**
     * Get the user details requested
     *
     */
    public function search($search) {
		
		$query = array();
		
		$Insert = $this->db->insert('search', array(
		   'search_term' => $search,
		   'date_added' => date('Y-m-d H:i:s'),
		));			

		$query = $this->db->select("product", "*", ["name[~]" => $search]);		
		  
		return $query;  
    }
	
    public function searches()
    {
		
        $query = $this->db->select('search', '*', ["ORDER" =>["date_added" => "DESC"]]);

        return $query;
    }
	
	
	
	
}