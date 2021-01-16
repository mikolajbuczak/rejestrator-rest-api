<?php
    // Connection variable used to communicate with DB
    $conn = new mysqli('localhost', 'root', '', 'rejestrator');

    if($_SERVER['REQUEST_METHOD'] != 'POST') {
        http_response_code(400);
        exit();
    }

    $username = $_POST['username'];
    $password = $_POST['password'];

    $test = $conn->query("SELECT COUNT(*) FROM administrators WHERE username='$username' AND password='$password'");
    $testResult = $test->fetch_assoc();

    // Check if employee exists
    if ( $testResult['COUNT(*)'] == 0 ) {
        http_response_code(404);
        $conn->close();
        exit();
    }

    $data = $conn->query("SELECT administratorID, name, surname FROM administrators WHERE username='$username' AND password='$password'");
    $response = $data->fetch_assoc();

    http_response_code(200);
    $conn->close();
    exit(json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
?>