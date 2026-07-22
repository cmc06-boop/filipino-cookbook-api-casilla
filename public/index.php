<?php

//naglo-load ng Slim Framework at ib apang pang packages na in-install gamit yung composer.
require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory; // para makagawa ng Slim application at responses para makapag send ng http response
use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;

// dito ginawa yung Slim application
$app = AppFactory::create();

// Para gumana ang routes sa local server (127.0.0.1) at sa XAMPP subdirectory para na hindi kailangan ng .htaccess
$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = strpos($requestPath, $scriptName) === 0
    ? $scriptName
    : rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
$app->setBasePath($basePath);

//Mga "filter" bago magprocess yung request
$app->addBodyParsingMiddleware();//ginagamit para mabasa ang JSON request body lalo na sa POST Requests.
$app->addRoutingMiddleware();//naghahanap kung anong end point ang tatawagin
$app->addErrorMiddleware(false, true, true); // hindi ipinapakita ang detalyadong server error sa client.

// DATABASE CONNECTION (PDO) PHP Data Objects
// database credentials na ginagamit para makonect yung API sa MYsql na database.
$host = 'localhost';
$dbname = 'filipino_cookbook_api';
$username = 'root';
$password = '';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $username,
        $password
    );
    // I-throw ang exception kapag may SQL error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Ibalik ang result bilang associative array (column name => value)
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Database connection failed.');
}
// Gumawa din ng Helper function para hindi pauli-ulit ang paggawa ng JSON response.
// Reusable function para lahat ng endpoint ay magbalik ng JSON format
$jsonResponse = function ($response, array $data, int $status = 200) {
    $response->getBody()->write(json_encode($data));

    return $response
        ->withStatus($status)
        ->withHeader('Content-Type', 'application/json');
};

// 1: PUBLIC WELCOME ROUTE (GET /)
$app->get('/', function ($request, $response) use ($jsonResponse) {
    return $jsonResponse($response, [
        'message' => 'Welcome to the Filipino Cookbook API',
        'note' => 'Use the public /api endpoints to browse foods and categories.',
    ]);
});
// helper function para kunin yung lahat ng ingredients ng isang pagkain.
$getFoodIngredients = function (int $foodId) use ($pdo): array {
    $stmt = $pdo->prepare(
        'SELECT i.ingredient_name
         FROM food_ingredients fi
         INNER JOIN ingredients i ON fi.ingredient_id = i.ingredient_id
         WHERE fi.food_id = :food_id
         ORDER BY i.ingredient_name'
    );
    $stmt->execute(['food_id' => $foodId]);

    return $stmt->fetchAll(PDO::FETCH_COLUMN);
};

//Naglalagay ng ingredients sa bawat food para hindi na kailangang ulit-ulitin ang parehong code.
$attachIngredients = function (array $foods) use ($getFoodIngredients): array {
    foreach ($foods as &$food) {
        $food['ingredients'] = $getFoodIngredients((int) $food['food_id']);
    }
    unset($food);

    return $foods;
};

// Ibinabalik ang kasalukuyang PDO database connection.
// Ginagamit ito para hindi na direktang ulit-ulitin ang database connection sa endpoint.
function getPdo(): PDO
{
    global $pdo;
    return $pdo;
}

// Gumagawa ng JSON response gamit ang ibinigay na HTTP status code at data.
// Una, kino-convert nito ang PHP array sa JSON bago ito ipadala sa client.
function jsonResponse(Response $response, int $status, array $data): Response
{
    $response->getBody()->write(json_encode($data));

    return $response
        ->withStatus($status)
        ->withHeader('Content-Type', 'application/json');
}

// Kinukuha ang lahat ng foods na kabilang sa isang category.
// Prepared statement ang ginagamit para ligtas na maipasa ang category ID sa SQL query.
function fetchFoodsByCategory(PDO $pdo, int $categoryId): array
{
    $stmt = $pdo->prepare(
        'SELECT f.food_id, f.food_name, f.instructions,
                c.category_name, o.origin_name
         FROM foods f
         INNER JOIN categories c ON f.category_id = c.category_id
         INNER JOIN origins o ON f.origin_id = o.origin_id
         WHERE f.category_id = ?
         ORDER BY f.food_name'
    );
    $stmt->execute([$categoryId]);
    $foods = $stmt->fetchAll();

    // Loop para idagdag ang ingredients sa bawat food na nakuha sa category.
    foreach ($foods as &$food) {
        $ingredientStmt = $pdo->prepare(
            'SELECT i.ingredient_name
             FROM food_ingredients fi
             INNER JOIN ingredients i ON fi.ingredient_id = i.ingredient_id
             WHERE fi.food_id = ?
             ORDER BY i.ingredient_name'
        );
        $ingredientStmt->execute([(int) $food['food_id']]);
        $food['ingredients'] = $ingredientStmt->fetchAll(PDO::FETCH_COLUMN);
    }
    unset($food);

    return $foods;
}

// 9: GET /api/categories/{id}/foods
// Get all foods under a specific category.
$app->get('/api/categories/{id}/foods', function (Request $request, Response $response, array $args): Response {
    // Kinukuha ang category ID mula sa URL at kino-convert ito sa integer.
    $categoryId = isset($args['id']) ? (int) $args['id'] : 0;

    // INPUT VALIDATION: Dapat positive number ang category ID. | Kapag zero, negative, o invalid ang value, magbabalik ng 400 Bad Request.
    if ($categoryId <= 0) {
        return jsonResponse($response, 400, [
            'status' => 'error',
            'message' => 'Invalid category_id.'
        ]);
    }

    // Kinukuha ang PDO connection gamit ang helper function.
    $pdo = getPdo();

    // Chine-check muna kung talagang umiiral ang category sa database.
    // Prepared statement ang ginagamit para makatulong laban sa SQL injection.
    $categoryStmt = $pdo->prepare('SELECT category_id FROM categories WHERE category_id = ?');
    $categoryStmt->execute([$categoryId]);

    // Kapag walang category na nakita, magbabalik ng 404 Not Found.
    if (!$categoryStmt->fetch()) {
        return jsonResponse($response, 404, [
            'status' => 'error',
            'message' => 'Category not found.'
        ]);
    }

    // Kapag valid at existing ang category, kinukuha at ibinabalik ang lahat ng foods nito.
    return jsonResponse($response, 200, [
        'data' => fetchFoodsByCategory($pdo, $categoryId)
    ]);
});

// Lahat ng secured endpoints ket piangsamasama na sa iisang group para isang beses lang ilagay yung authentication middleware.
$app->group('/api', function ($group) use ($pdo, $jsonResponse, $getFoodIngredients, $attachIngredients) {
    // 5: GET /api/categories: Kinukuha lahat ng categories mula sa database
    $group->get('/categories', function ($request, $response) use ($pdo, $jsonResponse) {
        $stmt = $pdo->query('SELECT category_id, category_name FROM categories ORDER BY category_name');
        $categories = $stmt->fetchAll();

        return $jsonResponse($response, [
            'data' => $categories,
        ]);
    });
    // 8: GET /api/categories/food-counts: Kinukuha ang bilang ng foods sa bawat category
    // LEFT JOIN ang ginagamit para maisama kahit ang category na wala pang food.
    // COUNT(f.food_id) ang nagbibilang kung ilang foods ang nakaugnay sa bawat category.
    $group->get('/categories/food-counts', function ($request, $response) use ($pdo, $jsonResponse) {
        $sql = 'SELECT c.category_id, c.category_name,
                       COUNT(f.food_id) AS food_count
                FROM categories c
                LEFT JOIN foods f ON f.category_id = c.category_id
                GROUP BY c.category_id, c.category_name
                ORDER BY c.category_name';

        // Isinasagawa ang query at kinukuha ang lahat ng category count results.
        $stmt = $pdo->query($sql);
        $categoryCounts = $stmt->fetchAll();

        return $jsonResponse($response, [
            'data' => $categoryCounts,
        ]);
    });

    //6: GET /api/ingredients :Kikukuha lahat ng ingredients mula sa database
    $group->get('/ingredients', function ($request, $response) use ($pdo, $jsonResponse) {
        $stmt = $pdo->query('SELECT ingredient_id, ingredient_name FROM ingredients ORDER BY ingredient_name');
        $ingredients = $stmt->fetchAll();

        return $jsonResponse($response, [
            'data' => $ingredients,
        ]);
    });
    //4: GET /api/foods/search/{name}: Maghanap ng food gamit ang pangalan
    // Gumagamit ng partial match at case-insensitive search.
    // Pagkatapos ng query, kinukuha ang ingredients ng bawat food gamit ang loop.
    // Dapat naka-place BAGO ang /foods/{id} para hindi ma-confuse ng router.
    $group->get('/foods/search/{name}', function ($request, $response, $args) use ($pdo, $jsonResponse, $getFoodIngredients) {
        $searchName = trim($args['name'] ?? '');

        if ($searchName === '' || mb_strlen($searchName) > 100) {
            return $jsonResponse($response, [
                'status' => 'error',
                'message' => 'Invalid search name. Enter 1 to 100 characters.',
            ], 400);
        }

        $searchTerm = '%' . $searchName . '%';

        $sql = 'SELECT f.food_id, f.food_name, c.category_name,
                       o.origin_name, f.instructions
                FROM foods f
                INNER JOIN categories c ON c.category_id = f.category_id
                INNER JOIN origins o ON o.origin_id = f.origin_id
                WHERE LOWER(f.food_name) LIKE LOWER(:search)
                ORDER BY f.food_name';

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'search' => $searchTerm,
        ]);

        $results = [];

        foreach ($stmt->fetchAll() as $row) {
            $row['ingredients'] = $getFoodIngredients((int) $row['food_id']);
            $results[] = $row;
        }

        return $jsonResponse($response, [
            'data' => $results,
        ]);
    });
    // 3: GET /api/foods/{id}:Kinukuha yung isang food gamit ang food_id, kasama ang ingredients | Kapag wala sa database, ibabalik ang 404 Not Found.
    $group->get('/foods/{id}', function ($request, $response, $args) use ($pdo, $jsonResponse, $getFoodIngredients) {
        $foodId = filter_var($args['id'] ?? null, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
        ]);

        if ($foodId === false) {
            return $jsonResponse($response, [
                'status' => 'error',
                'message' => 'Invalid food ID. A positive whole number is required.',
            ], 400);
        }

        $sql = 'SELECT f.food_id, f.food_name, f.instructions,
                       c.category_name, o.origin_name
                FROM foods f
                INNER JOIN categories c ON f.category_id = c.category_id
                INNER JOIN origins o ON f.origin_id = o.origin_id
                WHERE f.food_id = :food_id';

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['food_id' => $foodId]);
        $food = $stmt->fetch();

        if (!$food) {
            return $jsonResponse($response, [
                'status' => 'error',
                'message' => 'Food not found',
            ], 404);
        }

        $food['ingredients'] = $getFoodIngredients($foodId);

        return $jsonResponse($response, [
            'data' => $food,
        ]);
    });
    //2: GET /api/foods: Kinukuha lahat ng foods kasama ang category, origin, at ingredients
    // Gumagamit ng INNER JOIN para makuha ang category_name at origin_name
    $group->get('/foods', function ($request, $response) use ($pdo, $jsonResponse, $attachIngredients) {
        $sql = 'SELECT f.food_id, f.food_name, f.instructions,
                       c.category_name, o.origin_name
                FROM foods f
                INNER JOIN categories c ON f.category_id = c.category_id
                INNER JOIN origins o ON f.origin_id = o.origin_id
                ORDER BY f.food_id';

        $stmt = $pdo->query($sql);
        $foods = $attachIngredients($stmt->fetchAll());

        return $jsonResponse($response, [
            'data' => $foods,
        ]);
    });
    //7: POST /api/foods: Magdagdag ng bagong food sa database
    // Required JSON body:
    // - food_name      (string)
    // - category_id    (number)
    // - origin_id      (number)
    // - instructions   (string)
    // - ingredient_ids (array of numbers)
    // Gumagamit ng database transaction para siguradong sabay na ma-save ang food at ang kanyang ingredients
    $group->post('/foods', function ($request, $response) use ($pdo, $jsonResponse) {
        $body = $request->getParsedBody();

        // INPUT VALIDATION: Siguraduhing valid JSON object ang request body
        if (!is_array($body)) {
            return $jsonResponse($response, [
                'status' => 'error',
                'message' => 'Invalid request body. Send valid JSON data.',
            ], 400);
        }

        $foodName = trim($body['food_name'] ?? '');
        $categoryId = filter_var($body['category_id'] ?? null, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
        ]);
        $originId = filter_var($body['origin_id'] ?? null, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
        ]);
        $instructions = trim($body['instructions'] ?? '');
        $ingredientIds = $body['ingredient_ids'] ?? [];

        // INPUT VALIDATION: I-check ang required text fields at haba ng values
        if ($foodName === '' || mb_strlen($foodName) > 150) {
            return $jsonResponse($response, [
                'status' => 'error',
                'message' => 'Food name is required and must not exceed 150 characters.',
            ], 400);
        }

        if ($categoryId === false) {
            return $jsonResponse($response, [
                'status' => 'error',
                'message' => 'Category ID must be a positive whole number.',
            ], 400);
        }

        if ($originId === false) {
            return $jsonResponse($response, [
                'status' => 'error',
                'message' => 'Origin ID must be a positive whole number.',
            ], 400);
        }

        if ($instructions === '') {
            return $jsonResponse($response, [
                'status' => 'error',
                'message' => 'Instructions are required.',
            ], 400);
        }

        if (!is_array($ingredientIds) || count($ingredientIds) === 0) {
            return $jsonResponse($response, [
                'status' => 'error',
                'message' => 'At least one ingredient ID is required.',
            ], 400);
        }

        foreach ($ingredientIds as $ingredientId) {
            $validIngredientId = filter_var($ingredientId, FILTER_VALIDATE_INT, [
                'options' => ['min_range' => 1],
            ]);

            if ($validIngredientId === false) {
                return $jsonResponse($response, [
                    'status' => 'error',
                    'message' => 'Every ingredient ID must be a positive whole number.',
                ], 400);
            }
        }

        // INPUT VALIDATION: Siguraduhing existing ang category at origin
        $categoryCheck = $pdo->prepare('SELECT category_id FROM categories WHERE category_id = :category_id');
        $categoryCheck->execute(['category_id' => $categoryId]);

        if (!$categoryCheck->fetch()) {
            return $jsonResponse($response, [
                'status' => 'error',
                'message' => 'Category not found.',
            ], 400);
        }

        $originCheck = $pdo->prepare('SELECT origin_id FROM origins WHERE origin_id = :origin_id');
        $originCheck->execute(['origin_id' => $originId]);

        if (!$originCheck->fetch()) {
            return $jsonResponse($response, [
                'status' => 'error',
                'message' => 'Origin not found.',
            ], 400);
        }

        $ingredientCheck = $pdo->prepare('SELECT ingredient_id FROM ingredients WHERE ingredient_id = :ingredient_id');

        foreach ($ingredientIds as $ingredientId) {
            $ingredientCheck->execute(['ingredient_id' => (int) $ingredientId]);

            if (!$ingredientCheck->fetch()) {
                return $jsonResponse($response, [
                    'status' => 'error',
                    'message' => 'One or more ingredient IDs were not found.',
                ], 400);
            }
        }

        try {
            $pdo->beginTransaction();

            // Auto-generate ang next food_id (MAX + 1)
            $idStmt = $pdo->query('SELECT COALESCE(MAX(food_id), 0) + 1 AS next_id FROM foods');
            $foodId = (int) $idStmt->fetchColumn();

            // I-insert ang bagong food sa foods table
            $insertFood = $pdo->prepare(
                'INSERT INTO foods (food_id, food_name, category_id, origin_id, instructions)
                 VALUES (:food_id, :food_name, :category_id, :origin_id, :instructions)'
            );
            $insertFood->execute([
                'food_id' => $foodId,
                'food_name' => $foodName,
                'category_id' => $categoryId,
                'origin_id' => $originId,
                'instructions' => $instructions,
            ]);

            // I-insert ang bawat ingredient sa food_ingredients junction table
            $insertIngredient = $pdo->prepare(
                'INSERT INTO food_ingredients (food_id, ingredient_id) VALUES (:food_id, :ingredient_id)'
            );

            foreach ($ingredientIds as $ingredientId) {
                $insertIngredient->execute([
                    'food_id' => $foodId,
                    'ingredient_id' => (int) $ingredientId,
                ]);
            }

            $pdo->commit();

            return $jsonResponse($response, [
                'status' => 'success',
                'message' => 'Food added successfully.',
            ], 201);
        } catch (PDOException $e) {
            // Kapag may error, i-rollback para hindi ma-save ang partial data
            $pdo->rollBack();

            return $jsonResponse($response, [
                'status' => 'error',
                'message' => 'Failed to add food.',
            ], 500);
        }
    });

});

$app->run();