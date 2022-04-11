<?php

use Phalcon\Mvc\Controller;
use GuzzleHttp\RequestOptions;
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
        $client = $this->client;
        $accessToken=$this->session->access;
        $response=$client->request('GET', 'me/playlists', ['headers' => ['Authorization'=>'Bearer '.$accessToken['access_token']]]);
        $this->view->playlists=json_decode($response->getBody(),true);
    }

    /**
     * Display details of playlist
     * Api call
     *
     * @return void
     */    
    public function detailsAction($playlistId)
    {
        $client = $this->client;
        $accessToken=$this->session->access;
        $response=$client->request('GET', 'playlists/'.$playlistId.'/tracks', ['headers' => ['Authorization'=>'Bearer '.$accessToken['access_token']]]);
        $tracks=json_decode($response->getBody(),true);
        $tracks['playlistId']=$playlistId;
        $this->view->tracks=$tracks;
    }

    /**
     * create new playlist
     *
     * @return void
     */
    public function createAction()
    {
        if ($this->request->isPost()) {
            $data=$this->request->getPost();
            if ($data['name'] != '' && $data['description'] != '') {
                //converting form data in json format for request body
                $bodyData=json_encode($data);
                $client = $this->client;
                $accessToken=$this->session->access;
                //setting request header
                $headers=[
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer '.$accessToken['access_token']
                ];
                //sending request to create playlist
                $response=$client->request('POST', 'users/31b26ay3l7nfhyl5rizz6aml5l4y/playlists', ['headers'=>$headers, 'body'=>$bodyData]);
                $this->view->details=$response->getBody();
                
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
        $client = $this->client;
        $accessToken=$this->session->access;
        $playlistId=$this->request->getQuery('playlistId');
        $client = $this->client;
        //Setting headers to send with request
        $headers=[
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$accessToken['access_token']
        ];
        $track='{ "tracks": [{ "uri": "spotify:track:'.$trackId.'" }] }';
        //Sending request as DELETE
        $response=$client->request('DELETE', 'playlists/'.$playlistId.'/tracks',  ['headers' => $headers, 'body' => $track]);
        echo($response->getBody());
        die;
    }
}
