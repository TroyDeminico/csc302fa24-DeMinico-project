<?php
header('Content-type: application/json');

session_start();


// For debugging:
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once('db.php');

// Handle incoming requests.
if(array_key_exists('action', $_POST)){
    $action = $_POST['action'];

    if($action == 'addUser'){
        $saltedHash = password_hash($_POST['password'], PASSWORD_BCRYPT);
        echo json_encode(addUser($_POST['username'], $saltedHash));

    }else if ($action == 'signIn') {
        echo signIn($_POST['username'], $_POST['password']);
    
    }else if ($action == 'signOut') {
        echo signOut(); 
    
    }else {
        echo json_encode([
            'success' => false, 
            'error' => 'Invalid action: '. $action
        ]);
    }
}



    function signIn($username, $password) {
        $userInfo = getUserByUsername($username);
    
        if ($userInfo['success'] && password_verify($password, $userInfo['password'])) {
            $_SESSION['signed_in'] = true;
            $_SESSION['username'] = $username; 
            return json_encode(['success' => true, 'message' => 'Signed in successfully']);
        } else {
            return json_encode(['success' => false, 'message' => 'Invalid credentials']);
        }
    }



    function signOut() {
        if (isset($_SESSION['signed_in'])) {
            session_destroy();
            return json_encode(['success' => true, 'message' => 'Signed out successfully']);
        } else {
            return json_encode(['success' => false, 'message' => 'You are not signed in']);
        }
    }
