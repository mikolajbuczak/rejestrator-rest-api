<?php
    // Connection variable used to communicate with DB
    $conn = new mysqli('localhost', 'root', '', 'rejestrator');

    // POST
    $employeeId = $_POST['employeeId'];
    $employeePin = $_POST['employeePin'];

    $query_check_employee = "select * from employees where employeeID = '$employeeId' and pin = '$employeePin'";

    $result = mysqli_query($conn, $query_check_employee);

    if(mysqli_num_rows($result) == 0)
    {
        $response['success'] = "false";
        $response['message'] = "Nie poprawne dane logowania.";
    }
    else
    {
        $row = mysqli_fetch_assoc($result);

        $response['success'] = "true";
        $response['message'] = "Zalogowano.";

        $response['employeeId'] = $row['employeeID'];
        $response['employeePin'] = $row['pin'];
    }

    exit(json_encode($response));
    mysqli_close($conn);

?>