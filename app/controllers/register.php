<?php

namespace Fir\Controllers;
 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Register extends Controller
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
			
		$validation = "";	
		
		/* Use User Model */
        $user = $this->library('User');
		if($user->isLoggedIn() === true):
		 redirect('user/dashboard');
		endif;
		
		/* Use Settings Model */
		$settingsModel = $this->model('Settings');
        $data['settings'] = $settingsModel->get();
		
		/* Use Input Library */
		$input = $this->library('Input');
		
		/* Use User Model */
		$userModel = $this->model('User');

        // If the user tries to log-in
		if(isset($_POST['register'])) {
		 if ($input->exists()) {
		
			$validator = $this->library('Validator');
			
			$validation = $validator->check($_POST, [
				  'name' => [
					 'required' => true,
					 'minlength' => 2,
				   ],
				  'email' => [
					 'required' => true,
					 'email' => true,
					 'minlength' => 2,
				  ],		
				   'password' => [
					 'required' => true,
				   ],
				   'confirmPassword' => [
					 'match' => 'password'
				   ]
			]);
				 
			if (!$validation->fails()) {
				
				$has = $userModel->hasEmail($input->get('email'));
				if($has === false):
		
					/* Hass Password */
					$password = password_hash($input->get('password'), PASSWORD_DEFAULT);
					
					/* Unique ID */	
					$userid = $this->uniqueid();	
					$slug = $this->slugify($input->get('name'));	
					
					$remember = null;			
			
					$Insert = $userModel->add($userid, $input->get('name'), $slug, $input->get('email'), $password, $input->get('user_type'));
					
					if ($Insert == 1) {
                
                        if($data['settings']['verify_email'] == 1){

                                // Attempt to auth the user
                                $auth = $user->login(
                                     $input->get('email'),
                                     $input->get('password'),
                                     $remember
                                  );

                                // If the user has been logged-in
                                if($auth) {

                                 if($user->data()["user_type"] === "1"){
                                    redirect(FREELANCER_URL.'/dashboard');
                                 }elseif($user->data()["user_type"] === "2"){
                                    redirect(CLIENT_URL.'/dashboard');
                                 }

                                }
                                // If the user could not be logged-in
                                elseif(isset($_POST['login'])) {
                                    $_SESSION['message'][] = ['error', $this->lang['invalid_user_pass']];
                                } 

							}elseif($data['settings']['verify_email'] == 2){
                        
                        
                                $has = $userModel->hasEmail($input->get('email'));
                                if($has === true):

                                    $token = md5(uniqid());

                                    $m = $userModel->getEmail($input->get('email'));
                                    $Update = $userModel->registertoken($m['userid'], $token); 

                                    if ($Update == 1) {


                                        $id = base64_encode($m["userid"]);

                                        $mail = new PHPMailer;

                                        //Server settings
                                        $mail->isSMTP();                                      // Set mailer to use SMTP
                                        $mail->Host = $data['settings']['smtp_host'];  // Specify main and backup SMTP servers
                                        $mail->SMTPAuth = true;                               // Enable SMTP authentication
                                        $mail->Username = $data['settings']['smtp_username'];                 // SMTP username
                                        $mail->Password = $data['settings']['smtp_password'];                           // SMTP password
                                        $mail->SMTPSecure = $data['settings']['smtp_encryption'];                                  // Enable TLS encryption, `ssl` also accepted				
                                        $mail->Port = $data['settings']['smtp_port'];                                    // TCP port to connect to	

                                         $mail->setFrom($data['settings']['smtp_username'], $data['settings']['sitename']);
                                         $mail->addAddress($m["email"], $m["name"]);
                                         $mail->Subject = "Verify Email - " .$data['settings']['sitename'];
                                         $mail->isHTML(true);
                                         $mail->Body = "
                                               <p>Hello ". $m["name"] ."</p>
                                               <p>Verify your Email to be able to Login to ". $data['settings']['sitename'] .",.</p>
                                               <p>Click Following Link To Verify Email</p> 
                                               <a href='". URL_PATH ."/verify/$id/$token'>Verify Email</a>
                                               <p>Thank you.</p>
                                         ";
                                         $mail->send();			

                                        $_SESSION['message'][] = ['success', $this->lang['email_sent']];
                                        redirect('register');		

                                    } else {
                                        $_SESSION['message'][] = ['warning', $this->lang['error_when_saving']];
                                        redirect('register');
                                    }
                                elseif($has === false):
                                        $_SESSION['message'][] = ['warning', $this->lang['email_not_available']];
                                        redirect('register');
                                endif;	                        
                        
							}
						
					} else {
						$_SESSION['message'][] = ['warning', $this->lang['error_when_saving']];
						redirect('register');
					}
				elseif($has === true):
						$_SESSION['message'][] = ['warning', $this->lang['email_is_available']];
						redirect('register');
                endif;				
					

			}else {
			 foreach ($validation->errors()->all() as $err) {
				$str = implode(" ",$err);
				 foreach ($err as $r) {
					$_SESSION['errors'][] = ['error', $r];
				 }	
			 }
						redirect('register');
			}
		 }	
		}

        return ['content' => $this->view->render($data, 'home/register')];
    }


	
	//Random String
	function uniqueid()
	{
		$un = substr(number_format(time() * rand(),0,'',''),0,12);
		return $un;
	}
	/**
	 * Return the slug of a string to be used in a URL.
	 *
	 * @return String
	 */
	function slugify($text){
		// replace non letter or digits by -
		$text = preg_replace('~[^\pL\d]+~u', '-', $text);

		// transliterate
		$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

		// remove unwanted characters
		$text = preg_replace('~[^-\w]+~', '', $text);

		// trim
		$text = trim($text, '-');

		// remove duplicated - symbols
		$text = preg_replace('~-+~', '-', $text);

		// lowercase
		$text = strtolower($text);

		if (empty($text)) {
		  return 'n-a';
		}

		return $text;
	}
}