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
        echo json_encode(addUser($_POST['username'], $saltedHash, $_POST['admin']));

    }else if ($action == 'signIn') {
        echo signIn($_POST['username'], $_POST['password']);
    
    }else if ($action == 'signOut') {
        echo signOut(); 
    
    }else if ($action == 'getAdminStatus') {
        echo getAdminStatus(); 
    
    } else if ($action == 'getLastFiveGames') {
        if (isset($_POST['teamId'])) {
            echo fetchLastFiveGames($_POST['teamId']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Missing teamId parameter.']);
        }
    } else if ($action == 'fetchNBAStandings') {
        echo fetchNBAStandings();
    }else if ($action == 'getUsername'){
        echo getUsername();
    }else if ($action == 'addFavoriteTeam'){
        echo updateFavoriteTeams($_POST['username'], $_POST['favoriteTeams']);
    }else if ($action == 'getFavTeamStats') {
        if (isset($_POST['teamName']) && isset($_POST['teamAbbreviation'])) {
            echo getFavoriteTeamStats($_POST['teamName'], $_POST['teamAbbreviation']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Missing teamName or teamAbbreviation']);
        }
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
            $_SESSION['admin'] = $userInfo['admin'];
            return json_encode(['success' => true, 'message' => 'Signed in successfully', 'isAdmin' => $userInfo['admin'] === 'yes']);
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

    function getAdminStatus(){
        if(isset($_SESSION['signed_in']) && $_SESSION['signed_in']) {
            return json_encode([
                'success' => true,
                'isAdmin' => $_SESSION['admin'] === 'yes',
                'username' => $_SESSION['username']
            ]);
        } else {
            return json_encode(['success' => false, 'message' => 'User not signed in']);
        }
    }

    #from API code
    function fetchLastFiveGames($teamId) {
        $url = "https://sport-highlights-api.p.rapidapi.com/nba/last-five-games?teamId=$teamId";
    
        $curl = curl_init();
    
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "x-rapidapi-host: sport-highlights-api.p.rapidapi.com",
                "x-rapidapi-key: c9fb707a89msh614d97148943c1cp1dddd2jsn1e4723e8e951" 
            ],
        ]);
    
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
    
        if ($err) {
            return json_encode(['success' => false, 'error' => "cURL Error: $err"]);
        }
    
        $data = json_decode($response, true);
    
        if (!$data) {
            return json_encode(['success' => false, 'error' => 'Invalid JSON response from API.']);
        }
    
        return json_encode(['success' => true, 'data' => $data]);
    }

    function fetchNBAStandings() {
        $url = "https://sport-highlights-api.p.rapidapi.com/nba/standings?leagueType=NBA&year=2024";
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "x-rapidapi-host: sport-highlights-api.p.rapidapi.com",
                "x-rapidapi-key: c9fb707a89msh614d97148943c1cp1dddd2jsn1e4723e8e951"
            ],
        ]);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return json_encode(['success' => false, 'error' => "cURL Error: $err"]);
        }
        $data = json_decode($response, true);
        if (isset($data['data']) && is_array($data['data'])) {
            return json_encode(['success' => true, 'data' => $data['data']]);
        } else {
            return json_encode(['success' => false, 'error' => 'Invalid data format.']);
        }
    }
    
    
    
    

    function getUsername() {
        if (isset($_SESSION['signed_in']) && $_SESSION['signed_in']) {
            if (isset($_SESSION['username'])) {
                return json_encode(['success' => true, 'username' => $_SESSION['username']]);
            } else {
                return json_encode(['success' => false, 'message' => 'Username not found in session']);
            }
        } else {
            return json_encode(['success' => false, 'message' => 'User not signed in']);
        }
    }

    function updateFavoriteTeams(){
        global $dbh;
    
        // make sure user is signed in
        if (!isset($_SESSION['signed_in']) || !$_SESSION['signed_in']) {
            return json_encode(['success' => false, 'message' => 'User not signed in']);
        }
    
        $username = $_SESSION['username'];
    
        // validate the incoming favorite teams
        if (!isset($_POST['favoriteTeams']) || !is_array($_POST['favoriteTeams'])) {
            return json_encode(['success' => false, 'message' => 'Invalid favoriteTeams parameter']);
        }
    
        $newTeams = $_POST['favoriteTeams'];
    
        try {
            // get the current favoriteTeams from the database
            $statement = $dbh->prepare('SELECT favoriteTeams FROM Users WHERE username = :username');
            $statement->execute([':username' => $username]);
            $result = $statement->fetch(PDO::FETCH_ASSOC);
    
            $currentTeams = isset($result['favoriteTeams']) ? json_decode($result['favoriteTeams'], true) : [];
            if (!is_array($currentTeams)) $currentTeams = [];
    
            // make sure no duplicate in by merging
            $updatedTeams = array_unique(array_merge($currentTeams, $newTeams));
    
            // update the database
            $statement = $dbh->prepare(
                'UPDATE Users SET favoriteTeams = :favoriteTeams, updatedAt = datetime() WHERE username = :username'
            );
            $statement->execute([
                ':favoriteTeams' => json_encode($updatedTeams),
                ':username' => $username
            ]);
        } catch(PDOException $e) {
            return json_encode(['success' => false, 'message' => "Error updating favorite teams: $e"]);
        }
    
        return json_encode(['success' => true, 'message' => 'Favorite teams updated successfully']);
    }

    function getFavoriteTeamStats($teamName, $teamAbbreviation) {
        // Encode team name to handle spaces and special characters
        // Ai used for thi line
        $encodedTeamName = urlencode($teamName);
        

        // API URL
        $apiUrl = "https://sport-highlights-api.p.rapidapi.com/nba/teams?displayName=$encodedTeamName&league=NBA&abbreviation=$teamAbbreviation";
        
        
        $curl = curl_init();
        
        // set cURL options
        curl_setopt_array($curl, [
            CURLOPT_URL => $apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "x-rapidapi-host: sport-highlights-api.p.rapidapi.com",
                "x-rapidapi-key: c9fb707a89msh614d97148943c1cp1dddd2jsn1e4723e8e951" 
            ],
        ]);
        
        // get the repsonse throug cURL
        $response = curl_exec($curl);
        $err = curl_error($curl);
        
        // close cURL
        curl_close($curl);
        
        // Check for errors and return response
        // AI used for help with this section
        if ($err) {
            return json_encode(['success' => false, 'message' => "cURL Error: $err"]);
        } else {
            $decodedResponse = json_decode($response, true);
            if ($decodedResponse && isset($decodedResponse[0])) {
                return json_encode(['success' => true, 'data' => $decodedResponse[0]]);
            } else {
                return json_encode(['success' => false, 'message' => 'No data found for the specified team']);
            }
        }
    }
    
    