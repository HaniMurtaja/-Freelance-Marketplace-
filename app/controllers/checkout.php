<?php

namespace Fir\Controllers;

class Checkout extends Controller
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
		
		/* Use Settings Model */
		$settingsModel = $this->model('Settings');
        $data['settings'] = $settingsModel->get();
        $data['currency_code'] = $settingsModel->currency_code();
		
		/* Use Product Model */
		$productModel = $this->model('Product');
        $data['product'] = $productModel->details();
		
		
		/* Use Basket Library */
        $basket = $this->library('Basket');
        $data['cart'] = $basket->all();
        $data['cart_total'] = $basket->subTotal();
		
		/*Use User Library*/
        $user = $this->library('User');
		$data['user'] = $user->data();
		$data['user_isloggedin'] = $user->isLoggedIn();
		
		if(!$user->isLoggedIn()){
			$_SESSION['message'][] = ['warning', $this->lang['please_login_to_finish_checkout']];
			redirect('login');
		}
		
		
		/*Use User Library*/
		$paymentid = $this->rando();
        $session = $this->library('Session');
		$session->put("paymentid", $paymentid);
		
		/*PayStack*/
		$data['paystack_id'] = $this->rando();
		$session->put("paystack_id", $data['paystack_id']);
		$data['payment_paystack'] = $data['cart_total'] * 100;
		
        $_GET['sandbox'] = $data['settings']['sandbox'];  
		
		/* PayPal Section */
		// Setup class
		$p = $this->library('paypal_class');					// initiate an instance of the class.
			$this_script = URL_PATH;
			  $user_email = $user->data()["email"];
			  $usern = $user->data()["name"];
			$p->add_field('business', $data['settings']['paypal_email']); //don't need add this item. if your set the $p -> paypal_mail.
			$p->add_field('return', $this_script.'/checkout/paypal/success/'.$paymentid);
			$p->add_field('cancel_return', $this_script.'/checkout/paypal/cancel');
			$p->add_field('notify_url', $this_script.'/checkout/paypal/ipn');
			$p->add_field('item_name', "Cart Checkout");
			$p->add_field('item_number', '');
			$p->add_field('amount', $data['cart_total']);
			$p->add_field('currency_code',$data['currency_code']);
			$p->add_field('usern', $usern);
			$p->add_field('user_email', $user_email);
			$p->add_field('cmd', '_xclick');
			$p->add_field('rm', '2');	// Return method = POST

			// 0 � all shopping cart payments use the GET method
			// 1 � the buyer's browser is redirected to the return URL by using the GET method, but no payment variables are included
			// 2 � the buyer's browser is redirected to the return URL by using the POST method, and all payment variables are included The default is 0.

			$data['paypal_form'] = $p; // submit the fields to paypal		

		// Setup class
        $p = $this->library('paypal_class');
			$this_script = URL_PATH;
			  $user_email = $user->data()["email"];
			  $usern = $user->data()["name"];
			$p->add_field('business', $data['settings']['paypal_email']); //don't need add this item. if your set the $p -> paypal_mail.
			$p->add_field('return', $this_script.'/checkout/paypal/success/'.$paymentid);
			$p->add_field('cancel_return', $this_script.'/checkout/paypal/cancel');
			$p->add_field('notify_url', $this_script.'/checkout/paypal/ipn');
			$p->add_field('item_name', "Cart Checkout");
			$p->add_field('item_number', '');
			$p->add_field('amount', $data['cart_total']);
			$p->add_field('currency_code',$data['currency_code']);
			$p->add_field('usern', $usern);
			$p->add_field('user_email', $user_email);
			$p->add_field('cmd', '_xclick');
			$p->add_field('rm', '2');	// Return method = POST

			// 0 � all shopping cart payments use the GET method
			// 1 � the buyer's browser is redirected to the return URL by using the GET method, but no payment variables are included
			// 2 � the buyer's browser is redirected to the return URL by using the POST method, and all payment variables are included The default is 0.

			$data['paypal_form'] = $p; // submit the fields to paypal		

		
		/* Stripe Section */
		$stripe = [
		  "secret_key"      => $data['settings']["stripe_secret_key"],
		  "publishable_key" => $data['settings']["stripe_public_key"],
		];

		\Stripe\Stripe::setApiKey($stripe['secret_key']);
 		
		/* Amount in Cents */
		$data['amount_cents'] = $this->getMoneyAsCents($data['cart_total']);	
		
    	

        return ['content' => $this->view->render($data, 'home/checkout')];
    }
	
    public function paypal()
    {

        /**
         * The $data array stores all the data that is passed to the views
         */
        $data = [];
		
		/*Use User Library*/
        $user = $this->library('User');
		$data['user'] = $user->data();
		
		/* Use Basket Library */
        $basket = $this->library('Basket');
        $data['cart'] = $basket->all();
		
		/* Use Session Library */
        $session = $this->library('Session');
		$paymentid = $session->get('paymentid');
	
		if($this->url[2] === "success"):	
		
			if($this->url[3] != $paymentid){
				$_SESSION['message'][] = ['error', $this->lang['please_proceed_to_paypal']];
				redirect('user/purchases');
			}
			
			/* Use Product Model */
			$transactionsModel = $this->model('Transactions');
			
			$type = "PayPal";
			
			$Insert = $transactionsModel->add($data['cart'], $paymentid, $user->data()["userid"], $type);
							
			if ($Insert == 1) {
				$_SESSION['message'][] = ['success', $this->lang['paid_successfully']];
				$basket->clear();
				$session->delete('paymentid');
				redirect('user/purchases');
			} else {
				$_SESSION['message'][] = ['warning', $this->lang['error_when_saving']];
				redirect('user/purchases');
			}
		
		elseif($this->url[2] === "cancel"):	
		
			$_SESSION['message'][] = ['warning', $this->lang['you_canceled_the_transaction']];
			$session->delete('paymentid');
			redirect('user/purchases');	
		
		elseif($this->url[2] === "ipn"):	
		
			$_SESSION['message'][] = ['warning', $this->lang['instant_payment_notification_not_set']];
			redirect('user/purchases');	
			
		endif;	
    }
	
    public function stripe()
    {

        /**
         * The $data array stores all the data that is passed to the views
         */
        $data = [];
		
		/* Use Settings Model */
		$settingsModel = $this->model('Settings');
        $data['settings'] = $settingsModel->get();
        $data['currency_code'] = $settingsModel->currency_code();
		
		/*Use User Library*/
        $user = $this->library('User');
		$data['user'] = $user->data();
		
		/* Use Basket Library */
        $basket = $this->library('Basket');
        $data['cart'] = $basket->all();
        $data['cart_total'] = $basket->subTotal();
		
		/* Amount in Cents */
		$data['amount_cents'] = $this->getMoneyAsCents($data['cart_total']);	
		
		$paymentid = $this->rando();
		
		/* Use Input Library */
		$input = $this->library('Input');
		
		/* Stripe Section */
		$stripe = [
		  "secret_key"      => $data['settings']["stripe_secret_key"],
		  "publishable_key" => $data['settings']["stripe_public_key"],
		];

		\Stripe\Stripe::setApiKey($stripe['secret_key']);
		
	
		if($this->url[2] === "success"):
		
			if(!isset($_POST['stripeToken'])){
				redirect('checkout');
			}
			
			$token = $_POST['stripeToken'];
			$email = $_POST["stripeEmail"];

			  $customer = \Stripe\Customer::create([
				  'email' => $email,
				  'source'  => $token,
			  ]);

			  $charge = \Stripe\Charge::create([
				  'customer' => $customer->id,
				  'amount'   => $data['amount_cents'],
				  'currency' => $data['currency_code'],
			  ]);
			
			/* Use Product Model */
			$transactionsModel = $this->model('Transactions');
			
			$type = "Stripe";
			
			$Insert = $transactionsModel->add($data['cart'], $paymentid, $user->data()["userid"], $type);
							
			if ($Insert == 1) {
				$_SESSION['message'][] = ['success', $this->lang['paid_successfully']];
				$basket->clear();
				redirect('user/purchases');
			} else {
				$_SESSION['message'][] = ['warning', $this->lang['error_when_saving']];
				redirect('user/purchases');
			}
		
		elseif($this->url[2] === "cancel"):	
		
			$_SESSION['message'][] = ['warning', $this->lang['you_canceled_the_transaction']];
			redirect('user/purchases');	
			
		endif;	
    }
	
    public function razorpay()
    {

        /**
         * The $data array stores all the data that is passed to the views
         */
        $data = [];
		
		/* Use Settings Model */
		$settingsModel = $this->model('Settings');
        $data['settings'] = $settingsModel->get();
        $data['currency_code'] = $settingsModel->currency_code();
		
		/*Use User Library*/
        $user = $this->library('User');
		$data['user'] = $user->data();
		
		/* Use Basket Library */
        $basket = $this->library('Basket');
        $data['cart'] = $basket->all();
        $data['cart_total'] = $basket->subTotal();
		
		/* Amount in Cents */
		$data['amount_cents'] = $this->getMoneyAsCents($data['cart_total']);	
		
		$paymentid = $this->rando();
		
		if($this->url[2] === "success"):
			
			/* Use Product Model */
			$transactionsModel = $this->model('Transactions');
			
			$type = "Razorpay";
			
			$Insert = $transactionsModel->add($data['cart'], $paymentid, $user->data()["userid"], $type);
							
			if ($Insert == 1) {
				$_SESSION['message'][] = ['success', $this->lang['paid_successfully']];
				$basket->clear();
				redirect('user/purchases');
			} else {
				$_SESSION['message'][] = ['warning', $this->lang['error_when_saving']];
				redirect('user/purchases');
			}
		
		elseif($this->url[2] === "cancel"):	
		
			$_SESSION['message'][] = ['warning', $this->lang['you_canceled_the_transaction']];
			redirect('user/purchases');	
			
		endif;	
    }
	
    public function paystack()
    {

        /**
         * The $data array stores all the data that is passed to the views
         */
        $data = [];
		
		/* Use Settings Model */
		$settingsModel = $this->model('Settings');
        $data['settings'] = $settingsModel->get();
        $data['currency_code'] = $settingsModel->currency_code();
		
		/*Use User Library*/
        $user = $this->library('User');
		$data['user'] = $user->data();
		
		/* Use Basket Library */
        $basket = $this->library('Basket');
        $data['cart'] = $basket->all();
        $data['cart_total'] = $basket->subTotal();
		
		/* Use Session Library */
        $session = $this->library('Session');
		$paystack_id = $session->get('paystack_id');
		
		if($this->url[2] != $paystack_id){
			$_SESSION['message'][] = ['error', 'Invalid Paystack Reference Code'];
			redirect('user/purchases');
		}else{	
		
			$paymentid = $this->rando();
			
			/* Use Product Model */
			$transactionsModel = $this->model('Transactions');
			
			$type = "PayStack";
			
			$Insert = $transactionsModel->add($data['cart'], $paymentid, $user->data()["userid"], $type);
							
			if ($Insert == 1) {
				$_SESSION['message'][] = ['success', $this->lang['paid_successfully']];
				$basket->clear();
				$session->delete('paystack_id');
				redirect('user/purchases');
			} else {
				$_SESSION['message'][] = ['warning', $this->lang['error_when_saving']];
				$session->delete('paystack_id');
				redirect('user/purchases');
			}
			
		}

		
    }
	
	
    /**
     * @param $language string
     */
    private function updateLanguage($language)
    {
        setcookie('lang', $language, time() + (10 * 365 * 24 * 60 * 60), COOKIE_PATH);
        redirect();
    }
	
	//Random String
	private function rando($length = 14){
		$str = "";
		$characters = array_merge(range('A','Z'), range('a','z'), range('0','9'));
		$max = count($characters) - 1;
		for ($i = 0; $i < $length; $i++) {
			$rand = mt_rand(0, $max);
			$str .= $characters[$rand];
		}
		return $str;
	}
	
	//Money As Cents
	private function getMoneyAsCents($value)
	{
		// strip out commas
		$value = preg_replace("/\,/i","",$value);
		// strip out all but numbers, dash, and dot
		$value = preg_replace("/([^0-9\.\-])/i","",$value);
		// make sure we are dealing with a proper number now, no +.4393 or 3...304 or 76.5895,94
		if (!is_numeric($value))
		{
			return 0.00;
		}
		// convert to a float explicitly
		$value = (float)$value;
		return round($value,2)*100;
	}
}