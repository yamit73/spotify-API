<?php

use Phalcon\Mvc\Controller;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Exception\ClientException;

/**
 * Class to simple use of API
 */
class PlaylistController extends Controller
{
    /**
     * Display all playlist
     * Api call
     *
     * @return void
     */
    public function indexAction()
    {
        //Check if user is logged in or not
        if (!$this->session->id) {
            $this->response->redirect('/users/login');
        }
        try {
            $client = $this->client;
            $accessToken = $this->session->access;
            $response = $client->request('GET', 'me/playlists', ['headers' => ['Authorization' => 'Bearer ' . $accessToken['access_token']]]);
            $this->view->playlists = json_decode($response->getBody(), true);
        } catch (ClientException $e) {
            //creating an object of event manager
            $eventsManagers = $this->di->get('EventsManager');
            //firing event to get token
            $eventsManagers->fire('events:getAccessTokenUsingRefresh', $this);
        }
    }

    /**
     * Display details of playlist
     * Api call
     *
     * @return void
     */
    public function detailsAction($playlistId)
    {
        //Check if user is logged in or not
        if (!$this->session->id) {
            $this->response->redirect('/users/login');
        }
        try {
            $client = $this->client;
            $accessToken = $this->session->access;
            $response = $client->request('GET', 'playlists/' . $playlistId . '/tracks', ['headers' => ['Authorization' => 'Bearer ' . $accessToken['access_token']]]);
            $tracks = json_decode($response->getBody(), true);
            $tracks['playlistId'] = $playlistId;
            $this->view->tracks = $tracks;
        } catch (ClientException $e) {
            //creating an object of event manager
            $eventsManagers = $this->di->get('EventsManager');
            //firing event to get token
            $eventsManagers->fire('events:getAccessTokenUsingRefresh', $this);
        }
    }

    /**
     * create new playlist
     *
     * @return void
     */
    public function createAction()
    {
        //Check if user is logged in or not
        if (!$this->session->id) {
            $this->response->redirect('/users/login');
        }
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            if ($data['name'] != '' && $data['description'] != '') {
                try {
                    //converting form data in json format for request body
                    $bodyData = json_encode($data);
                    $client = $this->client;
                    $accessToken = $this->session->access;
                    //setting request header
                    $headers = [
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $accessToken['access_token']
                    ];
                    //sending request to create playlist
                    $response = $client->request('POST', 'users/'.$this->session->spotify_user_id.'/playlists', ['headers' => $headers, 'body' => $bodyData]);
                    $this->view->details = $response->getBody();
                } catch (ClientException $e) {
                    //creating an object of event manager
                    $eventsManagers = $this->di->get('EventsManager');
                    //firing event to get token
                    $eventsManagers->fire('events:getAccessTokenUsingRefresh', $this);
                }
            }
        }
    }

    /**
     * To remove a track from playlist
     *
     * @param [string] $trackId
     * @return void
     */
    public function removeTrackAction($trackId)
    {
        //Check if user is logged in or not
        if (!$this->session->id) {
            $this->response->redirect('/users/login');
        }
        try {
            $client = $this->client;
            $accessToken = $this->session->access;
            $playlistId = $this->request->getQuery('playlistId');
            $client = $this->client;
            //Setting headers to send with request
            $headers = [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $accessToken['access_token']
            ];
            $track = '{ "tracks": [{ "uri": "spotify:track:' . $trackId . '" }] }';
            //Sending request as DELETE
            $response = $client->request('DELETE', 'playlists/' . $playlistId . '/tracks',  ['headers' => $headers, 'body' => $track]);
            echo ($response->getBody());
            die;
        } catch (ClientException $e) {
            //creating an object of event manager
            $eventsManagers = $this->di->get('EventsManager');
            //firing event to get token
            $eventsManagers->fire('events:getAccessTokenUsingRefresh', $this);
        }
    }
}
