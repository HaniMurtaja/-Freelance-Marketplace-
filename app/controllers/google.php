<?php

namespace Fir\Controllers;

// Import Hybridauth's namespace
use Hybridauth\Hybridauth;

class Google extends Controller
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
		$userProfile = [];	

        $user = $this->library('User');
        $session = $this->library('Session');
		
		/*Use User Model*/
        $userModel = $this->model('User');
		
		/* Use Settings Model */
		$settingsModel = $this->model('Settings');
        $data['settings'] = $settingsModel->get();

        $config = [
            // Location where to redirect users once they authenticate with a provider
            'callback' => URL_PATH.'/google',
        
            // Providers specifics
            'providers' => [
                'Google' => [
                    'enabled' => true,     // Optional: indicates whether to enable or disable Twitter adapter. Defaults to false
                    'keys'     => [
                                    'id' => getenv('GOOGLE_CLIENT_ID'),
                                    'secret' => getenv('GOOGLE_CLIENT_SECRET')
                                ],
                ]
            ]
        ];        
		 
        try{
            //Feed configuration array to Hybridauth
            $hybridauth = new \Hybridauth\Hybridauth($config);
            
            //Then we can proceed and sign in with Twitter as an example. If you want to use a diffirent provider,
            //simply replace ‘Twitter’ with ‘Google’ or ‘Facebook’.
            
            //Attempt to authenticate users with a provider by name
            $adapter = $hybridauth->authenticate('Google');
            
            //Returns a boolean of whether the user is connected with Twitter
            $isConnected = $adapter->isConnected();
            
            //Retrieve the user’s profile
            $userProfile = $adapter->getUserProfile();

            $userArray =  (array) $userProfile;
            
            //Inspect profile’s public attributes
            //var_dump($userProfile);
            
            //Disconnect the adapter
            $adapter->disconnect();
            }
            catch(\Exception $e){
                redirect('login');
            }	     

            $hasEmail = $userModel->hasEmail($userProfile->email);
            if($hasEmail == true){
                $userData = $userModel->getEmail($userProfile->email);
                $session->put('waveUser', $userData["userid"]);

                if($userData["user_type"] === "1"){
                    redirect(FREELANCER_URL.'/dashboard');
                 }elseif($userData["user_type"] === "2"){
                    redirect(CLIENT_URL.'/dashboard');
                 }                
            }
            $data['hasEmail'] = $hasEmail;
            $data['user'] = $userProfile;	

            return ['content' => $this->view->render($data, 'home/google')];
    }
	

}