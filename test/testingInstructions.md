# Testing Instructions

## Unit Testing

### What to Test:
1. **User Authentication**:
   - **`signIn` Function**:
     - Test with valid credentials and verify successful login.
     - Test with invalid credentials and ensure an appropriate error message is displayed.
   - **`addUser` Function**:
     - Add a user with a unique username and verify the user is saved successfully.
     - Attempt to add a user with an existing username and confirm it fails gracefully (requires a pop-up or error message in the UI).
   - **`signOut` Function**:
     - Sign in, then call `signOut` and ensure the session is properly terminated.
   - **`getAdminStatus` Function**:
     - Test for correct admin status when logged in as an admin user.
     - Test for appropriate response when logged in as a non-admin user.

2. **Favorite Team Management**:
   - **`updateFavoriteTeams` Function**:
     - Add new NBA teams to a user's favorites and confirm they are saved in the database.
     - Attempt to add invalid or duplicate teams and ensure proper handling (e.g., prevent duplicates).
   - **`getFavoriteTeams` Function**:
     - Fetch a user’s favorite teams and verify the correct list is returned.
     - Test for proper handling of an empty favorites list.
   - **`removeFavoriteTeam` Function**:
     - Remove a favorite team and verify it no longer exists in the user’s favorites list.
     - Attempt to remove a team not in the favorites list and ensure proper error handling.

3. **Favorite NFL Team Management**:
   - **`addFavoriteNFLTeam` Function**:
     - Add new NFL teams to a user's favorites and confirm they are saved in the database.
     - Ensure duplicates are not allowed.
   - **`getFavoriteNFLTeams` Function**:
     - Fetch the user’s favorite NFL teams and confirm the correct list is returned.
   - **`removeFavoriteNFLTeam` Function**:
     - Remove an NFL team and verify it no longer exists in the user’s favorites.
     - Test with a team not in the favorites list to confirm proper error handling.

4. **API Response Structure**:
   - **`fetchNBAStandings` Function**:
     - Verify that standings data includes fields like team names, wins, and losses.
     - Simulate API failures and ensure appropriate error messages are returned.
   - **`fetchNFLStandings` Function**:
     - Verify that standings data includes division names, wins, and losses.
     - Handle invalid API responses gracefully.
   - **`fetchLastFiveGames` and `fetchLastFiveNFLGames` Functions**:
     - Test that the correct game data is returned for valid `teamId` values.
     - Test with invalid `teamId` and ensure error handling works correctly.

---

## API Testing

### What to Test:
1. **API Endpoints**:
   - `/api/user/register`: Test user registration with valid and invalid inputs (e.g., missing username, password, or invalid characters).
   - `/api/user/login`: Test with correct and incorrect credentials.
   - `/api/team/stats`: Test fetching stats for valid and invalid `teamId`.
   - `/api/favorites/update`: Test with valid and invalid favorite team data for both NBA and NFL.

2. **Error Handling**:
   - Verify proper error messages for:
     - Missing parameters.
     - Invalid data (e.g., malformed JSON or unsupported characters).
   - Test fallback behavior if external APIs fail (e.g., show an error message or cached data).

### How to Perform:
1. Open the browser's Developer Tools (Network tab).
2. Perform API calls (e.g., signing in, updating favorites) and confirm:
   - API requests return `200 OK` status.
   - Responses include expected data (e.g., JSON format with proper fields).
3. Simulate invalid inputs and verify error responses:
   - Missing or invalid `teamId`.
   - Invalid credentials for login.

---

## Component Testing

### What to Test:
1. **Forms**:
   - **Sign-In Form**:
     - Test with valid credentials and verify successful redirection to the homepage.
     - Submit invalid credentials and confirm an error message is displayed.
   - **Sign-Up Form**:
     - Register a new user with unique credentials and verify they are saved in the database.
     - Attempt to register with an existing username and ensure the error message is displayed.
   - **Add Favorite Teams**:
     - Add NBA and NFL teams through the UI and verify the changes reflect in the database.
     - Ensure duplicate teams cannot be added.

2. **Dynamic Data Integration**:
   - **Standings Pages**:
     - Confirm NBA and NFL standings dynamically fetch and display up-to-date data from the APIs.
   - **Favorites Pages**:
     - Ensure the list of favorite teams (NBA and NFL) updates in real time after additions or deletions.
   - **Last Five Games**:
     - Display the last five games for a selected team and verify data matches the external API.
   - **User Profile**:
     - Verify user details (e.g., username, admin status) are correctly displayed.

3. **Error Handling in UI**:
   - Simulate API failures (e.g., disconnect the API or provide invalid data) and verify:
     - Error messages are displayed to the user.
     - UI remains responsive and does not crash.

---

### How to Perform Component Testing:
1. Open the app in a web browser.
2. Interact with all forms, buttons, and dynamic elements:
   - Add or remove teams.
   - Fetch standings or last five games.
3. Check the browser’s developer tools for:
   - Network requests and responses.
   - Errors or warnings in the Console tab.
4. Compare UI updates with backend changes:
   - Verify that database changes reflect in the UI (e.g., added/removed favorite teams).
