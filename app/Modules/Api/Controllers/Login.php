<?php
namespace App\Modules\Api\Controllers;
// require_once VENDORPATH.'\firebase\php-jwt\src\JWT.php';
// require_once VENDORPATH.'\firebase\php-jwt\src\SignatureInvalidException.php';
// require_once VENDORPATH.'\firebase\php-jwt\src\BeforeValidException.php';
// require_once VENDORPATH.'\firebase\php-jwt\src\ExpiredException.php';
// require_once VENDORPATH.'\firebase\php-jwt\src\JWK.php';

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Controller;
// use \Firebase\JWT\JWT;
// Use \Firebase\JWT\Key;

use App\Libraries\JWT_token;

class Login extends Controller
{
  use ResponseTrait;
  public function __construct()
  {

  }

  public function index_get()
  {
    $lib = new JWT_token(); 
    $nip = '20230922155812';

    $res['status'] = true;
    $res['message'] = 'This page login';
    $res['library'] = $lib->create($nip);
    return $this->respond($res, 200);
  }

  public function index_delete()
  {
    $lib = new JWT_token();
    $lib->destroy();
    return $this->respond('oke', 200);
  }

  public function index_post()
  {
    $lib = new JWT_token();
    $res['status'] = true;
    $res['message'] = 'This page login';
    $res['library'] = $lib->validate();
    return $this->respond($res, 200);
  }

}
