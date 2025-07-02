<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Test extends ClientsController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        echo "Test controller with ClientsController is working!";
        die();
    }
}