<?php

namespace Fir\Controllers;

class Product extends Controller
{
    /**
     * This would be your http://localhost/project-name/ index page
     *
     * @return array
     */
    protected $admin;
	
    public function index()
    {

        /**
         * The $data array stores all the data that is passed to the views
         */
        $data = [];
		
		/* Use Product Model */
		$productModel = $this->model('Product');
		
		$has = $productModel->Has($this->url[1]);

            // If exists
            if($has === true) {
				
				$slug = $productModel->slug($this->url[2]);

				// If exists
				if($slug === true) {

					/*Use User Library*/
					$user = $this->library('User');
					$data['user'] = $user->data();
					$data['user_isloggedin'] = $user->isLoggedIn();
				
					/* Product data */
					$data["product"] = $productModel->get($this->url[1]);
					$data["author"] = $productModel->getauthor($this->url[1]);
					$productModel->updateViews($this->url[1]);
		
					/* Use Transactions Model */
					$transactions = $this->model('Transactions');
					$data['product_transactions'] = $transactions->getTransactions($this->url[1]);
		
					/* Use Basket Library */
					$basket = $this->library('Basket');
					$data['cart'] = $basket->all();
					$data['cart_ids'] = $basket->ids();
					
					if($user->isLoggedIn() === true){
						$has = $transactions->has($data['user']['userid']);
						if($has === true){
							$data['transactions'] = $transactions->getProductId($data['user']['userid']);
							
						}elseif($has === false){
							$data['transactions'] = [];
						}
					}
					
					return ['content' => $this->view->render($data, 'home/product')];
				}else {
					redirect('home');
				}
			}else {
                redirect('home');
            }	
    }
	
    public function cart()
    {

        /**
         * The $data array stores all the data that is passed to the views
         */
        $data = [];
		
		/* Use Product Model */
		$productModel = $this->model('Product');
		
		$product = $productModel->get($this->url[2]);
		
		/* Use Basket Library */
        $this->basket = $this->library('Basket');

        try {
            $this->basket->add($product['productid']);
        } catch (QuantityExceededException $e) {
            //
        }
		
		redirect('product/'. $product['productid'] .'/'. $product['slug']);
    }
	
    public function clearcart()
    {

        /**
         * The $data array stores all the data that is passed to the views
         */
        $data = [];
		
		/* Use Basket Library */
        $this->basket = $this->library('Basket');
		$this->basket->clear();

		
		redirect('home');
    }
	
    public function removecart()
    {

        /**
         * The $data array stores all the data that is passed to the views
         */
        $data = [];
		
		/* Use Product Model */
		$productModel = $this->model('Product');
		
		$product = $productModel->get($this->url[2]);
		
		/* Use Basket Library */
        $this->basket = $this->library('Basket');
		$this->basket->remove($this->url[2]);

		
		redirect('product/'. $product['productid'] .'/'. $product['slug']);
    }
	
    public function download()
    {

		/*Use User Library*/
        $user = $this->library('User');
		$data['user'] = $user->data();
		$data['user_isloggedin'] = $user->isLoggedIn();
		
		if($user->isLoggedIn() === true):	
			
			/* Use Product Model */
			$productModel = $this->model('Product');
			$product = $productModel->get($this->url[2]);
			
			$domain = $this->getDomain($product["s3_link"]);
			if($domain == "google.com"):
				/* Use Downloads Model */
				$downloadsModel = $this->model('Downloads');			
				$downloadsModel->add($product["productid"],$user->data()["userid"]);
				
				header("Location: ". $product["s3_link"]);		
				exit;
			elseif($domain == "dropbox.com"):
				/* Use Downloads Model */
				$downloadsModel = $this->model('Downloads');			
				$downloadsModel->add($product["productid"],$user->data()["userid"]);
				
				header("Location: ". $product["s3_link"]);	
				exit;
			else:
			
				$filepath = $product["s3_link"];	
				
				/* Use Downloads Model */
				$downloadsModel = $this->model('Downloads');			
				$downloadsModel->add($product["productid"],$user->data()["userid"]);
				
				// Process download
				header('Content-Type: application/octet-stream');
				header("Content-Transfer-Encoding: Binary"); 
				header("Content-disposition: attachment; filename=\"" . basename($filepath) . "\""); 
				readfile($filepath);  
				exit;
			endif;
				
			
		else:
			$_SESSION['message'][] = ['warning', $this->lang['please_login_to_download']];
			redirect('login');	
		endif;
    }
	
	function getDomain($url){
		$pieces = parse_url($url);
		$domain = isset($pieces['host']) ? $pieces['host'] : '';
		if(preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)){
			return $regs['domain'];
		}
		return FALSE;
	}	

}