<?php
include_once "post_params_methods.php";
include "api/auth.php";
include_once 'api/db.php';
include_once 'conf.php';
$username = get_input1("username");
$password = get_input1("password");
$role = get_input1("role");

echo login_db($db_host, $db_name, $db_user, $db_pass, $username, $password , $role);


?>
