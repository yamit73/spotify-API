<?php

use Phalcon\Mvc\Controller;

class UsersController extends Controller
{
    /**
     * User registration function
     *
     * @return void
     */
    public function signupAction()
    {
        if ($this->request->isPost()) {
            $user = new Users();
            $postData = $this->request->getPost();
            //filtering form data with escaper
            $escaper=new \App\Components\MyEscaper();
            $data=$escaper->sanitize($postData);
            //checking if user exist or not
            if (!Users::findFirst('email="'.$data['email'].'"')) {
                //cheking if password entered by the user is same in both input
                if ($data['password'] === $data['confirmPassword']) {
                    $user->assign(
                        $data,
                        [
                            'name',
                            'email',
                            'password'
                        ]
                    );
                    if ($user->save()) {
                        //if registration is succesfull redirect to the login page
                        $this->view->message="registered successfully!!";
                    } else {
                        //if registration failed then display the error message
                        $this->view->message = "Not created: <br>" . implode("<br>", $user->getMessages());
                    }
                } else {
                    //if password did not match then display a message and enter it to signup log file
                    $this->view->message = "Password did not match";
                }
            } else {
                $this->view->message = "Email already registered try with different email!";
            }
        }
            
    }

    /**
     * Login action controller
     *
     * @return void
     */
    public function loginAction()
    {
        //filtering the data with escaper
        $postData = $this->request->getPost();
        $escaper=new \App\Components\MyEscaper();
        $data=$escaper->sanitize($postData);
        //checking if user has entered the email and password
        if (isset($data['email']) && isset($data['password'])) {
            //sending request to model for finding the user
            $user=Users::findFirst(
                [
                    'email = :email: AND password = :password:',
                    'bind' => [
                        'email' => $data['email'],
                        'password' => $data['password'],
                    ],
                ]
            );
            //checking if the user exist or not
            if ($user) {
                //if user exist then set the session
                $this->session->set('id', $user->id);
                $this->session->set('name', $user->name);
                $this->session->set('refresh_token', $user->refresh_token);
                $this->response->redirect('dashboard');
            } else {
                $this->view->message="Authentication failed!, wrong credentials";
            }
            
        } else {
            $this->view->message='input Field should not be empty';
        }
    }
    /**
     * Logout action
     *
     * @return void
     */
    public function logoutAction()
    {
        $this->session->destroy();
        $this->response->redirect('');
    }
}
