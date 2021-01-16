<?php
     // Connection variable used to communicate with DB
     $conn = new mysqli('localhost', 'root', '', 'rejestrator');

     if($_SERVER['REQUEST_METHOD'] != 'GET'){
        http_response_code(400);
        exit();
     }

    //GET
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        $data = array();
        $sql = $conn->query("SELECT employees.name, employees.surname, logs.date 
                             FROM employees
                             JOIN logs
                             ON logs.employeeID=employees.employeeID
                             ORDER BY logs.date DESC");
        while($d = $sql->fetch_assoc()) {
            $data[] = $d;
        }
        http_response_code(200);
        exit(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }
?>