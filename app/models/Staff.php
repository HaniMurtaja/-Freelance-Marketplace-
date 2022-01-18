<?php

namespace Fir\Models;

class Staff extends Model {
		
    /**
     * Add User
     *
     */
    public function add($adminid, $name, $email, $password, $role) {

		$filename = "default.png";
		
	    $Insert = $this->db->insert('admin', array(
	   'adminid' => $adminid,
	   'password' => $password,
	   'name' => $name,
	   'email' => $email,
	   'imagelocation' => $filename,
	   'joined' => date('Y-m-d H:i:s'),
	   'user_type' => $role
		));	
		  
		return $Insert->rowCount();  
    }
    
    public function list()
    {
		
        $query = $this->db->select('admin', '*', ["ORDER" =>["joined" => "DESC"]]);

        return $query;
    }
    public function has($id) {

		$has = $this->db->has("admin", ["adminid" => $id]);		
		  
		return $has;  
    }
    public function get($id) {

		$query = $this->db->select("admin", "*", ["adminid" => $id]);	
        foreach($query as $row){}		
		  
		return $row;  
    }
    public function update_staff($name, $email, $user_type, $id) {

		$Update = $this->db->update('admin',[
		   'name' => $name,
		   'email' => $email,
		   'user_type' => $user_type,
		],[
		    'adminid' => $id
		  ]);
		  
		return $Update->rowCount();  
    }
    public function changeImage($filename, $id) {

		$Update = $this->db->update('admin',[
		   'imagelocation' => $filename
		],[
		    'adminid' => $id
		  ]);
		  
		return $Update->rowCount();  
    }
    public function password($password, $id) {

		$Update = $this->db->update('admin',[
		   'password' => $password,
		],[
		    'adminid' => $id
		  ]);
		  
		return $Update->rowCount();  
    }
    
    public function roles_list()
    {
		
        $query = $this->db->select('roles', '*', ["ORDER" =>["id" => "DESC"]]);

        return $query;
    }
    public function roles_add($name, $staff, $projects, $categories, $skills, $users, $portfolios, $verification_requests, $settings, $pages, $revenue_settings, $revenues, $escrow_payments, $funds, $withdrawals, $disputes, $boxplace) {
		
	    $Insert = $this->db->insert('roles', array(
	   'name' => $name,
	   'staff' => $staff,
	   'projects' => $projects,
	   'categories' => $categories,
	   'skills' => $skills,
	   'users' => $users,
	   'portfolios' => $portfolios,
	   'verification_requests' => $verification_requests,
	   'settings' => $settings,
	   'pages' => $pages,
	   'revenue_settings' => $revenue_settings,
	   'revenues' => $revenues,
	   'escrow_payments' => $escrow_payments,
	   'funds' => $funds,
	   'withdrawals' => $withdrawals,
	   'disputes' => $disputes,
	   'boxplace' => $boxplace
		));	
		  
		return $Insert->rowCount();  
    }
    public function has_role($id) {

		$has = $this->db->has("roles", ["id" => $id]);		
		  
		return $has;  
    }
    public function get_role($id) {

		$query = $this->db->select("roles", "*", ["id" => $id]);	
        foreach($query as $row){}		
		  
		return $row;  
    }
    public function roles_update($name, $staff, $projects, $categories, $skills, $users, $portfolios, $verification_requests, $settings, $pages, $revenue_settings, $revenues, $escrow_payments, $funds, $withdrawals, $disputes, $boxplace, $id) {

		$Update = $this->db->update('roles',[
           'name' => $name,
           'staff' => $staff,
           'projects' => $projects,
           'categories' => $categories,
           'skills' => $skills,
           'users' => $users,
           'portfolios' => $portfolios,
           'verification_requests' => $verification_requests,
           'settings' => $settings,
           'pages' => $pages,
           'revenue_settings' => $revenue_settings,
           'revenues' => $revenues,
           'escrow_payments' => $escrow_payments,
           'funds' => $funds,
           'withdrawals' => $withdrawals,
           'disputes' => $disputes,
           'boxplace' => $boxplace
		],[
		    'id' => $id
		  ]);
		  
		return $Update->rowCount();   
    }
	
    
}