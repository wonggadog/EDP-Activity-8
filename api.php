<?php
header("Content-Type: application/json");

$host = 'localhost';
$db = 'new_album';
$user = 'root';
$pass = 'wong';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $action = isset($_GET['action']) ? $_GET['action'] : null;  // Handle missing action

        if ($action === 'get_albums') {
            // Retrieve albums
            $stmt = $pdo->query("SELECT * FROM album");
            $albums = $stmt->fetchAll();
            echo json_encode($albums);
        } elseif ($action === 'get_publishers') {
            // Retrieve publishers
            $stmt = $pdo->query("SELECT * FROM publishers");
            $publishers = $stmt->fetchAll();
            echo json_encode($publishers);
        } else {
            // Default action (optional): If no action is specified, you can provide a default behavior here
            // For example, you could return a help message explaining valid actions.
            echo json_encode(['message' => 'Valid actions: get_albums, get_publishers']);
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = isset($_GET['action']) ? $_GET['action'] : null;

        if ($action === 'create_publisher') {
            $input = json_decode(file_get_contents('php://input'), true);

            $sql = "INSERT INTO publishers (publisher_name, email) VALUES (?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$input['publisher_name'], $input['email']]);
            echo json_encode(['message' => 'Publisher added successfully']);
        } elseif ($action === 'create_album') {
            $input = json_decode(file_get_contents('php://input'), true);

            $sql = "INSERT INTO album (album_id, title, price) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$input['album_id'], $input['title'], $input['price']]);
            echo json_encode(['message' => 'Album added successfully']);
        } else {
            echo json_encode(['error' => 'Invalid action parameter']);
        }
    }
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
