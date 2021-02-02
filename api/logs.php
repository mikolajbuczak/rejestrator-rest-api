<?php
    // Connection variable used to communicate with DB
    $conn = new mysqli('localhost', 'root', '', 'rejestrator');

    // GET
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        // GET /logs/employeeID/date
        if(isset($_GET['employeeID']) && isset($_GET['date'])){
            $id = $conn->real_escape_string($_GET['employeeID']);
            $date = $conn->real_escape_string($_GET['date']);
            $data = array();
            $sql = $conn->query("SELECT employeeID, date
                                 FROM logs 
                                 WHERE `employeeID`='$id' AND date LIKE '$date%' ORDER BY str_to_date(date, '%d.%m.%y %H:%i') ASC LIMIT 1");
            $data = $sql -> fetch_assoc();
        }
        // GET /logs/employeeID
        else if(isset($_GET['employeeID'])) {
            $id = $conn->real_escape_string($_GET['employeeID']);
            $data = array();
            $sql = $conn->query("SELECT employeeID, date
                                 FROM logs 
                                 WHERE employeeID='$id' ORDER BY str_to_date(date, '%d.%m.%y %H:%i') DESC");
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
    
        http_response_code(200);
        exit(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }
    // POST
    else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['employeeID']) && 
            isset($_POST['date'])) {

            // Check if the url is correct
            if ( isset($_GET['employeeID']) ) {
                http_response_code(404);
                exit();
            }
            
            // Get all arguments
            $employeeID = $conn->real_escape_string($_POST['employeeID']);
            $date = $conn->real_escape_string($_POST['date']);

            // Check if date format is valid
            if( !verifyDate($date) ) {
                http_response_code(404);
                exit();
            }

            $sql = $conn->query("INSERT INTO logs 
                                 (employeeID, date)
                                 VALUES
                                 ('$employeeID', '$date')");

            // Success
            http_response_code(200);
            exit();
        }
        else {
            // Missing arguments
            http_response_code(404);
            exit();
        }
    }
    //DELETE
    else if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
        // Check if the employeeID is set
        if ( !isset($_GET['employeeID']) ) {
            http_response_code(404);
            exit();
        }

        $employeeID = $conn->real_escape_string($_GET['employeeID']);

        $conn->query("DELETE FROM logs WHERE employeeID='$employeeID'");

        // Success
        http_response_code(200);
        exit();
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