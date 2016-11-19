<?php
    if(!isset($_GET['v2']))
        header("Location: /not-found");

    $id = $_GET['v2'];

    if(mysqli_num_rows(mysqli_query($con, "SELECT * FROM users WHERE type != 0 AND id = {$id}")) != 1)
        header("Location: /not-found");

    // DELETE Files
    $tickets = mysqli_query("SELECT * FROM tickets WHERE user_id = {$id}");
    while($ticket = mysqli_fetch_assoc($tickets)){
        $files = mysqli_query($con, "SELECT * FROM files WHERE ticket_id = {$ticket['id']}");
        while($file = mysqli_fetch_assoc($files)){
            unlink('upload/'.$file['name']);
            mysqli_query($con, "DELETE FROM files WHERE id = {$file['id']}");
        }
        mysqli_query($con, "DELETE FROM messages WHERE ticket_id = {$ticket['id']}");
        mysqli_query($con, "DELETE FROM tickets WHERE id = {$ticket['id']} LIMIT 1");
    }

    mysqli_query($con, "DELETE FROM tickets WHERE user_id = {$id} AND type != 0 LIMIT 1");

    header("Locatin: /kullanicilar");
?>