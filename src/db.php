<?php

$dbName = 'data.db';

$matches = [];
preg_match('#^/~([^/]*)#', $_SERVER['REQUEST_URI'], $matches);
$homeDir = count($matches) > 1 ? $matches[1] : '';
$dataDir = "/home/$homeDir/www-data";
if (!file_exists($dataDir)) {
    $dataDir = __DIR__;
}
$dbh = new PDO("sqlite:$dataDir/$dbName");

$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

/**
 * Returns an associative array with two fields:
 *  - success: false
 *  - error:  $message
 * 
 * @return An associative array describing the error.
 */
function error($message) {
    return [
        'success' => false,
        'error' => $message
    ];
}

function createTables() {
    global $dbh;

    try {
        $dbh->exec('CREATE TABLE IF NOT EXISTS Users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE,
            password TEXT,
            admin TEXT DEFAULT "no",
            favoriteTeams TEXT DEFAULT "[]",
            favoriteNFLTeams TEXT DEFAULT "[]",
            createdAt DATETIME DEFAULT (DATETIME()),
            updatedAt DATETIME DEFAULT (DATETIME())
        )');
    } catch (PDOException $e) {
        echo "There was an error creating the Users table: $e";
    }

    try {
        $dbh->exec('ALTER TABLE Users ADD COLUMN favoriteNFLTeams TEXT DEFAULT "[]"');
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'duplicate column name') === false) {
            echo "Error adding column favoriteNFLTeams: " . $e->getMessage();
        }
    }
    
}


// Functions for the User

function addUser($username, $hashedPassword, $admin, $favoriteTeams = null, $favoriteNFLTeams = null) {
    global $dbh;
    $id = null;

    // set up the admin default
    $isAdmin = isset($admin) ? 'yes' : 'no';
    try {
        $statement = $dbh->prepare(
            'insert into Users(username, password, admin, favoriteTeams, favoriteNFLTeams) values (:username, :password, :admin, :favoriteTeams, :favoriteNFLTeams)'
        );
        $statement->execute([
            ':username' => $username,
            ':password' => $hashedPassword,
            ':admin' => $isAdmin,
            ':favoriteTeams' => $favoriteTeams,
            ':favoriteNFLTeams' => $favoriteNFLTeams
        ]);

        $id = $dbh->lastInsertId();
    } catch (PDOException $e) {
        return error("There was an error adding a user: $e");
    }
    return [
        'success' => true,
        'id' => $id
    ];
}

// Needed for sign in
function getUserByUsername($username) {
    global $dbh;
    $userData = null;
    try {
        $statement = $dbh->prepare('select * from Users where username = :username');
        $statement->execute([
            ':username' => $username
        ]);
        $userData = $statement->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return error("There was an error retrieving the user: $e");
    }
    $userData['success'] = true;
    return $userData;
}




createTables();
?>
