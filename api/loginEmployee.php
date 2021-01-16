<?php
    // Connection variable used to communicate with DB
    $conn = new mysqli('localhost', 'root', '', 'rejestrator');
    $conn->set_charset("utf-8");

    if ( isset($_SERVER['PHP_AUTH_USER']) &&
         isset($_SERVER['PHP_AUTH_PW']) ) {
            $employeeID = urldecode($_SERVER['PHP_AUTH_USER']);
            $pin = urldecode($_SERVER['PHP_AUTH_PW']);

            $test = $conn->query("SELECT COUNT(*) FROM employees WHERE employeeID='$employeeID' AND pin='$pin'");
            $testResult = $test->fetch_assoc();

            // Check if employee exists
            if ( $testResult['COUNT(*)'] == 0 ) {
                http_response_code(404);
                exit();
            }

            $data = $conn->query("SELECT employeeID, name, surname, shift FROM employees WHERE employeeID='$employeeID' AND pin='$pin'");
            $repsonse = $data->fetch_assoc();

            http_response_code(200);
            exit(json_encode($repsonse, JSON_PRETTY_PRINT));
    }
?>