<?php
include '../api/auth.php';
include_once '../api/post_params_methods.php';
include_once '../api/conf.php';
check_for_previous_login($db_host, $db_name, $db_user, $db_pass, $username);
if (is_user_loggen_in()) {
    try {
        $sql = notverified_parent_query();
        $user = get_current_user_data();
        if ($user['role'] == "m") {
            $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_school', get_input1("id_school"));
            $stmt->execute();
            $output = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $record = array();
                $record['id_user'] = $row['id_user'];
                $record['id_school'] = $row['id_school'];
                $record['firstname'] = $row['firstname'];
                $record['lastname'] = $row['lastname'];
                $record['gender'] = $row['gender'];
                $record['role'] = $row['role'];
                $record['username'] = $row['username'];
                $record['phone_no'] = $row['phone_no'];
                $record['verified'] = $row['verified'];
                $record['child_name'] = $row['child_name'];
                $record['st_no_of_child'] = $row['st_no_of_child'];
                $record['verified_by_m'] = $row['verified_by_m'];
                $output[] = $record;

            }
            if ($output != null) {
                $output['success'] = true;
            } else{
                $output['success'] = false;
            }
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
