<?php
    // Connection variable used to communicate with DB
    $conn = new mysqli('localhost', 'root', '', 'rejestrator');

    // GET
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        // GET /administrators/id
        if(isset($_GET['administratorID'])) {
            $id = $conn->real_escape_string($_GET['administratorID']);
            $sql = $conn->query("SELECT administratorID, username, password, name, surname 
                                 FROM administrators 
                                 WHERE administratorID='$id'");
            $data = $sql->fetch_assoc();
        }
        // GET /administrators
        else {
            $data = array();
            $sql = $conn->query("SELECT administratorID, username, password, name, surname  
                                 FROM administrators");
            while($d = $sql->fetch_assoc()) {
                $data[] = $d;
            }
        }
        http_response_code(200);
        $conn->close();
        exit(json_encode($data, JSON_PRETTY_PRINT));
    }
    // POST
    else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['administratorID']) && 
        isset($_POST['username']) && 
        isset($_POST['password']) && 
        isset($_POST['name']) && 
        isset($_POST['surname'])) {

            // Check if the url is correct
            if ( isset($_GET['administratorID']) ) {
                http_response_code(400);
                $conn->close();
                exit();
            }
        
            // Get all arguments
            $administratorID = $conn->real_escape_string($_POST['administratorID']);
            $username = $conn->real_escape_string($_POST['username']);
            $password = $conn->real_escape_string($_POST['password']);
            $name = $conn->real_escape_string($_POST['name']);
            $surname = $conn->real_escape_string($_POST['surname']);

            // Check if the length of administratorID is 4
            if( strlen($administratorID) != 4) {
                http_response_code(404);
                $conn->close();
                exit();
            }

            // Check if the length of username is > 0
            if( strlen($username) <= 0) {
                http_response_code(404);
                $conn->close();
                exit();
            }

            $sqlTest1 = $conn->query("SELECT COUNT(*) FROM administrators where username='$username'");
            $testResult1 = $sqlTest1->fetch_assoc();

            // Check if administrator with selected username already exists
            if ( $testResult1['COUNT(*)'] != 0 ) {
                http_response_code(404);
                $conn->close();
                exit();
            }

            // Check if the length of password is > 0
            if( strlen($password) <= 0) {
                http_response_code(404);
                $conn->close();
                exit();
            }

            $sqlTest2 = $conn->query("SELECT COUNT(*) FROM administrators where administratorID='$administratorID'");
            $testResult2 = $sqlTest2->fetch_assoc();

            // Check if administrator with this administratorID already exists
            if ( $testResult2['COUNT(*)'] != 0 ) {
                http_response_code(404);
                $conn->close();
                exit();
            }

            $sql = $conn->query("INSERT INTO administrators 
                                (administratorID, username, password, name, surname)
                                VALUES
                                ('$administratorID', '$username', '$password', '$name', '$surname')");

            // Success
            http_response_code(2000);
            $conn->close();
            exit();
        }
        else {
            // Missing arguments
            http_response_code(400);
            $conn->close();
            exit();
        }
    }
    //PUT
    else if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        // Check if the administrator to update is selected
        if ( !isset($_GET['administratorID']) ) {
            http_response_code(400);
            $conn->close();
            exit();
        }

        $administratorID = $conn->real_escape_string($_GET['administratorID']);

        // Check if the length of administratorID is 4
        if( strlen($administratorID) != 4) {
            http_response_code(404);
            $conn->close();
            exit();
        }

        $checkIfExists = $conn->query("SELECT COUNT(*) FROM administrators where administratorID='$administratorID'");
        $result = $checkIfExists->fetch_assoc();

        // Check if administrator to update exists
        if ( $result['COUNT(*)'] == 0 ) {
            http_response_code(404);
            $conn->close();
            exit();
        }

        // Read arguments
        $data = urldecode(file_get_contents('php://input'));

        // Check if any argument is set
        if ( !strpos($data, '=') ) {
            http_response_code(404);
            $conn->close();
            exit();
        }
        
        $allPairs = array();
        $data = explode('&', $data);
        
        foreach($data as $pair) {
            $pair = explode('=', $pair);
            $allPairs[$pair[0]] = $pair[1];
        }
        
        $sqlQuery = "UPDATE administrators SET ";

        $array = array();

        // Check if administratorID is set
        if ( isset($allPairs['administratorID']) ) {
            $newAdministratorID = $allPairs['administratorID'];
            
            // Check if the length of new administratorID is 4
            if ( strlen($newAdministratorID) != 4 ) {
                http_response_code(404);
                $conn->close();
                exit();
            }

            $sqlTest = $conn->query("SELECT COUNT(*) FROM administrators where administratorID='$newAdministratorID'");
            $testResult = $sqlTest->fetch_assoc();
            
            // Check if new administratorID is already used
            if ( $testResult['COUNT(*)'] != 0 ) {
                http_response_code(404);
                $conn->close();
                exit();
            }

            array_push($array, "administratorID='$newAdministratorID'");
        }

        // Check if username is set
        if ( isset($allPairs['username']) ) {
            $newUsername = $allPairs['username'];

            // Check if the length of new username is > 0
            if ( strlen($newUsername) <= 0) {
                http_response_code(404);
                $conn->close();
                exit();
            }

            $sqlTest = $conn->query("SELECT COUNT(*) FROM administrators where username='$newUsername'");
            $testResult = $sqlTest->fetch_assoc();
            
            // Check if new username is already used
            if ( $testResult['COUNT(*)'] != 0 ) {
                http_response_code(404);
                $conn->close();
                exit();
            }


            array_push($array, "username='$newUsername'");
        }

        // Check if password is set
        if ( isset($allPairs['password']) ) {
            $newPassword = $allPairs['password'];

            // Check if the length of new password is > 0
            if( strlen($newPassword) <= 0) {
                http_response_code(404);
                $conn->close();
                exit();
            }

            array_push($array, "password='$newPassword'");
        }

        // Check if name is set
        if ( isset($allPairs['name']) ) {
            $newName = $allPairs['name'];
            array_push($array, "name='$newName'");
        }

        // Check if surname is set
        if ( isset($allPairs['surname']) ) {
            $newSurname = $allPairs['surname'];
            array_push($array, "surname='$newSurname'");
        }

        $sqlQuery .= implode(', ', $array);

        $sqlQuery .= " WHERE administratorID=$administratorID";

        $sql = $conn->query($sqlQuery);

        // Success
        http_response_code(200);
        $conn->close();
        exit();
    }
    //DELETE
    else if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
        // Check if the administrator to delete is selected
        if ( !isset($_GET['administratorID']) ) {
            http_response_code(400);
            $conn->close();
            exit();
        }

        $administratorID = $conn->real_escape_string($_GET['administratorID']);

        $conn->query("DELETE FROM administrators WHERE administratorID='$administratorID'");

        // Success
        http_response_code(200);
        $conn->close();
        exit();
    }
?>