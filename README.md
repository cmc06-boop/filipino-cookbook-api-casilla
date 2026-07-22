# Filipino Cookbook API

## API Description 
The Filipino Cookbook API is a RESTful API developed using PHP, Slim Framework, and MySQL. It provides information about Filipino foods, including their categories, origins, ingredients, and cooking instructions. The API returns data in JSON format and can be used by developers or students to build applications that consume Filipino food data.

### Purpose of API
- Provide Filipino food information through a RESTful API.
- Allow developers to access food, category, origin, and ingredient data.
- Demonstrate API development using PHP, Slim Framework, and MySQL.

### Type of Information Provided
- Filipino foods
- Food categories
- Food origins
- Food ingredients
- Cooking instructions

### Intended Users
- Students
- Developers
- Client applications that consume REST APIs

### Main Functions of The API
- Retrieve all Filipino foods
- Retrieve a specific food
- Search foods by name
- Retrieve categories
- Retrieve ingredients
- Return data in JSON format
- Add a new food using a protected endpoint

## Features
- Retrieve all foods
- Retrieve a specific food
- Search food by name
- Retrieve categories
- Retrieve ingredients
- Add new food (Protected)
- JSON responses
- Input validation
- Bearer token authentication

## Technologies Used
- PHP
- Slim Framework 
- MySQL
- Composer
- Apache
- XAMPP
- JSON
- Thunder Client / Postman
- Git
- GitHub

## Installation Instructions

### 1. Clone the repository

```bash
git clone https://github.com/YOUR_USERNAME/filipino-cookbook-api-casilla.git
```

### 2. Open the project folder

```bash
cd filipino-cookbook-api-casilla
```

### 3. Install the dependencies

```bash
composer install
```

### 4. Create the database

Create a database named:

```text
filipino_cookbook_api
```

Then import the SQL file included in the repository.

### 5. Configure the database

Copy:

```text
config.example.php
```

Rename it to:

```text
config.php
```

Update the database credentials according to your local environment.

### 6. Start Apache and MySQL

Start both services using the XAMPP Control Panel.

### 7. Run the API

Open:

```text
http://127.0.0.1:8000/
```

## Database Setup

### Database Name

```text
filipino_cookbook_api
```

### Main Tables

- foods
- categories
- origins
- ingredients
- food_ingredients

### Relationship

```
categories
     │
     │
    foods
   /     \
origins  food_ingredients
             │
        ingredients
```
## Base URL
```text
http://127.0.0.1:8000/
```

## Authentication Instructions

## Endpoint Documentation

## HTTP Status Code


## Testing Evidence


# Optional API Enhancements

## Description of the Enhancement
This enhancement keeps the original API behavior and adds the requested category-based endpoints and input validation improvements. The API now supports retrieving foods by category, getting the number of foods under each category, and returning clearer validation messages for invalid requests while still using the existing search endpoint already available in the original API.

## Purpose of the Enhancement
The enhancements were implemented to improve the functionality and reliability of the API.

- Allow users to retrieve all foods under a selected category.
- Provide the total number of foods available in each category.
- Prevent invalid or incomplete requests by validating user input before processing.

## Files Modified
- [public/index.php](public/index.php)

## Endpoints Added
- GET /api/categories/{id}/foods
  - Returns all foods under a selected category.

- GET /api/categories/food-counts
  - Returns the total number of foods under each category.

## Security Features Implemented

- Input validation
 - Proper error handling for invalid or missing parameters

## Instructions for Testing the Enhancement

### Get Foods by Category

1. Send a GET request to `/api/categories/{id}/foods`.
2. Replace `{id}` with a valid category ID.
3. Verify that the API returns all foods under the selected category.

### Get Number of Foods Under Each Category

1. Send a GET request to `/api/categories/food-counts`.
2. Verify that the API returns all categories with their corresponding number of foods.

### Input Validation
1. Test using an invalid category ID.
2. Test using missing or empty parameters.
3. Verify that the API returns the appropriate HTTP status code and JSON error message.  

## Screenshots of Successful Testing

### Get Foods by Category

![Foods By Category](Screenshots/Foods%20By%20Category.png)

### Get Number of Foods Under Each Category

![Number of Foods Per Category](Screenshots/Number%20of%20Foods%20Per%20Category.png)

### Input Validation
![Validation Error](Screenshots/Validation%20Error.png)

## Developer Information
- **Name:** Cherry Lyn M. Casilla
- **Course and Section:** BS Information Technology – [4B]
- **GitHub Username:** cmc06-boop
- **Repository:** https://github.com/cmc06-boop/filipino-cookbook-api-casilla
- **Date Completed:** July, 2026