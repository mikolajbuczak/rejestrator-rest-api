<?php
    // Connection variable used to communicate with DB
    $conn = new mysqli('localhost', 'root', '', 'rejestrator');

    // GET
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        // GET /loginEmployee/employeeID/pin
        $id = $conn->real_escape_string($_GET['employeeID']);
        $pin = $conn->real_escape_string($_GET['pin']);

        // Check if the length of employeeID is 4
        if( strlen($id) != 4) {
            exit(json_encode(array('status' => 'failed', 'reason' => 'Invalid employeeID length'), JSON_PRETTY_PRINT));
        }

        // Check if the length of pin is 4
        if( strlen($pin) != 4) {
            exit(json_encode(array('status' => 'failed', 'reason' => 'Invalid pin length'), JSON_PRETTY_PRINT));
        }

        $data = array();
        $sql = $conn->query("SELECT COUNT(*)
                                FROM employees 
                                WHERE employeeID='$id'
                                AND pin='$pin'");
        
        $result = $sql->fetch_assoc();

        // Check if corrent combination on employeeID and pin
        if ( $result['COUNT(*)'] == 0 ) {
            exit(json_encode(array('status' => 'failed', 'reason' => 'Invalid employeeID or pin'), JSON_PRETTY_PRINT));
        }
        exit(json_encode(array('status' => 'success'), JSON_PRETTY_PRINT));
    }
?>