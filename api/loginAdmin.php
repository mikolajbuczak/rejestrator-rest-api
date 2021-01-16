<?php
    // Connection variable used to communicate with DB
    $conn = new mysqli('localhost', 'root', '', 'rejestrator');

    // GET
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        // GET /loginAdmin/username/password
        $username = $conn->real_escape_string($_GET['username']);
        $password = $conn->real_escape_string($_GET['password']);

        $data = array();
        $sql = $conn->query("SELECT COUNT(*)
                                FROM administrators 
                                WHERE username='$username'
                                AND password='$password'");
        
        $result = $sql->fetch_assoc();

        // Check if corrent combination on username and password
        if ( $result['COUNT(*)'] == 0 ) {
            exit(json_encode(array('status' => 'failed', 'reason' => 'Invalid username or password'), JSON_PRETTY_PRINT));
        }
        exit(json_encode(array('status' => 'success', 'reason' => ''), JSON_PRETTY_PRINT));
    }
?>