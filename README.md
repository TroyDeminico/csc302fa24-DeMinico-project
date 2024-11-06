Sports App
Project Description
The Sports App allows users to add their favorite players, view up-to-date statistics, and keep track of recent sports events. The app enables user authentication, player tracking, and event updates through a connected API. It's hosted live on the DigDug server, with the backend managed by PHP and SQLite.

Repository Structure
This repository contains the following files:

src/ - Contains the prototype code (HTML, CSS, JavaScript, PHP)
README.md - Provides project description, feature list, API documentation, and data model
diagrams.pdf - Contains digital wireframes and component diagram
db.php - Manages database connection with SQLite
Feature List
This checklist outlines the planned features for the app. Each feature shows the percentage of implementation, helping track the prototype’s progress toward the final version.

 User Authentication - 100% complete
 Home Screen - 100% complete
 Player Tracking - 50% complete
 Real-time Updates - 0% complete
 User Profile Management - 0% complete
 Notifications for Player Events - 0% complete
Note: The goal for Milestone 2 is to complete roughly 25% of the app, including user authentication, home screen, and partially complete player tracking.

Live Version
The app is live and accessible on DigDug: https://digdug.cs.endicott.edu/~tdeminico/csc302/csc302Final/

API Documentation
The Sports App connects to an external API to fetch player data and interact with the database. Below is a list of API actions:

Endpoint	HTTP Method	Parameters	Response Data
/api/player/add	POST	player_id, user_id	JSON with confirmation
/api/player/stats	GET	player_id	JSON with player stats
/api/user/register	POST	username, password	JSON with user data
/api/user/login	POST	username, password	JSON with session token
/api/events/recent	GET	None	JSON with recent events
Data Model Description
The app’s data model consists of both client-side and server-side storage, managed by SQLite on the server.

Client Side: Stores session information (e.g., session token stored in cookies or local storage) for user authentication.
Server Side: SQLite database containing:
Users Table: Stores user ID, username, and password hash.
Players Table: Stores player ID, name, team, and position.
User_Players Table: Links users to their tracked players.
Events Table: Holds recent sports events linked to players.
Diagrams
All digital wireframes and a component diagram are included in diagrams.pdf, covering:

Wireframes for each major view (e.g., sign-in, home, and player stats screens).
Component Diagram showing data flow between front-end and back-end.
