<?php

use Phalcon\Mvc\Controller;

/**
 * Class to simple use of API
 */
class IndexController extends Controller
{
    /**
     * Location search
     * Api call
     *
     * @return void
     */    
    public function indexAction()
    {
        $q = $this->request->getPost('search');
        // $response=$client->request('GET', 'search.json?key='.$key.'&q='.$q.'');
        // $this->view->locations=json_decode($response->getBody(), true);
        
    }
}
