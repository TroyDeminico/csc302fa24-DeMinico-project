# Sports App

## Project Description
The Sports App is a platform for sports enthusiasts to add their favorite teams, track team stats, and stay updated on recent sports events. Users can sign in, manage their profiles, and track real-time team data through an integrated API.

## Repository Structure
This repository includes the following files and directories:

- **src/**: Contains the primary files for the app:
  - **api.php**: PHP file handling API requests for interacting with the SQLite database.
  - **data.db**: SQLite database storing user, team, and event data.
  - **db.php**: Manages SQLite database connections and queries.
  - **home.html**: HTML file for the home screen.
  - **index.html**: Entry point for the app.
  - **profile.html**: HTML file for user profile management.
  - **standings.html**: HTML file for displaying team standings.
  
- **test/**: Reserved for future testing files and utilities.

- **README.md**: Provides an overview of the project, feature list, API documentation, and data model details.

## Feature List
This list of features highlights the completed components of the app, with ongoing progress noted:

- **User Authentication** - 100% complete
- **Home Screen** - 100% complete
- **Recent Game Tracking** - 100% complete
- **Real-time Updates** - 100% complete
- **Standings Tracking** - 100% complete
- **User Profile Management** - 100% complete
- **Favorite Team Tracking** - 100% complete

**Milestone 4 Goal**: Achieve roughly 100% completion, including user authentication, home screen, team tracking feature, standings, and sign out/up feature.

## Live Version
Access the live version of the app hosted on DigDug:  
[https://digdug.cs.endicott.edu/~tdeminico/csc302/csc302Final/](https://digdug.cs.endicott.edu/~tdeminico/csc302/csc302Final/)

## API Documentation
The app uses API calls to facilitate data retrieval and manipulation for teams, users, and events. Below is a summary of the API actions:

| Endpoint                      | HTTP Method | Parameters                                | Response Data                                      |
|-------------------------------|-------------|------------------------------------------|--------------------------------------------------|
| /api.php                      | POST        | action: addUser, username, password, admin (optional) | JSON with confirmation                           |
| /api.php                      | POST        | action: signIn, username, password       | JSON with session token                          |
| /api.php                      | POST        | action: signOut                          | JSON with confirmation                           |
| /api.php                      | POST        | action: getAdminStatus                   | JSON with admin status                           |
| /api.php                      | POST        | action: getUsername                      | JSON with the current username                  |
| /api.php                      | POST        | action: addFavoriteTeam, favoriteTeams   | JSON with updated favorite teams                |
| /api.php                      | POST        | action: getFavoriteTeams                 | JSON with a list of the user's favorite teams    |
| /api.php                      | POST        | action: removeFavoriteTeam, teamId       | JSON with confirmation of team removal          |
| /api.php                      | POST        | action: getFavTeamStats, teamName, teamAbbreviation | JSON with stats for the user's favorite team    |
| /api.php                      | POST        | action: fetchNBAStandings                | JSON with current NBA standings                 |
| /api.php                      | POST        | action: getLastFiveGames, teamId         | JSON with the last five games for a given team  |
| /api.php                      | POST        | action: fetchNFLStandings                | JSON with current NFL standings                 |
| /api.php                      | POST        | action: getLastFiveNFLGames, teamId      | JSON with the last five NFL games for a team    |
| /api.php                      | POST        | action: addFavoriteNFLTeam, favoriteNFLTeams | JSON with updated favorite NFL teams          |
| /api.php                      | POST        | action: getFavoriteNFLTeams              | JSON with the user's favorite NFL teams         |
| /api.php                      | POST        | action: removeFavoriteNFLTeam, teamId    | JSON with confirmation of NFL team removal      |

### Notes:
- **action**: This parameter determines the type of operation to be performed.
- **teamId**: The unique ID for a team (NBA or NFL).
- **teamName** and **teamAbbreviation**: Identifiers for specific teams when retrieving stats.
- **favoriteTeams** and **favoriteNFLTeams**: JSON-encoded arrays containing team details.

Each endpoint provides structured JSON responses indicating success or failure, along with relevant data or error messages.


## Data Model Description
The app's data model includes both client-side and server-side storage, with SQLite used for server data management.

- **Client Side**: Stores session information, such as session tokens (typically in cookies or local storage), for managing user authentication.
  
- **Server Side**: SQLite database tables include:
  - **Users**: Stores user ID, username, and password hash.
  - **Teams**: Stores team ID, name, league, and ranking.
  - **User_Teams**: Connects users with their tracked teams.
  - **Events**: Stores recent sports events linked to teams.

## Diagrams
All diagrams, including wireframes and a component diagram, are compiled in **diagrams.pdf**:
- **Wireframes**: Each major view (sign-in, home, team stats, standings) is mapped out.
- **Component Diagram**: Visualizes data flow between the front end (user interface) and back end (database and API interactions).
