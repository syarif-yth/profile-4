<?php
namespace App\Libraries;
require VENDORPATH.'\firebase\php-jwt\src\JWT.php';
require VENDORPATH.'\firebase\php-jwt\src\SignatureInvalidException.php';
require VENDORPATH.'\firebase\php-jwt\src\BeforeValidException.php';
require VENDORPATH.'\firebase\php-jwt\src\ExpiredException.php';
require VENDORPATH.'\firebase\php-jwt\src\JWK.php';
require VENDORPATH.'\firebase\php-jwt\src\Key.php';

Use \Firebase\JWT\JWT;
Use \Firebase\JWT\ExpiredException;
Use \Firebase\JWT\SignatureInvalidException;
Use \Firebase\JWT\Key;
Use \Config\Services;

class JWT_token
{
  # only can access in this library
  protected $key_jwt;
  protected $algorithm;
  protected $header;
  protected $expire;
  protected $auto_regen;
  protected $key_cookie;
  protected $name_cookie;
  protected $request;

  public function __construct()
  {
    # config JWT
    $this->key_jwt = 'JwtSecret';
    $this->algorithm = 'HS256';
    $this->header = 'HTTP_AUTHORIZATION';
    // $this->expire = 60*15;
    $this->expire = 15;
    $this->auto_regen = true;
    
    # for auto refresh token
    $this->key_cookie = 'user';
    
    # save token in cookie for request
    $this->name_cookie = 'token';
    
    # load helper
    helper(['app']);

    # initialization
    $this->request = Services::request();
  }
  
  /**
   * generate token
   * # nip is primarykey for table users
   * # extra is your extra data for encode to jwt
   */
  public function create($nip, $extra = null)
  {
    $user_agent = $this->get_device();
    $exp = time()+$this->expire;
		$payload = array(
			'sub' => $user_agent,
			'aud' => $nip,
			'exp' => $exp);
    if($extra) {
      $payload = array_merge($payload, array('ext' => $extra));
    }

    $encode = $this->encode($payload);
    if($encode['code'] != 200) {
			return $encode;
		} else {
      $remember = $this->request->getVar('remember');
			$this->set_cookie($token, $nip, $remember);
			$token = $encode['body']['token'];
			$body = array('token' => $token,
				'expired' => $exp);
			return for_response($body, 200);
		}
  }

  /**
   * validate token before access
   * you can auto re generate token
   */
  public function validate()
	{
    # check available cookie
    $cookie = $this->get_cookie();
    if($cookie['code'] == 200) {
      # validate by cookie
      $token = $cookie['body']['token'];

      return for_response($token);
    } else {
      # validate by headers request
      $valid = $this->valid_headers();
			return $valid;
    }
  }

  # for logout
  public function destroy()
	{
		helper('cookie');
		delete_cookie($this->name_cookie);
		delete_cookie($this->key_cookie);
	}


  /**
   * ---------------------------------------
   * PRIVATE can access in this library noly
   * ---------------------------------------
   */
  /**
   * detect device user for generate token
   * this used for, token cannot use in other device
   */
  private function get_device()
	{
    $agent = $this->request->getUserAgent();
    $ip_address = $this->request->getIPAddress();
    if(!$agent->isBrowser()) {
      $string = $agent->getAgentString();
      $get = $ip_address.'/'.$string;
    } else {
      $browser = $agent->getBrowser();
      $version = $agent->getVersion();
      $platform = $agent->getPlatform();
      $get = $ip_address.'/'.$browser.'/'.$version.'/'.$platform;
    }
		$get = str_replace(' ','/',$get);
		return strtolower($get);
	}

  # encode JWT
  private function encode($data)
  {
    try {
			$encode = JWT::encode($data, $this->key_jwt, $this->algorithm);
			$body['token'] = $encode;
			$res = for_response($body, 200);
		} catch(\Exception $err) {
			$res = for_response($err->getMessage(), 500);
		}
		return $res;
  }

  # set cookie for autorefresh token
  private function set_cookie($token, $nip, $remember = null)
	{
    helper('cookie');
    $hari = 86400;
		$expire = ($remember) ? 10*$hari : 1*$hari;
    $config_token = array(
      'name' => $this->name_cookie,
      'value' => $token,
      'expire' => $expire);
    set_cookie($config_token);

    $config_user = array(
      'name' => $this->key_cookie,
      'value' => $nip,
      'expire' => $expire);
    set_cookie($config_user);
	}

  # get cookie in device
  private function get_cookie()
	{
		helper('cookie');
		$cookie = get_cookie($this->name_cookie);
		if(empty($cookie)) {
			return for_response('Token not found', 404);
		} else {
			$body['token'] = $cookie;
			return for_response($body);
		}
	}

  # validate token in headers request
  private function valid_headers()
	{
    $head = $this->request->getServer($this->header);
    if(!empty($head)) {
      $token = explode(" ", $head)[1];
      $verify = $this->verify($token);
      if($verify['code'] == 200) {
        $body['token'] = $token;
        return for_response($body);
      } else {
        if($verify['body']['message'] == 'Expired token') {
          if($this->auto_regen) {
            return $this->re_create($token);
          } else { return $verify; }
        } else { return $verify; }
      }
    } else {
      return for_response('Access forbidden!', 403);
    }
	}

  private function decode($token)
  {
    $explode = explode('.', $token);
    list($headb64, $bodyb64, $cryptob64) = $explode;
    $payload = JWT::jsonDecode(JWT::urlsafeB64Decode($bodyb64));
    $body['data'] = $payload;
    return for_response($body);
  }

  private function verify($token)
	{
		try {
			$key = new Key($this->key_jwt, $this->algorithm);
			$verify = JWT::decode($token, $key);
			$body['data'] = $verify;
			return for_response($body, 200);
		} catch(\Exception $err) {
      return for_response($err->getMessage(), 403);
    }
	}

  /**
   * you can auto re generate token must have:
   * - your token expired
   * - key_cookie is available
   * - value key_cookie is same
   * - your device is same
   */
  private function re_create($token)
  {
    $nip = $this->get_nip();
    if($nip) {
      $decode = $this->decode($token);
      $data = $decode['body']['data'];
      # validate value of key_cookie is same 
      if($nip === $data->aud) {
        $device = $this->get_device();
        # validate device for access is same
        if($device === $data->sub) {
          return $this->create($nip);
        } else { return for_response('Access forbidden!', 403); }
      } else { return for_response('Access forbidden!', 403); }
    } else { return for_response('Access forbidden!', 403); }
  }

  private function get_nip()
	{
		helper('cookie');
		$nip = get_cookie($this->key_cookie);
		return (empty($nip)) ? false : $nip;
	}

  
}
?>