<?php
include 'api/auth.php';
include_once 'post_params_methods.php';
include_once 'conf.php';

check_for_previous_login($db_host, $db_name, $db_user, $db_pass, $username);
if (is_user_loggen_in()) {
    try {
        $verification_code = get_input1("verification_code");
        $usql = verified_code_query();
        $user = get_current_user_data();
        $username = $user['username'];
        $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare($usql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':verification_code', $verification_code);
        $stmt->execute();
        $ssql = select_verified_code_query();
        $stmt = $conn->prepare($ssql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $output = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $record = array();
            $record['verified'] = $row['verified'];
            $output = $record;

        }
        if ($output != null){
            $output["success"] = true;
        }else{
            $output["verified"] = 0;
            $output["success"] = false;
        }
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