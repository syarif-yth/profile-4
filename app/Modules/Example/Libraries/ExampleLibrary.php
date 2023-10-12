<?php

namespace Example\Libraries;

use CodeIgniter\HTTP\Response;
use Example\Models\ExampleModel;

class ExampleLibrary
{
    public $response;

    public function __construct()
    {
        $config = config(App::class);
        $this->response = new Response($config);
    }
}
