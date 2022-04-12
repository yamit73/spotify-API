<?php
namespace App\Events;
use Phalcon\Di\Injectable;
use Phalcon\Events\Event;
use Users;
use GuzzleHttp\Client;

class EventListener extends Injectable
{
   public function getAccessTokenUsingRefresh($event, $controller)
   {
        $user=Users::findFirst($this->session->id);
        //Get refresh token from DB
        $refreshToken=$user->refresh_token;
        $clientId=$this->di->get('config')->get('app')->get('clientId');
        $secret=$this->di->get('config')->get('app')->get('secret');
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
        $urlQuery=['refresh_token' => $refreshToken, 'grant_type' => 'refresh_token'];
        
        //Sending post request to get the APi token
        $token=$client->request('POST', '/api/token', ['form_params'=>$urlQuery]);
        $res=json_decode($token->getBody(), true);
        //update token into database
        $user=Users::findFirst($this->session->id);
        $user->access_token= $res['access_token'];
        $user->save();
        
        $this->session->access=$res;
   }
}
