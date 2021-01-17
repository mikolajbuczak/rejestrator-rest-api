<?php
     // Connection variable used to communicate with DB
     $conn = new mysqli('localhost', 'root', '', 'rejestrator');

     // GET
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        // GET /tasksDone/employeeID/endDate
        if(isset($_GET['employeeID']) && isset($_GET['endDate'])) {
            $id = $conn->real_escape_string($_GET['employeeID']);
            $date = $conn->real_escape_string($_GET['endDate']);
            $data = array();
            $sql = $conn->query("SELECT *
                                 FROM tasksdone 
                                 WHERE employeeID='$id'
                                 AND enddate LIKE '$date%' ORDER BY enddate ASC, id ASC");
            while($d = $sql->fetch_assoc()) {
                $data[] = $d;
            }
        }
        // GET /tasksDone/employeeID
        else if(isset($_GET['employeeID'])) {
            $id = $conn->real_escape_string($_GET['employeeID']);
            $data = array();
            $sql = $conn->query("SELECT *
                                 FROM tasksdone 
                                 WHERE employeeID='$id'
                                 ORDER BY enddate ASC, id ASC");
            while($d = $sql->fetch_assoc()) {
                $data[] = $d;
            }
        }
        // GET /done
        else {
            $data = array();
            $sql = $conn->query("SELECT *
                                 FROM tasksdone
                                 ORDER BY enddate DESC, id DESC");
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
            isset($_POST['task']) && 
            isset($_POST['startdate']) && 
            isset($_POST['enddate']) && 
            isset($_POST['time'])) {

            // Check if the url is correct
            if ( isset($_GET['employeeID']) || isset($_GET['endDate']) ) {
                http_response_code(400);
                $conn->close();
                exit();
            }
            
            // Get all arguments
            $employeeID = $conn->real_escape_string($_POST['employeeID']);
            $task = $conn->real_escape_string($_POST['task']);
            $startDate = $conn->real_escape_string($_POST['startdate']);
            $endDate = $conn->real_escape_string($_POST['enddate']);
            $time = $conn->real_escape_string($_POST['time']);

            if( !verifyDate($startDate) || !verifyDate($endDate)) {
                http_response_code(404);
                $conn->close();
                exit();
            }

            $sql = $conn->query("INSERT INTO tasksdone 
                                 (employeeID, task, startdate, enddate, time)
                                 VALUES
                                 ('$employeeID', '$task', '$startDate', '$endDate', '$time')");

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
    //DELETE
    else if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
        // Check if the employeeID is set
        if ( !isset($_GET['employeeID']) ) {
            http_response_code(400);
            $conn->close();
            exit();
        }

        $employeeID = $conn->real_escape_string($_GET['employeeID']);

        $conn->query("DELETE FROM tasksdone WHERE employeeID='$employeeID'");

        // Success
        http_response_code(200);
        $conn->close();
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