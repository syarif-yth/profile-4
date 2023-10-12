<?php
$CI_INSTANCE = [];

function register_ci_instance(\App\Controllers\BaseController &$_ci) {
    global $CI_INSTANCE;
    $CI_INSTANCE[0] = &$_ci;
}

function &get_instance(): \App\Controllers\BaseController {
    global $CI_INSTANCE;
    return $CI_INSTANCE[0];
}

/**
 * helper return for consumable response restcontroller
 * like this '$this->respond($res['body'], $res['code']);'
 */
function for_response($data, $code = null)
{
  if(!$code) {
    $status = array('status' => true);
    $code = 200;
  } else {
    $status = array('status' => ($code != 200) ? false : true);
  }

  $res['code'] = $code;
  if(is_array($data)) {
    $res['body'] = array_merge($status, $data);
  } else {
    $res['body'] = array_merge($status, 
      array('message' => $data));
  }
  return $res;
}


/**
 * helper for debuging model errors
 */
function db_error($err)
{
  $res['code'] = 500;
  $res['body'] = array('status' => false,
    'message' => $err['message']);
  return $res;
}

?>