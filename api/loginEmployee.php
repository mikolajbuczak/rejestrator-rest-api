<?php
    require_once('functions.php');
    // Connection variable used to communicate with DB
    $conn = new mysqli('localhost', 'root', '', 'rejestrator');

    if($_SERVER['REQUEST_METHOD'] != 'GET') {
        http_response_code(400);
        $conn->close();
        exit();
    }

    $authString = getallheaders()['Authorization'];

    if( $authString == "") 
    {
        http_response_code(404);
        $conn->close();
        exit();
    }

    if( !auth($authString)) {
        http_response_code(404);
        $conn->close();
        exit();
    }

    $cred = getCredentials($authString);
    $employeeID = $cred[0];
    $pin = $cred[1];

    $sql = $conn->query("SELECT employeeID, pin, name, surname, shift FROM employees WHERE employeeID='$employeeID' AND pin='$pin'");
    $response = $sql->fetch_assoc();

    http_response_code(200);
    $conn->close();
    exit(json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
?>