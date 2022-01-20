<?php

namespace Fir\Models;

class Transactions extends Model {
		
    /**
     * Add User
     *
     */
    public function add($cart, $paymentid, $userid, $type) {
		
		foreach($cart as $item){

		   //Insert
		   $Insert = $this->db->insert('transactions', array(
			   'productid' => $item['productid'],
			   'price_paid' => $item['price'],
			   'userid' => $userid,
			   'type' => $type,
			   'paymentid' => $paymentid,
			   'complete' => 1,
			   'date_added' => date('Y-m-d H:i:s')
			));	
			
		}
		  
		return $Insert->rowCount();   
    }
	
    /**
     * Gets User details
     *
     * @return    array
     */
    public function details()
    {
		
        $query = $this->db->select('transactions', '*', ["complete" => 1, "ORDER" =>["date_added" => "DESC"]]);

        return $query;
    }
	
    /**
     * Check if the user is available in the db
     *
     */
    public function has($id) {

		$has = $this->db->has("transactions", ["userid" => $id]);		
		  
		return $has;  
    }
	
    /**
     * Get the user details requested
     *
     */
    public function get($id) {

		$query = $this->db->select("transactions", "*", ["userid" => $id]);	
        foreach($query as $row){}		
		  
		return $row;  
    }
	
    /**
     * Get the user details requested
     *
     */
    public function getProductId($id) {

		$query = $this->db->select("transactions", "*", ["userid" => $id]);	
        foreach($query as $row){
			$productid[] = $row["productid"];
		}		
		  
		return $productid;  
    }
	
    /**
     * Get the user details requested
     *
     */
    public function getProducts($id) {
        
		$has = $this->has($id);
	    if($has === true):	
			$q1 = $this->db->select("product", ["[>]transactions" => ["productid" => "productid"]],  "*", ["transactions.userid" => $id]);	
			foreach($q1 as $r1){
				$arr[] = $r1;
			}
        elseif($has === false):
			$arr[] = [];
        endif;		
		  
		return $arr;  
    }
	
    /**
     * Sum of Payments
     *
     */
    public function payments() {
		
		$total = $this->db->sum("transactions", "price_paid", ["complete" => 1]);		
		  
		return $total;  
    }
	
    /**
     * Count of Payments of products
     *
     */
    public function countTransactions() {

		$query = $this->db->select("product", "*", []);	
        foreach($query as $row){
		
			$total[$row["productid"]] = $this->db->count("transactions", ["AND" => ["productid" => $row["productid"],"complete" => 1]]);
			
		}		
		
		return $total;  
    }
	
    /**
     * Count of Payments of products
     *
     */
    public function getTransactions($id) {
		
			$total = $this->db->count("transactions", ["AND" => ["productid" => $id,"complete" => 1]]);
			
		return $total;  
    }
	
}