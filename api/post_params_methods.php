<?php

function prepare_input($data) {
    return trim(htmlspecialchars(stripcslashes($data)));
}
function isPOST(){
    if($_SERVER['REQUEST_METHOD'] == "POST"){
        return true;
    }
    return false;
}

function get_input($input_name) {
    if(isset($_POST[$input_name])) {
        return prepare_input($_POST[$input_name]);
    }
    return null;
}

function get_input1($input_name) {
    if(isPOST()){
        if(isset($_REQUEST[$input_name])) {
            return prepare_input($_REQUEST[$input_name]);
        }
    }
    return null;
}


?>