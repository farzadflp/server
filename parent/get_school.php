<?php
include '../api/auth.php';
include_once '../api/post_params_methods.php';
include_once '../api/conf.php';
try {

    $sql = get_school_query();

    $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = null ;
    $stmt = $conn->prepare($sql);
    $state = get_input1("state");
    $grade = get_input1("grade");
    $city = get_input1("city");
    $zone = get_input1("zone");
    $stmt->bindParam(':state', $state );
    $stmt->bindParam(':grade', $grade);
    $stmt->bindParam(':myzone', $zone );
    $stmt->bindParam(':city', $city);
    $stmt->execute();
    $output = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $record = array();
        $record['name'] = $row['name'];
        $record['id_school'] = $row['id_school'];
        $output[] = $record;
    }

    if ($output != null) {
        $output['success'] = true;
    } else{
        $output['success'] = false;
    }

    echo json_encode($output);
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage();
}
$conn = null;