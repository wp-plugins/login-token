<?php
/*
Plugin Name: Login Token
Plugin URI: http://wordpress.org/extend/plugins/login-token/
Description: add a hidden filed values token in login form to avoid brute force attack。在后台登录页面添加了一个隐藏的令牌，用来防止暴力破解。
Author: leo108
Version: 1.0
Author URI: http://leo108.com/
*/
session_start();
function login_token() { 
	$_SESSION['login_token'] = md5(rand());
	echo "<input type='hidden' name='login_token' value='{$_SESSION['login_token']}' />";
}
add_action('login_form', 'login_token'); 
if ( !function_exists('wp_authenticate') ) :
function wp_authenticate($username, $password) {
	$username = sanitize_user($username);
	$password = trim($password);

	$user = apply_filters('authenticate', null, $username, $password);
	if($_POST['login_token'] != $_SESSION['login_token'] && $username != '')
	{
		$user = new WP_Error('authentication_failed', 'Login Token Error!');
	}
	if ( $user == null ) {
		$user = new WP_Error('authentication_failed', __('<strong>ERROR</strong>: Invalid username or incorrect password.'));
	}

	$ignore_codes = array('empty_username', 'empty_password');

	if (is_wp_error($user) && !in_array($user->get_error_code(), $ignore_codes) ) {
		do_action('wp_login_failed', $username);
	}
	return $user;
}
endif;
?>