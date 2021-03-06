<?php


function creat_verification_code_query() {
    global $query;
    $query = "
            UPDATE
                `User`
            SET
                `verification_code` = FLOOR(RAND() * 1000000)
            WHERE
                username = :username
    ";
    return $query;
}
function verified_code_query() {
    global $query;
    $query = "
            UPDATE
                `User`
            SET
                `verified` = 1
            WHERE
                `username` = :username AND verification_code = :verification_code
                ;
    ";
    return $query;
}
function select_verified_code_query() {
    global $query;
    $query = "
            SELECT
                `verified`
            FROM
                `User`
            WHERE
                `verified` = 1 AND `username` = :username
                ;
            
    ";
    return $query;
}
function set_verified_parent_query() {
    global $query;
    $query = "
            UPDATE
                `Parents`
            SET
                `verified_by_m` = \"v\"
                WHERE
                `id_user` = :id_user
    ";
    return $query;
}
function set_unverified_parent_query() {
    global $query;
    $query = "
            UPDATE
                `Parents`
            SET
                `verified_by_m` = \"u\"
                WHERE
                `id_user` = :id_user
    ";
    return $query;
}

function parent_login_query() {
    global $query;
    $query = "
            SELECT
                *
            FROM
                (
                SELECT
                    `id_user`,
                    `id_school`,
                    `role`,
                    `username`,
                    `password`,
                    `firstname`,
                    `lastname`,
                    `gender`,
                    `phone_no`,
                    `verified`
                FROM
                    `User`
                WHERE
                    `username` = :username AND `password` = :password
            ) AS myuser
            JOIN(
                SELECT
                    `id_user`,
                    `child_name`,
                    `st_no_of_child`,
                    `verified_by_m`
                FROM
                    `Parents`
            ) AS myparent
            ON
                myparent.id_user = myuser.id_user
    ";
    return $query;
}
function manager_login_query() {
    global $query;
    $query = "
           SELECT
                *
            FROM
                (
                    SELECT
                            `id_user`,
                            `id_school`,
                            `role`,
                            `username`,
                            `password`,
                            `firstname`,
                            `lastname`,
                            `gender`,
                            `phone_no`,
                            `verified`
                        FROM
                            `User`
                        WHERE
                            `username` = :username AND `password` = :password
                    ) AS myuser
                    JOIN(
                        SELECT
                            `id_user`,
                            `degree`,
                            `course`
                        FROM
                            `Manager`
                    ) AS mymanager
                    ON
                        mymanager.id_user = myuser.id_user
    ";
    return $query;
}
function community_login_query() {
    global $query;
    $query = "
        SELECT
            *
        FROM
            (
            SELECT
                `id_user`,
                `role`,
                `username`,
                `password`,
                `firstname`,
                `lastname`,
                `gender`,
                `phone_no`,
                `verified`,
                `id_school`
            FROM
                `User`
            WHERE
                `username` = :username AND `password` = :password
        ) AS myuser
        JOIN(
            SELECT
                `id_user`,
                `post`,
                `degree`,
                `course`,
                `tel_work`,
                `address_work`
            FROM
                `Community`
        ) AS mycommunity
        ON
            mycommunity.id_user = myuser.id_user
    ";
    return $query;
}
function get_community_query() {
    global $query;
    $query = "
            SELECT
                *
            FROM
                (
                SELECT
                    `id_user`,
                    `firstname`,
                    `lastname`,
                    `gender`,
                    `username`
                FROM
                    `User`
                WHERE
                    `role` = \"c\" AND `id_school` = :id_school
            ) AS myuser
            JOIN(
                SELECT
                    `id_user`,
                    `post`
                FROM
                    `Community`
            ) AS mycommunity
            ON
                mycommunity.id_user = myuser.id_user
            
    ";
    return $query;
}

function edit_community_query() {
    global $query;
    $query = "
            START TRANSACTION
                ;
            UPDATE
                `User`
            SET
                `firstname` = :firstname,
                `lastname` = :lastname,
                `gender` = :gender,
                `phone_no` = :phone_no
            WHERE
                `id_user` = :id_user
                ;
            UPDATE
                `Community`
            SET
                `post` = :post,
                `degree` = :degree,
                `course` = :course,
                `address_work` = :address_work,
                `tel_work` = :tel_work
            WHERE
                `id_user` = :id_user
                ;
            COMMIT
                ;
    ";
    return $query;
}
function get_community_data_query() {
    global $query;
    $query = "
            SELECT
                *
            FROM
                (
                SELECT
                    `id_user`,
                    `firstname`,
                    `lastname`,
                    `gender`,
                    `phone_no`
                FROM
                    `User`
                WHERE
                    `role` = \"c\" AND `id_school` = 1
            ) AS myuser
            JOIN(
                SELECT
                    `id_user`,
                    `post`,
                    `degree`,
                    `course`,
                    `address_work`,
                    `tel_work`
                FROM
                    `Community`
            ) AS mycommunity
            ON
                mycommunity.id_user = myuser.id_user
            
    ";
    return $query;
}
function get_msg_query() {
    global $query;
    $query = "
            SELECT
                `id_msg`,
                `id_user`,
                `username`,
                `id_conversation`,
                `msg`,
                `date_time_msg`
            FROM
                (
                SELECT
                    *
                FROM
                    Message
                WHERE
                    `id_conversation` IN(
                    SELECT
                        id_conversation
                    FROM
                        Conversation
                    WHERE
                        `id_conversation` = :id_conversation
                )
            ) AS tempgetmsg
            JOIN(
                SELECT
                    USER.id_user AS myuser,
                    USER.username AS username
                FROM
                    `User`
            ) AS tempconv
            ON
                myuser = tempgetmsg.id_user;
    ";
    return $query;
}

function parent_registration_query() {
    global $query;
    $query = "
        START TRANSACTION
            ;
        INSERT INTO `User`(
            `username`,
            `password`,
            `firstname`,
            `lastname`,
            `gender`,
            `phone_no`,
            `id_school`
        )
        VALUES(
            :username,
            :password,
            :firstname,
            :lastname,
            :gender,
            :phone_no,
            :id_school
        );
        INSERT INTO `Parents`(
            `id_user`,
            `child_name`,
            `st_no_of_child`
        )
        VALUES(
            LAST_INSERT_ID(),
             :child_name,
             :st_no_of_child);
        COMMIT
            ;
                ";
    return $query;
}
function add_community_query() {
    global $query;
    $query = "
        START TRANSACTION
            ;
        INSERT INTO `User`(
            `username`,
            `password`,
            `firstname`,
            `lastname`,
            `gender`,
            `phone_no`,
            `id_school`,
            `role`,
            `verified`
        )
        VALUES(
            :username,
            :password,
            :firstname,
            :lastname,
            :gender,
            :phone_no,
            :id_school,
            :role,
            :verified
        );
        INSERT INTO `Community`(
            `id_user`,
            `post`,
            `degree`,
            `course`,
            `tel_work`,
            `address_work`
        )
        VALUES(
            LAST_INSERT_ID(),
            :post,
            :degree,
            :course,
            :tel_work,
            :address_work);
        COMMIT
            ;
                ";
    return $query;
}

function user_exist_query() {
    global $query;
    $query = "
              SELECT
                    `username`
                FROM
                    `User`
                WHERE
                    `username` = :username;
               ";
    return $query;
}

function inbox_query() {
    global $query;
    $query = "
    SELECT
        *
    FROM
        (
        SELECT
            id_two,
            username_two,
            `id_conversation`,
            `topic`,
            `date_time_conv`,
            `category`
        FROM
            (
            SELECT
                USER.id_user AS id_two,
                USER.username AS username_two
            FROM
                `User`
        ) AS tempuser
    JOIN(
        SELECT
            Conversation.id_conversation,
            Conversation.topic,
            Conversation.date_time_conv,
            Conversation.id_user_two,
            Conversation.category
        FROM
            `Conversation`
        WHERE
            `id_user_one` IN(
            SELECT
                `id_user`
            FROM
                `User`
            WHERE
                username = :username
        )
    ) AS tempconv
    ON
        tempuser.id_two = tempconv.id_user_two
    ) AS inbox_one
    UNION ALL
SELECT
        *
    FROM
        (
        SELECT
            id_one,
            username_one,
            `id_conversation`,
            `topic`,
            `date_time_conv`,
            `category`
        FROM
            (
            SELECT
                USER.id_user AS id_one,
                USER.username AS username_one
            FROM
                `User`
        ) AS tempuser
    JOIN(
        SELECT
            Conversation.id_conversation,
            Conversation.topic,
            Conversation.date_time_conv,
            Conversation.id_user_one,
            Conversation.category
        FROM
            `Conversation`
        WHERE
            `id_user_two` IN(
            SELECT
                `id_user`
            FROM
                `User`
            WHERE
                username = :username
        )
    ) AS tempconv
    ON
        tempuser.id_one = tempconv.id_user_one
    ) AS inbox_two
    ORDER BY
        `date_time_conv`
    DESC
    

    ";
    return $query;
}
function new_conv_query() {
    global $query;
    $query = "
            START TRANSACTION
                ;
            INSERT INTO `Conversation`(
                `id_user_one`,
                `id_user_two`,
                `category`,
                `accessibility`,
                `topic`
            )
            VALUES(
                :id_user_one,
                (
                SELECT
                    USER.id_user
                FROM
                    `User`
                WHERE
                    `username` = :username_two
            ),
            :category,
            :accessibility,
            :topic
            );
           INSERT INTO `Message`(`id_user`, `id_conversation`, `msg`)
            VALUES(
                :id_user_one,
                LAST_INSERT_ID(), :msg);
            COMMIT
                ;
          
    ";
    return $query;
}
function notverified_parent_query() {
    global $query;
    $query = "
            SELECT
                `id_user`,
                    `id_school`,
                    `role`,
                    `username`,
                    `password`,
                    `firstname`,
                    `lastname`,
                    `gender`,
                    `phone_no`,
                    `verified`,
                    `child_name`,
                    `st_no_of_child`,
                    `verified_by_m`
            FROM
                (
                SELECT
                    `id_user`,
                    `id_school`,
                    `role`,
                    `username`,
                    `password`,
                    `firstname`,
                    `lastname`,
                    `gender`,
                    `phone_no`,
                    `verified`
                FROM
                    `User`
                WHERE
                    `id_school` = :id_school AND `role` = \"p\"
            ) AS myuser
            JOIN(
                SELECT
                    `id_user` AS id,
                    `child_name`,
                    `st_no_of_child`,
                    `verified_by_m`
                FROM
                    `Parents`
                WHERE
                `verified_by_m` = \"n\"
            ) AS myparent
            ON
                myparent.id = myuser.id_user
          
    ";
    return $query;
}


function get_city_query() {
    global $query;
    $query = "
            SELECT DISTINCT
                `city`
            FROM
                `School`
            WHERE
                `state` = :state AND `grade` = :grade 
    ";
    return $query;
}


function get_school_query() {
    global $query;
    $query = "
            SELECT
                `name`,
                `id_school`
            FROM
                `School`
            WHERE
                `state` = :state AND `grade` = :grade AND `city` = :city AND `zone` = :myzone
    ";
    return $query;
}

function add_msg_query() {
    global $query;
    $query = "
            INSERT INTO `Message`(`id_user`, `id_conversation`, `msg`)
            VALUES(
                :id_user,
                :id_conversation,
                :msg
            );
    ";
    return $query;
}




