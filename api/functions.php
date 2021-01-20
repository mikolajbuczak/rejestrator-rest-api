<?php

function auth($authString) 
{
    $data = explode(' ', $authString);
    
    if($data[0] != "Basic")
        return false;
    $cred = base64_decode($data[1]);
    $cred = explode(':', $cred);

    $username = $cred[0];
    $password = $cred[1];

    $conn = new mysqli('localhost', 'root', '', 'rejestrator');
    $sql = $conn->query("SELECT COUNT(*) FROM employees WHERE employeeID='$username' AND pin='$password'");
    $result = $sql->fetch_assoc();

    if($result['COUNT(*)'] != 0)
        return true;
    
    $sql2 = $conn->query("SELECT COUNT(*) FROM administrators WHERE username='$username' AND password='$password'");
    $result2 = $sql2->fetch_assoc();

    if($result2['COUNT(*)'] != 0)
        return true;

    return false;
}

function getCredentials($authString) {
    $data = explode(' ', $authString);
    
    if($data[0] != "Basic")
        return false;
    $cred = base64_decode($data[1]);
    $cred = explode(':', $cred);

    return $cred;
}
 
?>