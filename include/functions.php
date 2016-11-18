<?php

function check_login($con)
{
    if(!isset($_SESSION['user']))
        return false;
    
    $user = $_SESSION['user'];
    
    $query = "SELECT * FROM users WHERE id = {$user['id']} AND name = '{$user['name']}' AND username = '{$user['username']}' AND type = '{$user['type']}'";
    
    $user = mysqli_query($con, $query);
    
    if(mysqli_num_rows($user) != 1)
        return false;
    
    return true;
}

?>