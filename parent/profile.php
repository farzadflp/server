<?php
include 'api/auth.php';
include_once 'post_params_methods.php';
include_once 'conf.php';
check_for_previous_login($db_host, $db_name, $db_user, $db_pass, $username);
if (is_user_loggen_in()) {
    try {
        $sql = inbox_query();
        $user = get_current_user_data();
        $username = $user['username'];
        $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $output = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $record = array();
            $record['id_two'] = $row['id_two'];
            $record['username_two'] = $row['username_two'];
            $record['id_conversation'] = $row['id_conversation'];
            $record['topic'] = $row['topic'];
            $record['date_time_conv'] = $row['date_time_conv'];
            $record['category'] = $row['category'];
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
    $output["success"] = false;
    echo  json_encode($output);

}