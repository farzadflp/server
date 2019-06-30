<?php
include '../api/auth.php';
include_once '../api/post_params_methods.php';
include_once '../api/conf.php';
check_for_previous_login($db_host, $db_name, $db_user, $db_pass, $username);
if (is_user_loggen_in()) {
    try {
        $sql = add_community_query();
        $user = get_current_user_data();
        if ($user['role'] == "m") {
            $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $conn->prepare($sql);
            $verified = 1;
            $stmt->bindParam(':firstname', get_input1('firstname'));
            $stmt->bindParam(':lastname', get_input1('lastname'));
            $stmt->bindParam(':gender', get_input1('gender'));
            $stmt->bindParam(':username', get_input1('username'));
            $stmt->bindParam(':password', get_input1('password'));
            $stmt->bindParam(':phone_no', get_input1('phone_no'));
            $stmt->bindParam(':role', get_input1('role'));
            $stmt->bindParam(':id_school', get_input1('id_school'));
            $stmt->bindParam(':degree', get_input1('degree'));
            $stmt->bindParam(':course', get_input1('course'));
            $stmt->bindParam(':post', get_input1('post'));
            $stmt->bindParam(':tel_work', get_input1('tel_work'));
            $stmt->bindParam(':address_work', get_input1('address_work'));
            $stmt->bindParam(':verified', $verified);
            $stmt->execute();
            $output = array();
            $output["success"] = true;
            echo json_encode($output);

        }
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
