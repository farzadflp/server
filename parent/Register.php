<?php
include_once "conf.php";
include_once "post_params_methods.php";
include_once "api/auth.php";


/**
 * @param string $db_host
 * @param string $db_name
 * @param string $db_user
 * @param string $db_pass
 * @throws Exception
 */
function registeration_db(string $db_host, string $db_name, string $db_user, string $db_pass): void
{
    try {
        $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_register = parent_registration_query();
        $stmt = bind_to_sql($conn, $sql_register);
        $stmt->execute();
        $response = array();
        $response["success"] = true;
        echo json_encode($response);

    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
    $conn = null;
}

/**
 * @param PDO $conn
 * @param string $sql_register
 * @return bool|PDOStatement
 * @throws Exception
 */
function bind_to_sql(PDO $conn, string $sql_register)
{
    $stmt = $conn->prepare($sql_register);
    $stmt->bindParam(':firstname', get_input1('firstname'));
    $stmt->bindParam(':lastname', get_input1('lastname'));
    $stmt->bindParam(':gender', get_input1('gender'));
    $stmt->bindParam(':username', get_input1('username'));
    $stmt->bindParam(':password', get_input1('password'));
    $stmt->bindParam(':phone_no', get_input1('phone_no'));
    $stmt->bindParam(':id_school', get_input1('id_school'));
    $stmt->bindParam(':child_name', get_input1('child_name'));
    $stmt->bindParam(':st_no_of_child', get_input1('st_no_of_child'));
    return $stmt;
}
if (user_exist_db($db_host, $db_name, $db_user, $db_pass,get_input1('username'))){
    $response = array();
    $response["success"] = false;
    echo json_encode($response);
    die("");
}

registeration_db($db_host, $db_name, $db_user, $db_pass);

