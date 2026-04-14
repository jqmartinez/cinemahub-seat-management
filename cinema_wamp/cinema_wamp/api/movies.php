<?php
require_once __DIR__ . '/../includes/db.php';

$pdo = getDB();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $movies = $pdo->query("SELECT * FROM movies ORDER BY title")->fetchAll();
    jsonResponse($movies);

} elseif ($method === 'POST') {
    $data  = json_decode(file_get_contents('php://input'), true);
    $title = trim($data['title'] ?? '');
    $genre = trim($data['genre'] ?? '');
    $dur   = intval($data['duration_min'] ?? 0);
    $rat   = trim($data['rating'] ?? '');
    $color = trim($data['poster_color'] ?? '#1a1a2e');

    if (!$title) jsonError('title is required');

    $stmt = $pdo->prepare("INSERT INTO movies (title,genre,duration_min,rating,poster_color) VALUES (?,?,?,?,?)");
    $stmt->execute([$title, $genre, $dur, $rat, $color]);
    $id = $pdo->lastInsertId();
    $movie = $pdo->query("SELECT * FROM movies WHERE id=$id")->fetch();
    jsonResponse($movie, 201);

} elseif ($method === 'DELETE') {
    $id = intval($_GET['id'] ?? 0);
    if (!$id) jsonError('id required');
    $pdo->prepare("DELETE FROM movies WHERE id=?")->execute([$id]);
    jsonResponse(['success' => true]);

} else {
    jsonError('Method not allowed', 405);
}
