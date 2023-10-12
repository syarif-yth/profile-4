<?php

namespace Example\Controllers;

use App\Controllers\BaseController;
use Example\Models\ExampleModel;

class ExampleController extends BaseController
{
    public $data = [];

    /**
     * Constructor.
     *
     */
    public function __construct()
    {
        // ...
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return 'This user modules cli';
    }

}
