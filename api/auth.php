<?php
include_once 'db.php';
$current_user = null;
$current_user_id = null;

define('SESSION_EXPIRATION_TIME', 30 * 24 * 3600);


function get_current_user_data()
{
    global $current_user;
    return $current_user;
}

function get_current_user_id()
{
    global $current_user_id;
    return $current_user_id;
}

function is_user_loggen_in()
{
    global $current_user_id;
    if ($current_user_id) {
        return true;
    }
    return false;
}

function clear_user_session()
{
    unset($_SESSION['last_access']);
    unset($_SESSION['id_user']);
    unset($_SESSION['username']);
    unset($_SESSION['password']);
    unset($_SESSION['role']);
}

function check_for_previous_login(string $db_host, string $db_name, string $db_user, string $db_pass)
{
    $last_access = $_SESSION['last_access'];
    $expired = ((time() - $last_access) > SESSION_EXPIRATION_TIME);
    if ($expired) {
        clear_user_session();
        return;
    }

    $username = $_SESSION['username'];

    $user = get_user_db($db_host, $db_name, $db_user, $db_pass, $username);
    if ($user) {
        $user_id = $_SESSION['id_user'];
        if ($user_id != $user['id_user']) {
            clear_user_session();
            return;
        }

        $password = $_SESSION['password'];
        if ($password != $user['password']) {
            clear_user_session();
            return;
        }

        global $current_user;
        global $current_user_id;

        $current_user = $user;
        $current_user_id = $user['id_user'];
    }
}

function user_logout()
{
    global $current_user;
    global $current_user_id;
    $current_user = null;
    $current_user_id = null;
    clear_user_session();
}

function login_db(string $db_host, string $db_name, string $db_user, string $db_pass, $username, string $password , string $role)
{
    try {
        user_logout();
        $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = null ;
        if ($role == "p"){
            $stmt = $conn->prepare(parent_login_query());
        } else if ($role == "c"){
            $stmt = $conn->prepare(community_login_query());
        } else if ($role == "m"){
            $stmt = $conn->prepare(manager_login_query());
        } else{
        }
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
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

            if ($role == "p"){
                $record['child_name'] = $row['child_name'];
                $record['st_no_of_child'] = $row['st_no_of_child'];
                $record['verified_by_m'] = $row['verified_by_m'];
            } else if ($role == "c"){
                $record['post'] = $row['post'];
                $record['degree'] = $row['degree'];
                $record['course'] = $row['course'];
                $record['address'] = $row['address'];
                $record['tel'] = $row['tel'];
                $record['address_work'] = $row['address_work'];
                $record['tel_work'] = $row['tel_work'];
            } else if ($role == "m"){
                $record['degree'] = $row['degree'];
                $record['course'] = $row['course'];
            } else{

            }

            $output[] = $record;
        }

        if ($output != null) {

            $output['success'] = true;
            $output['sess_id'] = session_id();
            $output['sess_name'] = session_name();
            global $current_user;
            global $current_user_id;

            $current_user = $username;
            $current_user_id = $record['id_user'];

            $_SESSION['last_access'] = time();
            $_SESSION['id_user'] = $current_user_id;
            $_SESSION['username'] = $username;
            $_SESSION['password'] = $password;
            $_SESSION['role'] = $role;
            if ($record['verified'] == 0){
                $stmt = $conn->prepare(creat_verification_code_query());
                $stmt->bindParam(':username', $username);
                $stmt->execute();
            }
        } else{
            $output['success'] = false;
        }
        echo json_encode($output);
    } catch (PDOException $e) {
        echo "ERROR: " . $e->getMessage();
    }
    $conn = null;
}

function get_user_db(string $db_host, string $db_name, string $db_user, string $db_pass, string $username)
{
    try {

        $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT
                    `id_user`,
                    `password`,
                    `username`,
                    `role`
                FROM
                    `User`
                WHERE
                    `username` = :username";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        global $data;
        $data = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $record = array();
            $record['id_user'] = $row['id_user'];
            $record['password'] = $row['password'];
            $record['username'] = $row['username'];
            $record['role'] = $row['role'];
            $data = $record;

        }
        return $data;
    } catch (PDOException $e) {
        echo "ERROR: " . $e->getMessage();
    }
    $conn = null;

}
function user_exist_db(string $db_host, string $db_name, string $db_user, string $db_pass, string $username)
{
    try {

        $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass,array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT
                    `username`
                FROM
                    `User`
                WHERE
                    `username` = :username";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $output = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $record = array();
            $record['username'] = $row['username'];
            $output[] = $record;

        }
        if($record){
            return true;
        }
        return false;
    } catch (PDOException $e) {
        echo "ERROR: " . $e->getMessage();
    }
    $conn = null;

}