# Used Ai to help format this after my testing instructions was given to it

# Testing Instructions

## Unit Testing

### What to Test:
1. **User Authentication**:
   - **`signIn` Function**:
     - Test with valid credentials and verify successful login.
     - Test with invalid credentials and ensure an appropriate error message is displayed.(Not yet added)
   - **`addUser` Function**:
     - Add a user with a unique username and verify the user is saved successfully.
     - Attempt to add a user with an existing username and confirm it fails gracefully.(Pop up needed to be added)
   - **`signOut` Function**:
     - Sign in, then call `signOut` and ensure the session is properly terminated.

2. **Team Updates**:
   - **`updateFavoriteTeams` Function**:
     - Add new teams to a user's favorites and confirm they are saved in the database.
     - Attempt to add invalid or duplicate teams and ensure proper handling (e.g., prevent duplicates).(Mostly implemented)

3. **API Response Structure**:
   - **`fetchNBAStandings` Function**:
     - Test that standings data fetched from the external API is correctly structured (e.g., includes team names, wins, losses, etc.).
     - Simulate API failures or invalid responses and check for fallback behavior or error messages.

### How to Perform:
1. Write test cases for each function, focusing on both valid and invalid inputs.
2. Call these functions directly in your PHP code or set up test scripts.
3. Verify the database reflects updates accurately (e.g., new users or favorite teams are saved).
4. Check the returned results match the expected outputs (e.g., JSON format, success/error messages).

---

## API Testing

### What to Test:
1. **API Endpoints**:
   - `/api/user/register`: Test with valid and invalid data (e.g., empty username or password).
   - `/api/user/login`: Test with correct and incorrect credentials.
   - `/api/player/stats`: Test with existing and non-existent `player_id`.
   - `/api/favorites/update`: Test updates with valid and invalid favorite team data.

2. **Error Handling**:
   - Verify proper error messages are returned for:
     - Missing parameters.
     - Invalid inputs.

### How to Perform:
1. Open Developer tools
2. Go into Network Session
3. Make sure the API request return 200 OK and expected data

---

## Component Testing

### What to Test:
1. **Forms**:
   - **Sign-In Form**:
     - Submit valid credentials and verify successful redirection to the homepage.
     - Submit invalid credentials and ensure an error message is displayed.
   - **Sign-Up Form**:
     - Create a new user with unique credentials and verify the database reflects this addition.
     - Attempt to create a user with existing credentials and ensure failure is handled gracefully.

2. **Dynamic Data Integration**:
   - Verify standings and player stats displayed on the homepage are dynamically fetched and rendered correctly from the APIs.
   - Ensure UI updates reflect changes in the backend (e.g., adding a new favorite team updates the displayed list).

3. **Error Handling in UI**:
   - Simulate API failures (e.g., disconnect the API or provide invalid data) and ensure error messages are shown appropriately in the UI.

### How to Perform:
1. Open the app in a web browser.
2. Manually interact with all forms, inputs, and buttons to verify they work as expected.
3. Check the browser’s developer tools (Console and Network tabs) for:
   - API requests being sent and responses being received.
   - Errors in the UI or backend connectivity.
4. Verify the UI updates dynamically when interacting with APIs or updating the database (e.g., new teams, player stats).
