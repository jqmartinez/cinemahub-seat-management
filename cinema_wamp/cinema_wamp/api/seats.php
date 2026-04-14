<?php
require_once __DIR__ . '/../includes/db.php';

$pdo    = getDB();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $showtimeId = intval($_GET['showtime_id'] ?? 0);
    if (!$showtimeId) jsonError('showtime_id required');

    $seats = $pdo->prepare("
        SELECT s.id, s.row_label, s.col_num, s.seat_type, s.status,
               b.guest_name, b.booked_at,
               CASE s.seat_type WHEN 'vip' THEN 550 ELSE 350 END AS price
        FROM seats s
        LEFT JOIN booking_seats bs ON bs.seat_id = s.id
        LEFT JOIN bookings b       ON b.id = bs.booking_id
        WHERE s.showtime_id = ?
        ORDER BY s.row_label, s.col_num
    ");
    $seats->execute([$showtimeId]);
    jsonResponse($seats->fetchAll());

} else {
    jsonError('Method not allowed', 405);
}
