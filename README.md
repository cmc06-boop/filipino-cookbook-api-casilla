# Filipino Cookbook API

## API Description 
The Filipino Cookbook API is a API developed using PHP, Slim Framework, and MySQL. It provides information about Filipino foods, including their categories, origins, ingredients, and cooking instructions. The API returns data in JSON format and can be used by developers or students to build applications that consume Filipino food data.

### Purpose of API
- Provide Filipino food information through a API.
- Allow developers to access food, category, origin, instructions and ingredient data.
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
- Client applications that consume APIs

### Main Functions of The API
- Retrieve all Filipino foods
- Retrieve a details of a specific food
- Search foods by name
- Retrieve categories
- Retrieve ingredients
- Return data in JSON format
- Add a new food using a protected endpoint
- Retrieve foods under a specific category
- Authenticate requests using a Bearer token
- Retrieve the number of foods under each category


## Features
- Retrieve Filipino foods
- View the details of a specific food
- Retrieve food categories
- Retrieve ingredients
- Add new food (Protected)
- Retrieve foods by category
- Get the number of foods under each category
- Authenticate request using a token format
- Return information in JSON format


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
git clone https://github.com/cmc06-boop/filipino-cookbook-api-casilla.git
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
filipino_cookbook_api_surname
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

### 7. Start the PHP built-in server

Run the following command inside the project folder (terminal):

```bash
php -S 127.0.0.1:8000 -t public public/index.php
```

### 8. Run the API

Open:

```text
http://127.0.0.1:8000/
```


## Database Setup

### Database Name

Create a new database named: 
```text
filipino_cookbook_api_surname
```
### SQL File

Import the:
```text
filipino_cookbook_api.sql
```

### Main Tables

- foods
- categories
- origins
- ingredients
- food_ingredients

### Relationship

```
categories -> foods <- origins 
fooods -> food_ingredients <- ingredients
```

## Base URL
```text
http://127.0.0.1:8000/api/foods
```

## Authentication Instructions
- All endpoints under `/api` require Bearer token authentication.
- The API token is stored in the local `config.php` file.
- To access the secured endpoints, include the following request headers:

```http
Authorization: Bearer YOUR_ACCESS_TOKEN
```
- For POST requests, also include:
```http
Content-Type: application/json
```


### Secured Endpoints

- GET /api/foods
- GET /api/foods/{id}
- GET /api/foods/search/{name}
- GET /api/categories
- GET /api/categories/{id}/foods
- GET /api/categories/food-counts
- GET /api/ingredients
- POST /api/foods

The welcome route `GET /` is publicly accessible.

### Invalid Authentication Response

```json
{
    "status": "error",
    "message": "Unauthorized. Provide a valid Bearer token."
}
```

HTTP Status Code:

```text
401 Unauthorized
```


## Endpoint Documentation

### 1. Welcome Route

**Endpoint:**

```
GET /
```

**Description:** Public route that returns a welcome message. Does not require authentication.

**Example request:**

```
GET http://127.0.0.1:8000/
```

**Example successful response:**

```json
{
    "message": "Welcome to the Filipino Cookbook API",
    "note": "Use a valid bearer token in the Authorization header to access the secured endpoints."
}
```

### 2. Get All Foods

**Endpoint:**

```
GET /api/foods
```

**Description:** Returns all Filipino foods stored in the database, including each food's category, origin, and list of ingredients and instructions.

**Required headers:**

```
Authorization: Bearer YOUR_ACCESS_TOKEN
Accept: application/json
```

**Example request:**

```
GET http://127.0.0.1:8000/api/foods
```

**Example successful response:**

```json
{
    "data": [
        {
            "food_id": 1,
            "food_name": "Adobo",
            "instructions": "Marinate the chicken or pork in soy sauce, vinegar, garlic, and pepper. Simmer until tender.",
            "category_name": "Main Dish",
            "origin_name": "Philippines",
            "ingredients": [
                "Bay Leaves",
                "Chicken",
                "Garlic",
                "Soy Sauce",
                "Vinegar"
            ]
        }
    ]
}
```

**Example error response:**

```json
{
    "status": "error",
    "message": "Unauthorized. Provide a valid Bearer token."
}
```

### 3. Get a Single Food by ID

**Endpoint:**

```
GET /api/foods/{id}
```

**Description:** Returns a single food item, including its category, origin, ingredients,and instructions, based on its `food_id`.

**Required headers:**

```
Authorization: Bearer YOUR_ACCESS_TOKEN
Accept: application/json
```

**Example request:**

```
GET http://127.0.0.1:8000/api/foods/1
```

**Example successful response:**

```json
{
    "data": {
        "food_id": 1,
        "food_name": "Adobo",
        "instructions": "Marinate the chicken or pork in soy sauce, vinegar, garlic, and pepper. Simmer until tender.",
        "category_name": "Main Dish",
        "origin_name": "Philippines",
        "ingredients": [
            "Bay Leaves",
            "Chicken",
            "Garlic",
            "Soy Sauce",
            "Vinegar"
        ]
    }
}
```

**Example error response:**

```json
{
    "status": "error",
    "message": "Invalid food ID. A positive whole number is required."
}
```

_Returned with HTTP status `404` when the `food_id` does not exist, or `400` if `{id}` is not a positive whole number._

### 4. Search Foods by Name

**Endpoint:**

```
GET /api/foods/search/{name}
```

**Description:** Searches for foods whose name partially matches the given keyword (case-insensitive).

**Required headers:**

```
Authorization: Bearer YOUR_ACCESS_TOKEN
Accept: application/json
```

**Example request:**

```
GET http://127.0.0.1:8000/api/foods/search/adobo
```

**Example successful response:**

```json
{
    "data": [
        {
            "food_id": 1,
            "food_name": "Adobo",
            "category_name": "Main Dish",
            "origin_name": "Philippines",
            "instructions": "Marinate the chicken or pork in soy sauce, vinegar, garlic, and pepper. Simmer until tender.",
            "ingredients": [
                "Bay Leaves",
                "Chicken",
                "Garlic",
                "Soy Sauce",
                "Vinegar"
            ]
        }
    ]
}
```

**Example error response:**

```json
{
    "status": "error",
    "message": "Invalid search name. Enter 1 to 100 characters."
}
```

_Returned with HTTP status `400` when `{name}` is empty or longer than 100 characters._


### 5. Get All Categories

**Endpoint:**

```
GET /api/categories
```

**Description:** Returns all food categories available in the database.

**Required headers:**

```
Authorization: Bearer YOUR_ACCESS_TOKEN
Accept: application/json
```

**Example request:**

```
GET http://127.0.0.1:8000/api/categories
```

**Example successful response:**

```json
{
    "data": [
        {
            "category_id": 1,
            "category_name": "Main Dish"
        },
        {
            "category_id": 2,
            "category_name": "Dessert"
        }
    ]
}
```

**Example error response:**

```json
{
    "status": "error",
    "message": "Unauthorized. Provide a valid Bearer token."
}
```

---

### 6. Get Foods by Category

**Endpoint:**

```
GET /api/categories/{id}/foods
```

**Description:** Returns all foods that belong to a selected category, based on the `category_id`.

**Required headers:**

```
Authorization: Bearer YOUR_ACCESS_TOKEN
Accept: application/json
```

**Example request:**

```
GET http://127.0.0.1:8000/api/categories/1/foods
```

**Example successful response:**

```json
{
    "data": [
        {
            "food_id": 1,
            "food_name": "Adobo",
            "category_name": "Main Dish",
            "origin_name": "Philippines",
            "instructions": "Marinate the chicken or pork in soy sauce, vinegar, garlic, and pepper. Simmer until tender.",
            "ingredients": [
                "Bay Leaves",
                "Chicken",
                "Garlic",
                "Soy Sauce",
                "Vinegar"
            ]
        }
    ]
}
```

**Example error response:**

```json
{
    "status": "error",
    "message": "Invalid category ID. A positive whole number is required."
}
```

_Returned with HTTP status `404` when the `category_id` does not exist, or `400` if `{id}` is not a positive whole number._

---

### 7. Get Number of Foods per Category

**Endpoint:**

```
GET /api/categories/food-counts
```

**Description:** Returns every category along with the total number of foods under each one.

**Required headers:**

```
Authorization: Bearer YOUR_ACCESS_TOKEN
Accept: application/json
```

**Example request:**

```
GET http://127.0.0.1:8000/api/categories/food-counts
```

**Example successful response:**

```json
{
    "data": [
        {
            "category_id": 1,
            "category_name": "Main Dish",
            "food_count": 5
        },
        {
            "category_id": 2,
            "category_name": "Dessert",
            "food_count": 2
        }
    ]
}
```

**Example error response:**

```json
{
    "status": "error",
    "message": "Unauthorized. Provide a valid Bearer token."
}
```

---

### 8. Get All Ingredients

**Endpoint:**

```
GET /api/ingredients
```

**Description:** Returns all ingredients stored in the database.

**Required headers:**

```
Authorization: Bearer YOUR_ACCESS_TOKEN
Accept: application/json
```

**Example request:**

```
GET http://127.0.0.1:8000/api/ingredients
```

**Example successful response:**

```json
{
    "data": [
        {
            "ingredient_id": 1,
            "ingredient_name": "Chicken"
        },
        {
            "ingredient_id": 2,
            "ingredient_name": "Garlic"
        }
    ]
}
```

**Example error response:**

```json
{
    "status": "error",
    "message": "Unauthorized. Provide a valid Bearer token."
}
```

---

### 9. Add a New Food

**Endpoint:**

```
POST /api/foods
```

**Description:** Adds a new food item to the database along with its ingredients. Uses a database transaction so the food and its ingredients are saved together.

**Required headers:**
```
Authorization: Bearer YOUR_ACCESS_TOKEN
Content-Type: application/json
Accept: application/json
```

**Example request:**

```
POST http://127.0.0.1:8000/api/foods
```

**Example Required JSON body:**

```json
{
    "food_name": "Sinigang na Baboy",
    "category_id": 1,
    "origin_id": 1,
    "instructions": "Boil pork with tamarind broth, add vegetables, and simmer until tender.",
    "ingredient_ids": [1, 3, 5]
}
```

**Example successful response:**

```json
{
    "status": "success",
    "message": "Food added successfully."
}
```

_Returned with HTTP status `201 Created`._

**Example error response:**

```json
{
    "status": "error",
    "message": "Food name is required and must not exceed 150 characters."
}
```

_Returned with HTTP status `400` for validation errors (invalid/missing fields, non-existent category, origin, or ingredient IDs), or `500` if the food could not be saved due to a server/database error._



## HTTP Status Code

**Success**
- `200` – Request completed successfully
- `201` – Resource successfully created

**Client Errors**
- `400` – Invalid request or parameter
- `401` – Missing or invalid authentication
- `404` – Requested resource was not found

**Server Errors**
- `500` – Internal server error


## Testing Evidence

<p align="center"><em>Public Welcome Route — <code>GET /</code> returns a welcome message without requiring authentication.</em></p>

![Public Welcome Route](Screenshots/Public%20Welcome%20Route.png)

<p align="center"><em>Get All Foods — <code>GET /api/foods</code> returns all foods with their category, origin, ingredients, and instructions.</em></p>

![Get All Foods](Screenshots/Get%20All%20Foods.png)

<p align="center"><em>Get Food By ID — <code>GET /api/foods/{id}</code> returns a single food item and its details.</em></p>

![Get Food By ID](Screenshots/Get%20Food%20By%20ID.png)

<p align="center"><em>Search Food By Name — <code>GET /api/foods/search/{name}</code> returns foods matching the given keyword.</em></p>

![Search Food By Name](Screenshots/Search%20Food%20By%20Name.png)

<p align="center"><em>Get All Categories — <code>GET /api/categories</code> returns all available food categories.</em></p>

![Get All Categories](Screenshots/Get%20All%20Categories.png)

<p align="center"><em>Get Foods by Category — <code>GET /api/categories/{id}/foods</code> returns all foods under a selected category.</em></p>

![Foods By Category](Screenshots/Foods%20By%20Category.png)

<p align="center"><em>Get Number of Foods per Category — <code>GET /api/categories/food-counts</code> returns each category with its total food count.</em></p>

![Number of Foods Per Category](Screenshots/Number%20of%20Foods%20Per%20Category.png)

<p align="center"><em>Get All Ingredients — <code>GET /api/ingredients</code> returns all ingredients stored in the database.</em></p>

![Get All Ingredients](Screenshots/Get%20All%20Ingredients.png)

<p align="center"><em>Add New Food — <code>POST /api/foods</code> successfully adds a new food along with its ingredients.</em></p>

![Add New Food](Screenshots/Add%20New%20Food.png)

<p align="center"><em>Invalid Bearer Token — Request with a missing or incorrect token returns a <code>401 Unauthorized</code> response.</em></p>

![Invalid Bearer Token](Screenshots/Invalid%20Bearer%20Token.png)

<p align="center"><em>Input Validation Error — Request with invalid or missing fields returns a <code>400 Bad Request</code> response.</em></p>

![Input Validation Error](Screenshots/Input%20Validation%20Error.png)


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

 **Name:** Cherry Lyn M. Casilla

 **Course and Section:** BS Information Technology – [4B]

 **GitHub Username:** cmc06-boop

 **Repository:** https://github.com/cmc06-boop/filipino-cookbook-api-casilla

 **Date Completed:** July, 2026