<?php
     // Connection variable used to communicate with DB
     $conn = new mysqli('localhost', 'root', '', 'rejestrator');

     // GET
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        // GET /tasksAvailable/employeeID
        if(isset($_GET['employeeID'])) {
            $id = $conn->real_escape_string($_GET['employeeID']);
            $data = array();
            $sql = $conn->query("SELECT employeeID, task
                                 FROM tasks 
                                 WHERE employeeID='$id'");
            while($d = $sql->fetch_assoc()) {
                $data[] = $d;
            }
        }
        // GET /tasksAvailable
        else {
            $data = array();
            $sql = $conn->query("SELECT employeeID, task 
                                 FROM tasks");
            while($d = $sql->fetch_assoc()) {
                $data[] = $d;
            }
        }
        exit(json_encode($data, JSON_PRETTY_PRINT));
    }
    // POST
    else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['employeeID']) && 
            isset($_POST['task'])) {

            // Check if the url is correct
            if ( isset($_GET['employeeID']) ) {
                exit(json_encode(array('status' => 'failed', 'reason' => 'You cannot create log using this url. Use /tasksAvailable/ instead'), JSON_PRETTY_PRINT));
            }
            
            // Get all arguments
            $employeeID = $conn->real_escape_string($_POST['employeeID']);
            $task = $conn->real_escape_string($_POST['task']);

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

            $sql = $conn->query("INSERT INTO tasks 
                                 (employeeID, task)
                                 VALUES
                                 ('$employeeID', '$task')");

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

        // Check if the length of employeeID is 4
        if( strlen($employeeID) != 4) {
            exit(json_encode(array('status' => 'failed', 'reason' => 'Invalid employeeID length'), JSON_PRETTY_PRINT));
        }

        $checkIfExists = $conn->query("SELECT COUNT(*) FROM tasks where employeeID='$employeeID'");
        $result = $checkIfExists->fetch_assoc();

        // Check if logs with selected employeeID exist
        if ( $result['COUNT(*)'] == 0 ) {
            exit(json_encode(array('status' => 'failed', 'reason' => 'Tasks with this employeeId do not exist'), JSON_PRETTY_PRINT));
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
        
        $sqlQuery = "UPDATE tasks SET ";

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

        // Check if task is set
        if ( isset($allPairs['task']) ) {
            $newTask= $allPairs['task'];

            array_push($array, "task='$newTask'");
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

        $checkIfExists = $conn->query("SELECT COUNT(*) FROM tasks where employeeID='$employeeID'");
        $result = $checkIfExists->fetch_assoc();

        // Check if tasks with selected employeeID exist
        if ( $result['COUNT(*)'] == 0 ) {
            exit(json_encode(array('status' => 'failed', 'reason' => 'Tasks with this employeeId do not exist'), JSON_PRETTY_PRINT));
        }

        $conn->query("DELETE FROM tasks WHERE employeeID='$employeeID'");

        // Success
        exit(json_encode(array('status' => 'success'), JSON_PRETTY_PRINT));
    }
?>