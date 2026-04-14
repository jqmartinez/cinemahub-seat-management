<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/install.php';

$pdo    = getDB();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $movieId = intval($_GET['movie_id'] ?? 0);
    $date    = $_GET['date'] ?? date('Y-m-d');

    $sql  = "SELECT s.id, s.show_date, s.show_time, s.hall,
                    m.id AS movie_id, m.title AS movie_title, m.genre, m.duration_min, m.rating, m.poster_color,
                    COUNT(CASE WHEN se.status='available' THEN 1 END) AS available_seats,
                    COUNT(CASE WHEN se.status='booked'   THEN 1 END) AS booked_seats,
                    COUNT(se.id) AS total_seats
             FROM showtimes s
             JOIN movies m  ON m.id = s.movie_id
             LEFT JOIN seats se ON se.showtime_id = s.id
             WHERE s.show_date = ?";
    $args = [$date];

    if ($movieId) {
        $sql  .= " AND s.movie_id = ?";
        $args[] = $movieId;
    }

    $sql .= " GROUP BY s.id ORDER BY m.title, s.show_time";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($args);
    jsonResponse($stmt->fetchAll());

} elseif ($method === 'POST') {
    $data      = json_decode(file_get_contents('php://input'), true);
    $movieId   = intval($data['movie_id'] ?? 0);
    $showDate  = $data['show_date'] ?? '';
    $showTime  = $data['show_time'] ?? '';
    $hall      = $data['hall'] ?? 'Hall 1';

    if (!$movieId || !$showDate || !$showTime) jsonError('movie_id, show_date, show_time required');

    $stmt = $pdo->prepare("INSERT INTO showtimes (movie_id,show_date,show_time,hall) VALUES (?,?,?,?)");
    $stmt->execute([$movieId, $showDate, $showTime, $hall]);
    $sid = $pdo->lastInsertId();

    // Auto-create seats
    $vipRows = ['D','E'];
    $allRows = ['A','B','C','D','E','F','G','H'];
    $seatStmt = $pdo->prepare("INSERT IGNORE INTO seats (showtime_id,row_label,col_num,seat_type) VALUES (?,?,?,?)");
    foreach ($allRows as $row) {
        $type = in_array($row, $vipRows) ? 'vip' : 'standard';
        for ($col = 0; $col < 10; $col++) {
            $seatStmt->execute([$sid, $row, $col, $type]);
        }
    }

    $show = $pdo->query("SELECT s.*, m.title as movie_title FROM showtimes s JOIN movies m ON m.id=s.movie_id WHERE s.id=$sid")->fetch();
    jsonResponse($show, 201);

} elseif ($method === 'DELETE') {
    $id = intval($_GET['id'] ?? 0);
    if (!$id) jsonError('id required');
    $pdo->prepare("DELETE FROM showtimes WHERE id=?")->execute([$id]);
    jsonResponse(['success' => true]);

} else {
    jsonError('Method not allowed', 405);
}
