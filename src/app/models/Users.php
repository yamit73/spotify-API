<?php
use Phalcon\Mvc\Model;

class Users extends Model
{
    public $id;
    public $name;
    public $refresh_token;
    public $email;
}