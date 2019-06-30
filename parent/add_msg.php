<?php
include '../api/auth.php';
include_once '../api/post_params_methods.php';
include_once '../api/conf.php';
check_for_previous_login($db_host, $db_name, $db_user, $db_pass, $username);
if (is_user_loggen_in()) {
    try {
        $sql = add_msg_query() ;
        $user = get_current_user_data();
        $id_user= $user['id_user'];
        $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_user', $id_user);
        $stmt->bindParam(':id_conversation', get_input1("id_conversation"));
        $stmt->bindParam(':msg', get_input1("msg"));
        $stmt->execute();
        $output = array();
        $output["success"] = true;
        echo json_encode($output);
    } catch (PDOException $e) {
        echo "ERROR: " . $e->getMessage();
    }
    $conn = null;
} else{
    $output = array();
    $output["success"] = false;
    echo  json_encode($output);

}