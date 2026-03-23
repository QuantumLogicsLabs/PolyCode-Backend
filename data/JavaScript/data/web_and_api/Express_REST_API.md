# Express.js REST API

## Overview
This project demonstrates how to build a simple **REST API** using **Express.js**, the most popular web framework for Node.js. Express simplifies the process of handling HTTP requests and defining routes.

## Key Features
- **Middleware**: Express's `express.json()` middleware is used to parse JSON data in the request body.
- **RESTful Endpoints**:
    - **GET `/api/users`**: Fetches all users from an in-memory array.
    - **GET `/api/users/:id`**: Retrieves a specific user by their unique identifier.
    - **POST `/api/users`**: Creates a new user object and adds it to the collection.
    - **PUT `/api/users/:id`**: Updates an existing user's information.
    - **DELETE `/api/users/:id`**: Removes a user from the system.

## Implementation Details
- **Route Parameters**: Uses `:id` to capture dynamic values from the URL.
- **In-Memory Data**: For this learning example, we use a simple JavaScript array (`users`) to store data instead of a database.
- **HTTP Status Codes**:
    - `200 OK`: Success.
    - `201 Created`: Successfully created a new resource.
    - `204 No Content`: Successfully deleted a resource.
    - `404 Not Found`: Resource does not exist.

## Prerequisites
- **Node.js**: Installed on your system.
- **Express**: Install via NPM:
```bash
npm install express
```

## Running the Server
Start the server using Node:
```bash
node "Express REST API.js"
```
The server will be available at `http://localhost:3000`.

[Express REST API.js](file:///c:/Users/HP/OneDrive/Documents/Projects/PolyCode/JavaScript/data/api_development/Express%20REST%20API.js)