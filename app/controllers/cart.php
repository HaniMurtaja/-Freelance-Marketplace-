<?php

namespace Fir\Controllers;

class Cart extends Controller
{
    /**
     * This would be your http://localhost/project-name/ index page
     *
     * @return array
     */
    protected $admin;
	
    public function index()
    {
        if (isset($this->url[0]) && $this->url[0] == 'lang') {
            $this->updateLanguage($this->url[1]);
        }

        /**
         * The $data array stores all the data that is passed to the views
         */
        $data = [];
			
		$validation = "";	

        $admin = $this->library('Admin');
		
		/* Use Product Model */
		$productModel = $this->model('Product');
        $data['product'] = $productModel->details();
		
		
		/* Use Basket Library */
        $basket = $this->library('Basket');
        $data['cart'] = $basket->all();
        $data['cart_total'] = $basket->subTotal();
		
		/*Use User Library*/
        $user = $this->library('User');
		$data['user_isloggedin'] = $user->isLoggedIn();

        return ['content' => $this->view->render($data, 'home/cart')];
    }
	
    public function removecart()
    {

        /**
         * The $data array stores all the data that is passed to the views
         */
        $data = [];
		
		/* Use Basket Library */
        $this->basket = $this->library('Basket');
		$this->basket->remove($this->url[2]);

		
		redirect('cart');
    }
	

    /**
     * @param $language string
     */
    private function updateLanguage($language)
    {
        setcookie('lang', $language, time() + (10 * 365 * 24 * 60 * 60), COOKIE_PATH);
        redirect();
    }
}