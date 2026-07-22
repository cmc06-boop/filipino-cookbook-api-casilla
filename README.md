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
- Input Validation
 - Proper Error Handling for invalid or missing parameters.

## Instructions for Testing the Enhancement

### Get Foods by Category
1. Send a GET request to `/api/categories/{id}/foods`.
2. Replace `{id}` with a valid category ID.
3. Verify that the API returns all foods under the selected category.

### Get Number of Foods Under Each Category
1. Send a GET request to `/api/categories/count`.
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