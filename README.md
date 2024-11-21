# Sports App

## Project Description
The Sports App is a platform for sports enthusiasts to add their favorite players, track player stats, and stay updated on recent sports events. Users can sign in, manage their profiles, and track real-time player data through an integrated API.

## Repository Structure
This repository includes the following files and directories:

- **src/**: Contains prototype code in HTML, CSS, JavaScript, and PHP.
- **README.md**: Provides an overview of the project, feature list, API documentation, and data model details.
- **diagrams.pdf**: Digital wireframes for each major view and a component diagram illustrating data flow.
- **db.php**: Manages SQLite database connection and queries for user and player data.

## Feature List
This list of features highlights the completed components of the app, with ongoing progress noted:

- **User Authentication** - 100% complete
- **Home Screen** - 100% complete
- **Player Tracking** - 50% complete
- **Real-time Updates** - 0% complete
- **User Profile Management** - 0% complete
- **Notifications for Player Events** - 0% complete

**Milestone 2 Goal**: Achieve roughly 25% completion, including user authentication, home screen, and a partially complete player tracking feature.

## Live Version
Access the live version of the app hosted on DigDug:  
[https://digdug.cs.endicott.edu/~tdeminico/csc302/csc302Final/](https://digdug.cs.endicott.edu/~tdeminico/csc302/csc302Final/)

## API Documentation
The app uses API calls to facilitate data retrieval and manipulation for players, users, and events. Below is a summary of the API actions:

| Endpoint                   | HTTP Method | Parameters            | Response Data              |
|----------------------------|-------------|-----------------------|----------------------------|
| /api/player/add             | POST        | player_id, user_id    | JSON with confirmation     |
| /api/player/stats           | GET         | player_id             | JSON with player stats     |
| /api/user/register          | POST        | username, password    | JSON with user data        |
| /api/user/login             | POST        | username, password    | JSON with session token    |
| /api/events/recent          | GET         | None                  | JSON with recent events    |

## Data Model Description
The app's data model includes both client-side and server-side storage, with SQLite used for server data management.

- **Client Side**: Stores session information, such as session tokens (typically in cookies or local storage), for managing user authentication.
  
- **Server Side**: SQLite database tables include:
  - **Users**: Stores user ID, username, and password hash.
  - **Players**: Stores player ID, name, team, and position.
  - **User_Players**: Connects users with their tracked players.
  - **Events**: Stores recent sports events linked to players.

## Diagrams
All diagrams, including wireframes and a component diagram, are compiled in **diagrams.pdf**:
- **Wireframes**: Each major view (sign-in, home, player stats) is mapped out.
- **Component Diagram**: Visualizes data flow between the front end (user interface) and back end (database and API interactions).

