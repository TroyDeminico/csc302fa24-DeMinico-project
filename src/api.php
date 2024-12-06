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

    if ($action == 'addUser') {
        $saltedHash = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $isAdmin = isset($_POST['admin']) ? $_POST['admin'] : null; // Handle the undefined key warning
        echo json_encode(addUser($_POST['username'], $saltedHash, $isAdmin));
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
        echo updateFavoriteTeams();
    }else if ($action == 'getFavTeamStats') {
        if (isset($_POST['teamName']) && isset($_POST['teamAbbreviation'])) {
            echo getFavoriteTeamStats($_POST['teamName'], $_POST['teamAbbreviation']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Missing teamName or teamAbbreviation']);
        }
    } else if ($action == 'getFavoriteTeams') {
        echo getFavoriteTeams();
    }
    else if ($action == 'removeFavoriteTeam') {
        if (isset($_POST['teamId']) && isset($_SESSION['username'])) {
            $teamId = $_POST['teamId'];
            $username = $_SESSION['username'];
            echo json_encode(removeFavoriteTeam($username, $teamId));
        } else {
            echo json_encode(['success' => false, 'message' => 'Missing teamId or user is not signed in.']);
        }
    } else if ($action == 'fetchNFLStandings') {
        echo fetchNFLStandings();
    }else if ($action == 'getLastFiveNFLGames') {
        if (isset($_POST['teamId'])) {
            echo fetchLastFiveNFLGames($_POST['teamId']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Missing teamId parameter.']);
        }
    } else if ($_POST['action'] === 'addFavoriteNFLTeam') {
        $favoriteNFLTeams = json_decode($_POST['favoriteNFLTeams'], true);
        $result = updateUserFavoriteNFLTeams($_SESSION['username'], $favoriteNFLTeams);
        echo json_encode($result);
    } else if ($_POST['action'] === 'getFavoriteNFLTeams') {
        $result = getFavoriteNFLTeams($_SESSION['username']);
        echo json_encode($result);
    } else if ($action === 'removeFavoriteNFLTeam') {
        if (!isset($_POST['teamId']) || !isset($_SESSION['username'])) {
            echo json_encode(['success' => false, 'message' => 'Missing teamId or user is not signed in.']);
            exit;
        }
    
        $teamId = $_POST['teamId'];
        $username = $_SESSION['username'];
        echo json_encode(removeFavoriteNFLTeam($username, $teamId));
    }else {
        echo json_encode([
            'success' => false, 
            'error' => 'Invalid action: '. $action
        ]);
    }
}


    // handles user sign in
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



    //handles user signing out 
    function signOut() {
        if (isset($_SESSION['signed_in'])) {
            session_destroy();
            return json_encode(['success' => true, 'message' => 'Signed out successfully']);
        } else {
            return json_encode(['success' => false, 'message' => 'You are not signed in']);
        }
    }

    // for future use this will say if admin yes or no
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

    //from API code 
    //gets the nba teams 5 most recent games
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
    
    // get the NBA current standigs
    function fetchNBAStandings() {
        $url = "https://sport-highlights-api.p.rapidapi.com/nba/standings?leagueType=NBA&year=2025";
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
    
    
    
    
    // used to display on page
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

    //how the favorite teams are updated in the db no dupes allowed
    function updateFavoriteTeams(){
        global $dbh;
    
        if (!isset($_SESSION['signed_in']) || !$_SESSION['signed_in']) {
            return json_encode(['success' => false, 'message' => 'User not signed in']);
        }
    
        $username = $_SESSION['username'];
    
        if (!isset($_POST['favoriteTeams'])) {
            return json_encode(['success' => false, 'message' => 'Invalid favoriteTeams parameter']);
        }
    
        $newTeams = json_decode($_POST['favoriteTeams'], true);
    
        if (!is_array($newTeams)) {
            return json_encode(['success' => false, 'message' => 'Invalid favoriteTeams data']);
        }
    
        try {
            $statement = $dbh->prepare('SELECT favoriteTeams FROM Users WHERE username = :username');
            $statement->execute([':username' => $username]);
            $result = $statement->fetch(PDO::FETCH_ASSOC);
    
            $currentTeams = isset($result['favoriteTeams']) ? json_decode($result['favoriteTeams'], true) : [];
            if (!is_array($currentTeams)) $currentTeams = [];
    
            // makes sure no duplicates
            $teamsById = [];
    
            foreach ($currentTeams as $team) {
                $teamsById[$team['id']] = $team;
            }
    
            foreach ($newTeams as $team) {
                $teamId = $team['id'];
                $teamsById[$teamId] = $team;
            }
    
            $updatedTeams = array_values($teamsById);
    
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
    
    
    // send back favorite teams to be displayed
    function getFavoriteTeams(){
        global $dbh;
    
        if (!isset($_SESSION['signed_in']) || !$_SESSION['signed_in']) {
            return json_encode(['success' => false, 'message' => 'User not signed in']);
        }
    
        $username = $_SESSION['username'];
    
        try {
            $statement = $dbh->prepare('SELECT favoriteTeams FROM Users WHERE username = :username');
            $statement->execute([':username' => $username]);
            $result = $statement->fetch(PDO::FETCH_ASSOC);
    
            $favoriteTeams = isset($result['favoriteTeams']) ? json_decode($result['favoriteTeams'], true) : [];
    
            return json_encode(['success' => true, 'favoriteTeams' => $favoriteTeams]);
        } catch(PDOException $e) {
            return json_encode(['success' => false, 'message' => "Error fetching favorite teams: $e"]);
        }
    }
    
    // for future use not in use
    function fetchSpecificMatch($params = []) {
    $url = "https://sport-highlights-api.p.rapidapi.com/nba/matches";

    // Prepare query parameters
    $queryParams = http_build_query($params);

    // Append query parameters to the URL
    $url .= '?' . $queryParams;

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_HTTPHEADER => [
            "x-rapidapi-host: sport-highlights-api.p.rapidapi.com",
            "x-rapidapi-key: c9fb707a89msh614d97148943c1cp1dddd2jsn1e4723e8e951'" 
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        return json_encode(['success' => false, 'error' => "cURL Error #: $err"]);
    } else {
        $data = json_decode($response, true);
        if (!$data) {
            return json_encode(['success' => false, 'error' => 'Invalid JSON response from API.']);
        }
        return json_encode(['success' => true, 'data' => $data]);
    }
}

// update favorite teams by deleting 1
function removeFavoriteTeam($username, $teamId) {
    global $dbh;

    try {
        // gets the current favorite teams for the user
        $statement = $dbh->prepare('SELECT favoriteTeams FROM Users WHERE username = :username');
        $statement->execute([':username' => $username]);
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$result || !isset($result['favoriteTeams'])) {
            return error('Favorite teams not found for this user.');
        }

        // decode JSON of fav teams
        $favoriteTeams = json_decode($result['favoriteTeams'], true);
        if (!is_array($favoriteTeams)) {
            return error('Invalid favorite teams data.');
        }

        // gets the team that is getting removed
        $updatedTeams = array_filter($favoriteTeams, function ($team) use ($teamId) {
            return $team['id'] != $teamId;
        });

        // updates the favorite teams in the database for that user
        $statement = $dbh->prepare(
            'UPDATE Users SET favoriteTeams = :favoriteTeams, updatedAt = datetime() WHERE username = :username'
        );
        $statement->execute([
            ':favoriteTeams' => json_encode(array_values($updatedTeams)),
            ':username' => $username
        ]);

        return ['success' => true, 'message' => 'Favorite team removed successfully.'];
    } catch (PDOException $e) {
        return error("There was an error removing the favorite team: $e");
    }
}

// gets nfl current standings
function fetchNFLStandings() {
    $url = "https://sport-highlights-api.p.rapidapi.com/american-football/standings?leagueType=NFL&year=2024";

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
        error_log("cURL Error: $err");
        return json_encode(['success' => false, 'error' => "cURL Error: $err"]);
    }

    $data = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("JSON Decode Error: " . json_last_error_msg());
        return json_encode(['success' => false, 'error' => 'Invalid JSON from API']);
    }

    error_log("Fetch NFL Standings Success: " . print_r($data, true));
    return json_encode(['success' => true, 'data' => $data['data'] ?? []]);
}

// gets the 5 most recent games for an NFL team
function fetchLastFiveNFLGames($teamId) {
    $url = "https://sport-highlights-api.p.rapidapi.com/american-football/last-five-games?teamId=$teamId";

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
        error_log("cURL Error: $err");
        return json_encode(['success' => false, 'error' => "cURL Error: $err"]);
    }

    // raw response from the api is logged
    error_log("Raw API Response: " . $response);

    $data = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("JSON Decode Error: " . json_last_error_msg());
        return json_encode(['success' => false, 'error' => 'Invalid JSON from API']);
    }

    // sees if response form API is an array or not
    if (is_array($data)) {
        error_log("Parsed API Data: " . print_r($data, true));
        return json_encode(['success' => true, 'data' => $data]);
    } else {
        error_log("NFL API Error: Unexpected API response structure");
        return json_encode(['success' => false, 'error' => 'Unexpected API response structure']);
    }
}

// gets users fav nfl teams from the db
function getFavoriteNFLTeams($username) {
    global $dbh;

    try {
        // gets the favorite NFL teams for the user
        $statement = $dbh->prepare('SELECT favoriteNFLTeams FROM Users WHERE username = :username');
        $statement->execute([':username' => $username]);
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return ['success' => false, 'message' => 'No favorite NFL teams found for the user.'];
        }

        $favoriteNFLTeams = json_decode($result['favoriteNFLTeams'], true);

        if (!is_array($favoriteNFLTeams)) {
            $favoriteNFLTeams = [];
        }

        return ['success' => true, 'favoriteNFLTeams' => $favoriteNFLTeams];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error fetching favorite NFL teams: ' . $e->getMessage()];
    }
}

// update users fav NFL teams with new team(s) make sure no dupes
function updateUserFavoriteNFLTeams($username, $newTeams) {
    global $dbh;

    try {
        // get favorite NFL teams that user has already
        $statement = $dbh->prepare('SELECT favoriteNFLTeams FROM Users WHERE username = :username');
        $statement->execute([':username' => $username]);
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        $currentTeams = isset($result['favoriteNFLTeams']) ? json_decode($result['favoriteNFLTeams'], true) : [];
        if (!is_array($currentTeams)) {
            $currentTeams = [];
        }

        // makes sure no duplicates
        $teamsById = [];
        foreach ($currentTeams as $team) {
            $teamsById[$team['id']] = $team;
        }
        foreach ($newTeams as $team) {
            $teamsById[$team['id']] = $team;
        }

        $updatedTeams = array_values($teamsById);

        // updates the database
        $statement = $dbh->prepare(
            'UPDATE Users SET favoriteNFLTeams = :favoriteNFLTeams, updatedAt = datetime() WHERE username = :username'
        );
        $statement->execute([
            ':favoriteNFLTeams' => json_encode($updatedTeams),
            ':username' => $username
        ]);

        return ['success' => true, 'message' => 'Favorite NFL teams updated successfully.', 'updatedTeams' => $updatedTeams];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error updating favorite NFL teams: ' . $e->getMessage()];
    }
}

// gets rid of a fav NFL team from db
function removeFavoriteNFLTeam($username, $teamId) {
    global $dbh;

    try {
        // gets the NFL teams user has
        $statement = $dbh->prepare('SELECT favoriteNFLTeams FROM Users WHERE username = :username');
        $statement->execute([':username' => $username]);
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        // makes sure there is teams
        if (!$result || !isset($result['favoriteNFLTeams'])) {
            return ['success' => false, 'message' => 'Favorite NFL teams not found for this user.'];
        }

        // JSOn becomes array
        $favoriteNFLTeams = json_decode($result['favoriteNFLTeams'], true);
        if (!is_array($favoriteNFLTeams)) {
            return ['success' => false, 'message' => 'Invalid favorite NFL teams data.'];
        }

        // find team to remove
        $updatedTeams = array_filter($favoriteNFLTeams, function ($team) use ($teamId) {
            return $team['id'] != $teamId;
        });

        // updates NFL teams in db
        $statement = $dbh->prepare(
            'UPDATE Users SET favoriteNFLTeams = :favoriteNFLTeams, updatedAt = datetime() WHERE username = :username'
        );
        $statement->execute([
            ':favoriteNFLTeams' => json_encode(array_values($updatedTeams)), // AI used to help 
            ':username' => $username
        ]);

        return ['success' => true, 'message' => 'Favorite NFL team removed successfully.'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error removing favorite NFL team: ' . $e->getMessage()];
    }
}






    
    