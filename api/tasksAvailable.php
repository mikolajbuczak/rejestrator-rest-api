<?php
     // Connection variable used to communicate with DB
     $conn = new mysqli('localhost', 'root', '', 'rejestrator');

     // GET
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        // GET /tasksAvailable/employeeID
        if(isset($_GET['employeeID'])) {
            $id = $conn->real_escape_string($_GET['employeeID']);
            $data = array();
            $sql = $conn->query("SELECT *
                                 FROM tasks 
                                 WHERE employeeID='$id'");
            while($d = $sql->fetch_assoc()) {
                $data[] = $d;
            }
        }
        // GET /tasksAvailable
        else {
            $data = array();
            $sql = $conn->query("SELECT *
                                 FROM tasks");
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
            isset($_POST['task'])) {

            // Check if the url is correct
            if ( isset($_GET['employeeID']) ) {
                http_response_code(400);
                $conn->close();
                exit();
            }
            
            // Get all arguments
            $employeeID = $conn->real_escape_string($_POST['employeeID']);
            $task = $conn->real_escape_string($_POST['task']);

            $sql = $conn->query("INSERT INTO tasks 
                                 (employeeID, task)
                                 VALUES
                                 ('$employeeID', '$task')");

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

        $conn->query("DELETE FROM tasks WHERE employeeID='$employeeID'");

        // Success
        http_response_code(200);
        $conn->close();
        exit();
    }
?>