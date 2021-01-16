<?php
    // Connection variable used to communicate with DB
    $conn = new mysqli('localhost', 'root', '', 'rejestrator');

    // GET
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        // GET /logs/employeeID
        if(isset($_GET['employeeID'])) {
            $id = $conn->real_escape_string($_GET['employeeID']);
            $data = array();
            $sql = $conn->query("SELECT employeeID, date
                                 FROM logs 
                                 WHERE employeeID='$id' ORDER BY date DESC");
            while($d = $sql->fetch_assoc()) {
                $data[] = $d;
            }
        }
        // GET /logs
        else {
            $data = array();
            $sql = $conn->query("SELECT employeeID, date 
                                 FROM logs");
            while($d = $sql->fetch_assoc()) {
                $data[] = $d;
            }
        }
        exit(json_encode($data, JSON_PRETTY_PRINT));
    }
    // POST
    else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['employeeID']) && 
            isset($_POST['date'])) {

            // Check if the url is correct
            if ( isset($_GET['employeeID']) ) {
                exit(json_encode(array('status' => 'failed', 'reason' => 'You cannot create log using this url. Use /logs/ instead'), JSON_PRETTY_PRINT));
            }
            
            // Get all arguments
            $employeeID = $conn->real_escape_string($_POST['employeeID']);
            $date = $conn->real_escape_string($_POST['date']);

            // Check if the length of employeeID is 4
            if( strlen($employeeID) != 4) {
                exit(json_encode(array('status' => 'failed', 'reason' => 'Invalid employeeID length'), JSON_PRETTY_PRINT));
            }

            $sqlTest = $conn->query("SELECT COUNT(*) FROM employees where employeeID='$employeeID'");
            $testResult = $sqlTest->fetch_assoc();

            // Check if employee with selected employeeID exists
            if ( $testResult['COUNT(*)'] == 0 ) {
                exit(json_encode(array('status' => 'failed', 'reason' => 'Employee with this employeeId does not exist'), JSON_PRETTY_PRINT));
            }

            // Check if date format is valid
            if( !verifyDate($date) ) {
                exit(json_encode(array('status' => 'failed', 'reason' => 'Invalid date format'), JSON_PRETTY_PRINT));
            }

            $sql = $conn->query("INSERT INTO logs 
                                 (employeeID, date)
                                 VALUES
                                 ('$employeeID', '$date')");

            // Success
            exit(json_encode(array('status' => 'success')));
        }
        else {
            // Missing arguments
            exit(json_encode(array('status' => 'failed', 'reason' => 'Required arguments are missing'), JSON_PRETTY_PRINT));
        }
    }
    //PUT
    else if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        // Check if the employeeID is set
        if ( !isset($_GET['employeeID']) ) {
            exit(json_encode(array('status' => 'failed', 'reason' => 'Employee is not selected'), JSON_PRETTY_PRINT));
        }

        $employeeID = $conn->real_escape_string($_GET['employeeID']);

        // Check if the length of new employeeID is 4
        if( strlen($employeeID) != 4) {
            exit(json_encode(array('status' => 'failed', 'reason' => 'Invalid new employeeID length'), JSON_PRETTY_PRINT));
        }

        $checkIfExists = $conn->query("SELECT COUNT(*) FROM logs where employeeID='$employeeID'");
        $result = $checkIfExists->fetch_assoc();

        // Check if logs with selected employeeID exist
        if ( $result['COUNT(*)'] == 0 ) {
            exit(json_encode(array('status' => 'failed', 'reason' => 'Logs with this employeeId do not exist'), JSON_PRETTY_PRINT));
        }

        // Read arguments
        $data = urldecode(file_get_contents('php://input'));

        // Check if any argument is set
        if ( !strpos($data, '=') ) {
            exit(json_encode(array('status' => 'failed', 'reason' => 'No arguments are set'), JSON_PRETTY_PRINT));
        }

        $allPairs = array();
        $data = explode('&', $data);
        
        foreach($data as $pair) {
            $pair = explode('=', $pair);
            $allPairs[$pair[0]] = $pair[1];
        }
        
        $sqlQuery = "UPDATE logs SET ";

        $array = array();

        // Check if employeeID is set
        if ( isset($allPairs['employeeID']) ) {
            $newEmployeeID = $allPairs['employeeID'];
            
            // Check if the length of new employeeID is 4
            if( strlen($newEmployeeID) != 4) {
                exit(json_encode(array('status' => 'failed', 'reason' => 'Invalid new employeeID length'), JSON_PRETTY_PRINT));
            }

            $sqlTest = $conn->query("SELECT COUNT(*) FROM employees where employeeID='$newEmployeeID'");
            $testResult = $sqlTest->fetch_assoc();
            
            // Check if employee with new employeeID exists
            if ( $testResult['COUNT(*)'] == 0 ) {
                exit(json_encode(array('status' => 'failed', 'reason' => 'Employee with new employeeID does not exist'), JSON_PRETTY_PRINT));
            }

            array_push($array, "employeeID='$newEmployeeID'");
        }

        // Check if date is set
        if ( isset($allPairs['date']) ) {
            $newDate= $allPairs['date'];

            /// Check if new date format is valid
            if( !verifyDate($newDate) ) {
                exit(json_encode(array('status' => 'failed', 'reason' => 'Invalid new date format'), JSON_PRETTY_PRINT));
            }

            array_push($array, "date='$newDate'");
        }

        $sqlQuery .= implode(', ', $array);

        $sqlQuery .= " WHERE employeeID=$employeeID";

        $sql = $conn->query($sqlQuery);

        // Success
        exit(json_encode(array('status' => 'success'), JSON_PRETTY_PRINT));
    }
    //DELETE
    else if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
        // Check if the employeeID is set
        if ( !isset($_GET['employeeID']) ) {
            exit(json_encode(array('status' => 'failed', 'reason' => 'Employee is not selected'), JSON_PRETTY_PRINT));
        }

        $employeeID = $conn->real_escape_string($_GET['employeeID']);

        // Check if the length of new employeeID is 4
        if( strlen($employeeID) != 4) {
            exit(json_encode(array('status' => 'failed', 'reason' => 'Invalid new employeeID length'), JSON_PRETTY_PRINT));
        }

        $checkIfExists = $conn->query("SELECT COUNT(*) FROM logs where employeeID='$employeeID'");
        $result = $checkIfExists->fetch_assoc();

        // Check if logs with selected employeeID exist
        if ( $result['COUNT(*)'] == 0 ) {
            exit(json_encode(array('status' => 'failed', 'reason' => 'Logs with this employeeId do not exist'), JSON_PRETTY_PRINT));
        }

        $conn->query("DELETE FROM logs WHERE employeeID='$employeeID'");

        // Success
        exit(json_encode(array('status' => 'success'), JSON_PRETTY_PRINT));
    }

    function verifyDate($date, $strict = true)
    {
        $dateTime = DateTime::createFromFormat('d.m.Y H:i', $date);
        if ($strict) {
            $errors = DateTime::getLastErrors();
            if (!empty($errors['warning_count'])) {
                return false;
            }
        }
        return $dateTime !== false;
    }
?>