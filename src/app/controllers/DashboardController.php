<?php

use Phalcon\Mvc\controller;
use GuzzleHttp\Exception\ClientException;

class DashboardController extends controller
{
    /**
     * Dashboard action
     *
     * @return void
     */
    public function indexAction()
    {
        if (!$this->session->id) {
            $this->response->redirect('/users/login');
        }
        /**
         * Current user's profile detail
         */
        try {
            $client = $this->client;
            $accessToken = $this->session->access;
            //Get details of current spotify user
            $response = $client->request('GET', 'me', ['headers' => ['Authorization' => 'Bearer ' . $accessToken['access_token']]]);
            $userDetails = json_decode($response->getBody(), true);
            $this->session->spotify_user_id = $userDetails['id'];
            $this->view->userDetails = $userDetails;

            //Get recommendation list
            $headers = [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $accessToken['access_token']
            ];
            $response = $client->request('GET', 'recommendations?seed_artists=4YRxDV8wJFPHPTeXepOstw&seed_genres=classical,country&seed_tracks=4Ld4kbKo1eOzToQ0P8JA0V,0Yld4eVEV6rBvpijVU2s6l&market=IN',['headers'=>$headers]);
            $this->view->recommendation = json_decode($response->getBody(), true);
        } catch (ClientException $e) {
            // creating an object of event manager
            $eventsManagers = $this->di->get('EventsManager');
            // firing event to get token
            $eventsManagers->fire('events:getAccessTokenUsingRefresh', $this);
        }
    }
}
