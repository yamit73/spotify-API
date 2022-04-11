<?php

use Phalcon\Mvc\Controller;

/**
 * Class to simple use of API
 */
class IndexController extends Controller
{
    /**
     *  search
     * Api call
     *
     * @return void
     */    
    public function indexAction()
    {
        if ($this->request->isPost()) {
            if ($this->request->getPost('search') !='' && $this->request->getPost('filters')) {
                $data=$this->request->getPost();
                $client = $this->client;
                $accessToken=$this->session->access;
                $response=$client->request('GET', 'search?q='.urlencode($data['search']).'&type='.implode(',', $data['filters']).'', ['headers' => ['Authorization'=>'Bearer '.$accessToken['access_token']]]);
                $details=json_decode($response->getBody(),true);
                $this->view->details=$details;
            }
        }

    }

    /**
     * Function to add tracks, to playlist
     *
     * @param [type] $trackId
     * @return void
     */
    
    public function addtoplaylistAction($trackId)
    {
        $client = $this->client;
        $accessToken=$this->session->access;
        //getting all the playlist of users
        $response=$client->request('GET', 'me/playlists', ['headers' => ['Authorization'=>'Bearer '.$accessToken['access_token']]]);
        $response=json_decode($response->getBody(),true);
        $playlists=array();
        foreach ($response['items'] as $v) {
            $playlists[$v['name']]=$v['id'];
        }
        $this->view->playlists=$playlists;

        //Adding track to selected playlist
        if ($this->request->isPost()) {
            $playlistId=$this->request->getPost('playlistId');
            $client = $this->client;
            //Setting headers of the request
            $headers=[
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.$accessToken['access_token']
            ];
            $track='{"uris":["spotify:track:'.$trackId.'"]}';
            //Sending response as POST
            $response=$client->request('POST', 'playlists/'.$playlistId.'/tracks',  ['headers' => $headers, 'body' => $track]);
        }
    }
}
