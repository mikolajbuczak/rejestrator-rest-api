<?php
    // Connection variable used to communicate with DB
    $conn = new mysqli('localhost', 'root', '', 'rejestrator');

    if ($_SERVER['REQUEST_METHOD'] != 'GET'){
        http_response_code(400);
        $conn->close();
        exit();
    }

    if (isset($_GET['employeeID'])) {
        $id = $conn->real_escape_string($_GET['employeeID']);
        $data = array();
        $sql = $conn->query("SELECT employees.employeeID, employees.name, employees.surname, logs.date 
                                FROM employees
                                JOIN logs
                                ON logs.employeeID=employees.employeeID
                                WHERE logs.employeeID='$id'
                                ORDER BY str_to_date(logs.date, '%d.%m.%y %H:%i') DESC");
        while($d = $sql->fetch_assoc()) {
            $data[] = $d;
        }
    }
    else {
        $data = array();
        $sql = $conn->query("SELECT employees.employeeID, employees.name, employees.surname, logs.date 
                                FROM employees
                                JOIN logs
                                ON logs.employeeID=employees.employeeID
                                ORDER BY str_to_date(logs.date, '%d.%m.%y %H:%i') DESC");
        while($d = $sql->fetch_assoc()) {
            $data[] = $d;
        }
    }
    http_response_code(200);
    $conn->close();
    exit(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
?>