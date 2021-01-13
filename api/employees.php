<?php
    // Connection variable used to communicate with DB
    $conn = new mysqli('localhost', 'root', '', 'rejestrator');

    // GET
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        // GET /employees/id
        if(isset($_GET['employeeID'])) {
            $id = $conn->real_escape_string($_GET['employeeID']);
            $sql = $conn->query("SELECT employeeID, pin, name, surname, shift 
                                 FROM employees 
                                 WHERE employeeID='$id'");
            $data = $sql->fetch_assoc();
        }
        // GET /employees
        else {
            $data = array();
            $sql = $conn->query("SELECT employeeID, pin, name, surname, shift 
                                 FROM employees");
            while($d = $sql->fetch_assoc()) {
                $data[] = $d;
            }
        }
        exit(json_encode($data, JSON_PRETTY_PRINT));
    }
    // POST
    else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['employeeID']) && 
            isset($_POST['pin']) && 
            isset($_POST['name']) && 
            isset($_POST['surname']) && 
            isset($_POST['shift'])) {
            
            // Get all arguments
            $employeeID = $conn->real_escape_string($_POST['employeeID']);
            $pin = $conn->real_escape_string($_POST['pin']);
            $name = $conn->real_escape_string($_POST['name']);
            $surname = $conn->real_escape_string($_POST['surname']);
            $shift = $conn->real_escape_string($_POST['shift']);

            // Check if the length of employeeID is 4
            if( strlen($employeeID) != 4) {
                exit(json_encode(array('status' => 'failed', 'reason' => 'Invalid employeeID length'), JSON_PRETTY_PRINT));
            }

            // Check if the length of pin is 4
            if( strlen($pin) != 4) {
                exit(json_encode(array('status' => 'failed', 'reason' => 'Invalid pin length'), JSON_PRETTY_PRINT));
            }

            // Check if shift uses enum('dzienny', 'nocny')
            if( strtolower($shift) != 'dzienny' && strtolower($shift) != 'nocny' ) {
                exit(json_encode(array('status' => 'failed', 'reason' => 'Invalid shift'), JSON_PRETTY_PRINT));
            }

            $sqlTest = $conn->query("SELECT COUNT(*) FROM employees where employeeID='$employeeID'");
            $testResult = $sqlTest->fetch_assoc();

            // Check if employee with this employeeID already exists
            if ( $testResult['COUNT(*)'] != 0 ) {
                exit(json_encode(array('status' => 'failed', 'reason' => 'This employeeID is already used'), JSON_PRETTY_PRINT));
            }

            $sql = $conn->query("INSERT INTO employees 
                                (employeeID, pin, name, surname, shift)
                                VALUES
                                ('$employeeID', '$pin', '$name', '$surname', '$shift')");

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
        // Check if the employee to update is selected
        if ( !isset($_GET['employeeID']) ) {
            exit(json_encode(array('status' => 'failed', 'reason' => 'Employee is not selected'), JSON_PRETTY_PRINT));
        }

        $employeeID = $conn->real_escape_string($_GET['employeeID']);

        $checkIfExists = $conn->query("SELECT COUNT(*) FROM employees where employeeID='$employeeID'");
        $result = $checkIfExists->fetch_assoc();

        // Check if employee to update exists
        if ( $result['COUNT(*)'] == 0 ) {
            exit(json_encode(array('status' => 'failed', 'reason' => 'Employee with this employeeId does not exist'), JSON_PRETTY_PRINT));
        }

        // Read arguments
        $data = file_get_contents('php://input');
        
        if ( strpos($data, '=') ==  true ) {
            $allPairs = array();
            $data = explode('&', $data);
            
            foreach($data as $pair) {
                $pair = explode('=', $pair);
                $allPairs[$pair[0]] = $pair[1];
            }
            
            // Check if there is any argument
            if ( empty($allPairs) ) {
                exit(json_encode(array('status' => 'failed', 'reason' => 'No arguments are set'), JSON_PRETTY_PRINT));
            }
            
            $sqlQuery = "UPDATE employees SET ";

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
                
                // Check if new employeeID is already used
                if ( $testResult['COUNT(*)'] != 0 ) {
                    exit(json_encode(array('status' => 'failed', 'reason' => 'This employeeID is already used'), JSON_PRETTY_PRINT));
                }

                array_push($array, "employeeID='$newEmployeeID'");
            }

            // Check if pin is set
            if ( isset($allPairs['pin']) ) {
                $newPin = $allPairs['pin'];

                // Check if the length of new pin is 4
                if( strlen($newPin) != 4) {
                    exit(json_encode(array('status' => 'failed', 'reason' => 'Invalid new pin length'), JSON_PRETTY_PRINT));
                }

                array_push($array, "pin='$newPin'");
            }

            // Check if name is set
            if ( isset($allPairs['name']) ) {
                $newName = $allPairs['name'];
                array_push($array, "name='$newName'");
            }

            // Check if surname is set
            if ( isset($allPairs['surname']) ) {
                $newSurname = $allPairs['surname'];
                array_push($array, "surname='$newSurname'");
            }

            // Check if shift is set
            if ( isset($allPairs['shift']) ) {
                $newShift = $allPairs['shift'];

                // Check if shift uses enum('dzienny', 'nocny')
                if( strtolower($newShift) != 'dzienny' && strtolower($newShift) != 'nocny' ) {
                    exit(json_encode(array('status' => 'failed', 'reason' => 'Invalid shift'), JSON_PRETTY_PRINT));
                }

                array_push($array, "shift='$newShift'");
            }

            $sqlQuery .= implode(', ', $array);

            $sqlQuery .= " WHERE employeeID=$employeeID";

            $sql = $conn->query($sqlQuery);

            // Success
            exit(json_encode(array('status' => 'success'), JSON_PRETTY_PRINT));
        }
    }
    //DELETE
    else if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
        // Check if the employee to delete is selected
        if ( !isset($_GET['employeeID']) ) {
            exit(json_encode(array('status' => 'failed', 'reason' => 'Employee is not selected'), JSON_PRETTY_PRINT));
        }

        $employeeID = $conn->real_escape_string($_GET['employeeID']);

        $checkIfExists = $conn->query("SELECT COUNT(*) FROM employees where employeeID='$employeeID'");
        $result = $checkIfExists->fetch_assoc();

        // Check if employee to delete exists
        if ( $result['COUNT(*)'] == 0 ) {
            exit(json_encode(array('status' => 'failed', 'reason' => 'Employee with this employeeId does not exist'), JSON_PRETTY_PRINT));
        }

        $conn->query("DELETE FROM employees WHERE employeeID='$employeeID'");

        // Success
        exit(json_encode(array('status' => 'success'), JSON_PRETTY_PRINT));
    }
?>