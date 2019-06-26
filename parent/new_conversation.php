<?php
include 'api/auth.php';
include_once 'post_params_methods.php';
include_once 'conf.php';
check_for_previous_login($db_host, $db_name, $db_user, $db_pass, $username);
if (is_user_loggen_in()) {
    try {
        $user_id_one = get_current_user_id();
        $sql = new_conv_query();
        $username_two = get_input1("username_two");
        $category = get_input1("category");
        $accessibility  = get_input1("accessibility");
        $topic  = get_input1("topic");
        $msg = get_input1("msg");
        $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username_two', $username_two);
        $stmt->bindParam(':id_user_one', $user_id_one);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':accessibility', $accessibility);
        $stmt->bindParam(':topic', $topic);
        $stmt->bindParam(':msg', $msg);
        $stmt->execute();
        $response = array();
        $response["success"] = true;
        echo json_encode($response);
    } catch (PDOException $e) {
        echo "ERROR: " . $e->getMessage();
    }
    $conn = null;
} else{
    $output = array();
    $output['user_loginned'] = false;
    $output["success"] = false;
    echo  json_encode($output);

}