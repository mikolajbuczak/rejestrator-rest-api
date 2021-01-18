<?php
     // Connection variable used to communicate with DB
     $conn = new mysqli('localhost', 'root', '', 'rejestrator');

    //GET
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        // Check if the id is set
        if ( !isset($_GET['employeeID']) || !isset($_GET['date'])) {
            http_response_code(400);
            $conn->close();
            exit();
        }

        $id = $conn->real_escape_string($_GET['employeeID']);
        $date = $conn->real_escape_string($_GET['date']);

        $data = array();
        $sql = $conn->query("SELECT employeeID, date FROM logs WHERE `employeeID`='$id' AND date LIKE '$date%' ORDER BY date ASC");
        while($d = $sql->fetch_assoc()) {
            $data[] = $d;
        }

        // Success
        http_response_code(200);
        $conn->close();
        exit(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }
?>