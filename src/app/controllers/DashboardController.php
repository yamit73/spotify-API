<?php
use Phalcon\Mvc\controller;

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
    }
}
