<?php

// TODO Change this as needed. SQLite will look for a file with this name, or
// create one if it can't find it.
$dbName = 'data.db';

// Leave this alone. It checks if you have a directory named www-data in
// you home directory (on a *nix server). If so, the database file is
// sought/created there. Otherwise, it uses the current directory.
// The former works on digdug where I've set up the www-data folder for you;
// the latter should work on your computer.
$matches = [];
preg_match('#^/~([^/]*)#', $_SERVER['REQUEST_URI'], $matches);
$homeDir = count($matches) > 1 ? $matches[1] : '';
$dataDir = "/home/$homeDir/www-data";
if(!file_exists($dataDir)){
    $dataDir = __DIR__;
}
$dbh = new PDO("sqlite:$dataDir/$dbName");
// Set our PDO instance to raise exceptions when errors are encountered.
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


/**
 * Returns an associative array with two fields:
 *  - success: false
 *  - error:  $message
 * 
 * @return An associative array describing the error.
 */
function error($message){
    return [
        'success' => false, 
        'error' => $message
    ];
}

function createTables(){
    global $dbh;

    // Create the Users table.
    try{
        $dbh->exec('create table if not exists Users('. 
            'id integer primary key autoincrement, '. 
            'username text UNIQUE, '. 
            'password text, '.
            'admin text, '. 
            'favoriteTeams text, '.
            'createdAt datetime default(datetime()), '.
            'updatedAt datetime default(datetime()))');
    } catch(PDOException $e){
        echo "There was an error creating the Users table: $e";
    }
}

// Functions for the User

function addUser($username, $hashedPassword, $admin, $favoriteTeams = null){
    global $dbh;
    $id = null;

    // set up the admin default
    $isAdmin = isset($admin) ? 'yes' : 'no'; 
    try {
        $statement = $dbh->prepare(
            'insert into Users(username, password, admin, favoriteTeams) values (:username, :password, :admin, :favoriteTeams)');
        $statement->execute([
            ':username' => $username,
            ':password' => $hashedPassword,
            ':admin' => $isAdmin,
            ':favoriteTeams' => $favoriteTeams
        ]);

        $id = $dbh->lastInsertId();
    } catch(PDOException $e){
        return error("There was an error adding a user: $e");
    }
    return [
        'success' => true,
        'id' => $id
    ];
}


//Needed for sign in
function getUserByUsername($username){
    global $dbh;
    $userData = null;
    try {
        $statement = $dbh->prepare('select * from Users where username = :username');
        $statement->execute([
            ':username' => $username
        ]);
        $userData = $statement->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e){
        return error("There was an error retrieving the user: $e");
    }
    $userData['success'] = true;
    return $userData;
}

createTables();
?>