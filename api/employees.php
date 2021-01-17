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
            $sql = $conn->query("SELECT employeeID, name, surname
                                 FROM employees");
            while($d = $sql->fetch_assoc()) {
                $data[] = $d;
            }
        }
        http_response_code(200);
        $conn->close();
        exit(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }
    // POST
    else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['employeeID']) && 
            isset($_POST['pin']) && 
            isset($_POST['name']) && 
            isset($_POST['surname']) && 
            isset($_POST['shift'])) {

            if( isset($_GET['employeeID']) ) {
                http_response_code(400);
                $conn->close();
                exit();
            }
            
            // Get all arguments
            $employeeID = $conn->real_escape_string($_POST['employeeID']);
            $pin = $conn->real_escape_string($_POST['pin']);
            $name = $conn->real_escape_string($_POST['name']);
            $surname = $conn->real_escape_string($_POST['surname']);
            $shift = $conn->real_escape_string($_POST['shift']);

            // Check if the length of employeeID is 4
            if( strlen($employeeID) != 4) {
                http_response_code(404);
                $conn->close();
                exit();
            }

            // Check if the length of pin is 4
            if( strlen($pin) != 4) {
                http_response_code(404);
                $conn->close();
                exit();
            }

            // Check if shift uses enum('dzienny', 'nocny')
            if( strtolower($shift) != 'dzienny' && strtolower($shift) != 'nocny' ) {
                http_response_code(404);
                $conn->close();
                exit();
            }

            $sqlTest = $conn->query("SELECT COUNT(*) FROM employees where employeeID='$employeeID'");
            $testResult = $sqlTest->fetch_assoc();

            // Check if employee with this employeeID already exists
            if ( $testResult['COUNT(*)'] != 0 ) {
                http_response_code(404);
                $conn->close();
                exit();
            }

            $sql = $conn->query("INSERT INTO employees 
                                (employeeID, pin, name, surname, shift)
                                VALUES
                                ('$employeeID', '$pin', '$name', '$surname', '$shift')");

            // Success
            http_response_code(200);
            $conn->close();
            exit();
        }
        else {
            // Missing arguments
            http_response_code(404);
            $conn->close();
            exit();
        }
    }
    //PUT
    else if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        // Check if the employee to update is selected
        if ( !isset($_GET['employeeID']) ) {
            http_response_code(400);
            $conn->close();
            exit();
        }

        $employeeID = $conn->real_escape_string($_GET['employeeID']);

        // Check if the length of employeeID is 4
        if( strlen($employeeID) != 4) {
            http_response_code(404);
            $conn->close();
            exit();
        }

        $checkIfExists = $conn->query("SELECT COUNT(*) FROM employees where employeeID='$employeeID'");
        $result = $checkIfExists->fetch_assoc();

        // Check if employee to update exists
        if ( $result['COUNT(*)'] == 0 ) {
            http_response_code(404);
            $conn->close();
            exit();
        }

        // Read arguments
        $data = urldecode(file_get_contents('php://input'));
        
        // Check if any argument is set
        if ( !strpos($data, '=') ) {
            http_response_code(404);
            $conn->close();
            exit();
        }

        $allPairs = array();
        $data = explode('&', $data);
        
        foreach($data as $pair) {
            $pair = explode('=', $pair);
            $allPairs[$pair[0]] = $pair[1];
        }
        
        
        $sqlQuery = "UPDATE employees SET ";

        $array = array();

        // Check if employeeID is set
        if ( isset($allPairs['employeeID']) ) {
            $newEmployeeID = $allPairs['employeeID'];
            
            // Check if the length of new employeeID is 4
            if( strlen($newEmployeeID) != 4) {
                http_response_code(404);
                $conn->close();
                exit();
            }

            $sqlTest = $conn->query("SELECT COUNT(*) FROM employees where employeeID='$newEmployeeID'");
            $testResult = $sqlTest->fetch_assoc();
            
            // Check if new employeeID is already used
            if ( $testResult['COUNT(*)'] != 0 ) {
                http_response_code(404);
                $conn->close();
                exit();
            }

            array_push($array, "employeeID='$newEmployeeID'");
        }

        // Check if pin is set
        if ( isset($allPairs['pin']) ) {
            $newPin = $allPairs['pin'];

            // Check if the length of new pin is 4
            if( strlen($newPin) != 4) {
                http_response_code(404);
                $conn->close();
                exit();
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
                http_response_code(404);
                $conn->close();
                exit();
            }

            array_push($array, "shift='$newShift'");
        }

        $sqlQuery .= implode(', ', $array);

        $sqlQuery .= " WHERE employeeID=$employeeID";

        $sql = $conn->query($sqlQuery);

        // Success
        http_response_code(200);
        $conn->close();
        exit();
    }
    //DELETE
    else if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
        // Check if the employee to delete is selected
        if ( !isset($_GET['employeeID']) ) {
            http_response_code(400);
            $conn->close();
            exit();
        }

        $employeeID = $conn->real_escape_string($_GET['employeeID']);

        $conn->query("DELETE FROM employees WHERE employeeID='$employeeID'");
        $conn->query("DELETE FROM tasks WHERE employeeID='$employeeID'");
        $conn->query("DELETE FROM tasksinprogress WHERE employeeID='$employeeID'");
        $conn->query("DELETE FROM tasksdone WHERE employeeID='$employeeID'");
        $conn->query("DELETE FROM logs WHERE employeeID='$employeeID'");

        // Success
        http_response_code(200);
        $conn->close();
        exit();
    }
?>