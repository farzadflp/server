<?php
include '../api/auth.php';
include_once '../api/post_params_methods.php';
include_once '../api/conf.php';
check_for_previous_login($db_host, $db_name, $db_user, $db_pass, $username);
if (is_user_loggen_in()) {
    try {
        $sql = get_msg_query();
        $user = get_current_user_data();
        $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_conversation', get_input1("id_conversation"));
        $stmt->execute();
        $output = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $record = array();
            $record['id_msg'] = $row['id_msg'];
            $record['id_user'] = $row['id_user'];
            $record['username'] = $row['username'];
            $record['id_conversation'] = $row['id_conversation'];
            $record['msg'] = $row['msg'];
            $record['date_time_msg'] = $row['date_time_msg'];
            $output[] = $record;

        }
        $output["success"] = true;
        echo json_encode($output);
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
/**
 * Created by PhpStorm.
 * User: farzad
 * Date: 2019-03-08
 * Time: 05:02
 */