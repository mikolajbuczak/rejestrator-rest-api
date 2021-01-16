<?php
     // Connection variable used to communicate with DB
     $conn = new mysqli('localhost', 'root', '', 'rejestrator');

     if($_SERVER['REQUEST_METHOD'] != 'POST'){
        http_response_code(400);
        exit();
     }

    if( isset($_POST['adminID']) && isset($_POST['username']) ) {

        $adminID = $conn->real_escape_string($_POST['adminID']);
        $username = $conn->real_escape_string($_POST['username']);

        $test = $conn->query("SELECT COUNT(*) FROM administrators WHERE administratorID='$adminID' OR username='$username'");
        $testResult = $test->fetch_assoc();
    
        // Check if employee exists
        if ( $testResult['COUNT(*)'] != 0 ) {
            http_response_code(404);
            exit();
        }

        // Success
        http_response_code(200);
        exit();
    }
    else if( isset($_POST['adminID']) ) {
        $adminID = $conn->real_escape_string($_POST['adminID']);

        $test = $conn->query("SELECT COUNT(*) FROM administrators WHERE administratorID='$adminID'");
        $testResult = $test->fetch_assoc();
    
        // Check if employee exists
        if ( $testResult['COUNT(*)'] != 0 ) {
            http_response_code(404);
            exit();
        }

        // Success
        http_response_code(200);
        exit();
    }
    else if( isset($_POST['username']) ) {
        $username = $conn->real_escape_string($_POST['username']);

        $test = $conn->query("SELECT COUNT(*) FROM administrators WHERE username='$username'");
        $testResult = $test->fetch_assoc();
    
        // Check if employee exists
        if ( $testResult['COUNT(*)'] != 0 ) {
            http_response_code(404);
            exit();
        }

        // Success
        http_response_code(200);
        exit();
    }
    else {
        http_response_code(400);
        exit();
    }
?>