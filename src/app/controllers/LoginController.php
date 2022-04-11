<?php

use Phalcon\Mvc\Controller;
use GuzzleHttp\Client;

/**
 * Class to simple connect account of spotify
 */
class LoginController extends Controller
{
    /**
     * function
     * Get API token
     *
     * @return void
     */  
    public function indexAction()
    {
        //Getting authorise code from url
        $code=$this->request->getQuery('code');
        if ($this->request->isPost() || isset($code)) {
            $clientId=$this->di->get('config')->get('app')->get('clientId');
            $secret=$this->di->get('config')->get('app')->get('secret');
            //Redirct url for spotify API
            $redirectUri='http://localhost:8080/login';

            //Defining the scope of authorisation
            $scope='playlist-read-collaborative playlist-modify-public playlist-read-private playlist-modify-private';
            //Sending request to the spotify authorise API to get authorise code
            $this->response->redirect('https://accounts.spotify.com/authorize?response_type=code&client_id='.$clientId.'&scope='.$scope.'&redirect_uri='.$redirectUri.'');
            
            //Check if authorise code set
            if ($code){
                //header for api/token
                $headers = [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Authorization' => 'Basic '.base64_encode($clientId.":".$secret)
                ];  
                //New client to post data to 
                $client = new Client([
                    // Base URI is used with relative requests
                    'base_uri' => 'https://accounts.spotify.com',
                    'headers' => $headers
                ]);
                //A query array for url
                $urlQuery=['code' => $code, 'redirect_uri'=>$redirectUri, 'grant_type' => 'authorization_code'];
                
                //Sending post request to get the APi token
                $token=$client->request('POST', '/api/token', ['form_params'=>$urlQuery]);
                // var_dump($token->getBody());die;

                $res=json_decode($token->getBody(), true);
                $this->session->access=$res;
                $this->response->redirect('/');
            }
        }
    }
}
