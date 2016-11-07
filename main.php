<?php 
date_default_timezone_set("Asia/Tokyo");

/* 错误处理 */
require 'includes/errorHandler.php';

/* 连接数据库 */
require 'includes/db.php';
$mysql->query('start transaction');
$rolled_back = false;
function rollback() {
  global $rolled_back, $mysql;
  $rolled_back = true;
  $mysql->query('rollback');
}


/* 验证访问合法性 */
if(!isset($_SERVER['PATH_INFO'])) {
  throw403('NO_PATH_INFO');
}

require 'config/code.php';
if ($_SERVER['PATH_INFO'] != '/login/authkey' && (!isset($_SERVER['HTTP_X_MESSAGE_CODE']) || $_SERVER['HTTP_X_MESSAGE_CODE'] != hash_hmac('sha1', $_POST['request_data'], $code))) {
  throw403('X-MESSAGE-CODE-WRONG');
}

if (isset($_SERVER['HTTP_USER_ID']) && $_SERVER['HTTP_USER_ID'] == -1) {
  header('Maintenance: 1');
  die();
}

if ($_SERVER['PATH_INFO'] != '/login/authkey' && $_SERVER['PATH_INFO'] != '/login/login' && $_SERVER['PATH_INFO'] != '/login/startUp' && $_SERVER['PATH_INFO'] != '/login/startWithoutInvite') {
  if (isset($_SERVER['HTTP_AUTHORIZE'])) {
    foreach (explode('&', $_SERVER['HTTP_AUTHORIZE']) as $v) {
      $v = explode('=', $v);
      $authorize[$v[0]] = $v[1];
    }
    $res = $mysql->query('SELECT username FROM users WHERE authorize_token=? AND user_id=?', [$authorize['token'], $_SERVER['HTTP_USER_ID']])->fetchColumn();
    if (!$res) {
      throw403('AUTHORIZE_TOKEN_NOT_FOUND');
    }
    $mysql->query('UPDATE users SET nonce=? WHERE authorize_token=? AND user_id=? AND username=?', [$authorize['nonce'], $authorize['token'], $_SERVER['HTTP_USER_ID'], $res]);
    $uid = (int)$_SERVER['HTTP_USER_ID'];
    $banned = $mysql->query("select msg from banned_user where user='$uid' or user='{$res}'")->fetchColumn();
    if ($banned) {
      header('HTTP/1.1 423 USER BANNED');
      $ret['response_data'] = [];
      $ret['status_code'] = 423;
      $ret = json_encode($ret);
      header('X-Message-Code: '.hash_hmac('sha1', $ret, $code));
      header('Content-Type: application/json');
      echo $ret;
      die();
    }
  } else {
    throw403('NOT_SET_PATH_INFO_OR_AUTHORIZE');
  }
}

if (isset($uid)) {
  $params = [];
  foreach ($mysql->query('SELECT * FROM user_params WHERE user_id='.$uid)->fetchAll() as $v) {
    $params[$v['param']] = (int)$v['value'];
  }
  $user = $mysql->query('SELECT name, introduction, level, exp, award, background FROM users WHERE user_id='.$uid)->fetch();
  $__params_bak = $params;
  $__user_bak = $user;
  //如果没有某些常用值，置初值，免得代码里判断
  foreach (['enable_card_switch', 'card_switch', 'random_switch', 'allow_test_func', 'item1', 'item2', 'item3', 'item4', 'item5', 'aqours_flag'] as $name) {
    if (!isset($params[$name])) {
      $params[$name] = 0;
    }
  }
  //访问别名
  $params['social_point'] = &$params['item2'];
  $params['coin'] = &$params['item3'];
  $params['loveca'] = &$params['item4'];
}

/* 维护及更新 */
require 'config/maintenance.php';
//客户端版本
if (!isset($_SERVER['HTTP_BUNDLE_VERSION'])) {
  throw403('NO_BUNDLE_VERSION');
}
if (!isset($_SERVER['HTTP_CLIENT_VERSION'])) {
  throw403('NO_CLIENT_VERSION');
}

if (isset($_SERVER['HTTP_BUNDLE_VERSION']) && preg_match('/^[0-9\.]+$/', $_SERVER['HTTP_BUNDLE_VERSION']) && version_compare($_SERVER['HTTP_BUNDLE_VERSION'], $bundle_ver, '<')) {
  header('Maintenance: 1');
  die();
}

if (isset($params) && isset($restrict_ver) && !$params['allow_test_func'] && $_SERVER['HTTP_BUNDLE_VERSION'] == $restrict_ver) {
  header('Maintenance: 1');
  die();
}

//数据包版本
if (version_compare($_SERVER['HTTP_CLIENT_VERSION'], $server_ver, '<')) {
  if (($_SERVER['HTTP_OS'] == 'Android' && $update_for_android == true) || ($_SERVER['HTTP_OS'] == 'iOS' && $update_for_ios == true)) {
    header("Server-Version: {$_SERVER['HTTP_CLIENT_VERSION']}.$server_ver");
  } else {
    header('Maintenance: 1');
    die();
  }
}
//扩展下载
if (isset($uid)) {
  $res = $mysql->query('
    SELECT extend_download.* FROM extend_download_queue
    LEFT JOIN extend_download
    ON extend_download.ID=extend_download_queue.download_id
    WHERE downloaded_version < version OR downloaded_version=0
    AND extend_download_queue.user_id='.$uid
  )->fetch();
  if (!empty($res)) {
    header("Server-Version: {$_SERVER['HTTP_CLIENT_VERSION']}.$server_ver");
  }
}
//维护
if ($maintenance && isset($uid) && array_search($uid, $bypass_maintenance) === false) {
  header('Maintenance: 1');
  die();
}

/* 处理用户请求 */
$ret['status_code'] = 200;
function retError($statusCode) {
  global $ret;
  $ret['status_code'] = 600;
  return ['error_code' => $statusCode];
}

function runAction($module, $action, $post=[]) {
  global $params;
  if (isset($params) && $params['allow_test_func'] && file_exists('modules.dev/'.$module.'.php')) {
    require_once 'modules.dev/'.$module.'.php';
  } else {
    if (!file_exists('modules/'.$module.'.php')) {
      return [];
    }
    require_once 'modules/'.$module.'.php';
  }
  if (!function_exists($module.'_'.$action)) {
    return [];
  }
  if (empty($post)) {
    return call_user_func($module.'_'.$action);
  }
  return call_user_func($module.'_'.$action, $post);
}

if (isset($_POST['request_data'])) {
  $post = json_decode($_POST['request_data'],true);
} else {
  $post = [];
}
$action = explode('/', $_SERVER['PATH_INFO']);
if (!isset($action[2])) {
  $action[2]='';
}
$ret['response_data'] = runAction($action[1], $action[2], $post);
$ret['release_info'] = isset($release_info) ? $release_info : '[]';

$ret = json_encode($ret);


/* 写回对users和params的修改 */
if (!$rolled_back && isset($__user_bak)) {
  foreach($__user_bak as $k => $v) {
    if ($user[$k] !== $v) {
      $mysql->query('UPDATE users SET name=?, introduction=?, level=?, exp=?, award=?, background=? WHERE user_id=?', [$user['name'], $user['introduction'], $user['level'], $user['exp'], $user['award'], $user['background'], $uid]);
      break;
    }
  }
  foreach($params as $k => $v) {
    if ($k == 'social_point' || $k == 'coin' || $k == 'loveca') { //访问别名
      continue;
    }
    if (!isset($__params_bak[$k])) {
      $mysql->query('insert into user_params values(?, ?, ?)', [$uid, $k, $v]);
    } else if ($__params_bak[$k] !== $v) {
      $mysql->query('update user_params set value=? where user_id=? and param=?', [$v, $uid, $k]);
    }
  }
}
$mysql->query('commit');

header('X-Message-Code: '.hash_hmac('sha1', $ret, $code));
header('Content-Type: application/json');
echo $ret;
