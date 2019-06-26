<?php
include 'api/auth.php';
include_once 'post_params_methods.php';
include_once 'conf.php';
check_for_previous_login($db_host, $db_name, $db_user, $db_pass, $username);
if (is_user_loggen_in()) {
    try {

        $user = get_current_user_data();
        if ($user['role'] == "m") {
            $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $verify = get_input1("verify");
            $sql = null;
            switch ($verify) {
                case "v" :
                    $sql = set_verified_parent_query();
                    break;
                case "u" :
                    $sql = set_unverified_parent_query();
                    break;

            }
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_user', get_input1("id_user"));
            $stmt->execute();
            $output = array();
            $output["success"] = true;
            echo json_encode($output);
        } else{
            $output["success"] = false;
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