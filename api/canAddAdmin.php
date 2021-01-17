<?php
     // Connection variable used to communicate with DB
     $conn = new mysqli('localhost', 'root', '', 'rejestrator');

     if($_SERVER['REQUEST_METHOD'] != 'POST'){
        http_response_code(400);
        $conn->close();
        exit();
     }

    if( isset($_POST['adminID']) && isset($_POST['username']) ) {

        $adminID = $conn->real_escape_string($_POST['adminID']);
        $username = $conn->real_escape_string($_POST['username']);

        $test = $conn->query("SELECT COUNT(*) FROM administrators WHERE administratorID='$adminID'");
        $testResult = $test->fetch_assoc();

        $test1 = $conn->query("SELECT COUNT(*) FROM administrators WHERE username='$username'");
        $testResult1 = $test1->fetch_assoc();
    
        // Check if admin exists
        if ( $testResult['COUNT(*)'] != 0 && $testResult1['COUNT(*)'] != 0) {
            http_response_code(404);
            $conn->close();
            exit();
        }
        else if ( $testResult['COUNT(*)'] != 0 ) {
            http_response_code(402);
            $conn->close();
            exit();
        }
        else if ( $testResult1['COUNT(*)'] != 0 ) {
            http_response_code(401);
            $conn->close();
            exit();
        }

        // Success
        http_response_code(200);
        $conn->close();
        exit();
    }
    else if( isset($_POST['adminID']) ) {
        $adminID = $conn->real_escape_string($_POST['adminID']);

        $test = $conn->query("SELECT COUNT(*) FROM administrators WHERE administratorID='$adminID'");
        $testResult = $test->fetch_assoc();
    
        // Check if employee exists
        if ( $testResult['COUNT(*)'] != 0 ) {
            http_response_code(404);
            $conn->close();
            exit();
        }

        // Success
        http_response_code(200);
        $conn->close();
        exit();
    }
    else if( isset($_POST['username']) ) {
        $username = $conn->real_escape_string($_POST['username']);

        $test = $conn->query("SELECT COUNT(*) FROM administrators WHERE username='$username'");
        $testResult = $test->fetch_assoc();
    
        // Check if employee exists
        if ( $testResult['COUNT(*)'] != 0 ) {
            http_response_code(404);
            $conn->close();
            exit();
        }

        // Success
        http_response_code(200);
        $conn->close();
        exit();
    }
    else {
        http_response_code(400);
        $conn->close();
        exit();
    }
?>