<?php
     // Connection variable used to communicate with DB
     $conn = new mysqli('localhost', 'root', '', 'rejestrator');

    //DELETE
    if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
        // Check if the id is set
        if ( !isset($_GET['id']) ) {
            http_response_code(400);
            $conn->close();
            exit();
        }

        $id = $conn->real_escape_string($_GET['id']);

        $conn->query("DELETE FROM tasksinprogress WHERE id='$id'");

        // Success
        http_response_code(200);
        $conn->close();
        exit();
    }
?>