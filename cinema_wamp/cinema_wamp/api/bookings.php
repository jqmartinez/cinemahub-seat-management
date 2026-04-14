<?php
require_once __DIR__ . '/../includes/db.php';

$pdo    = getDB();
$method = $_SERVER['REQUEST_METHOD'];

$PRICES = ['standard' => 350, 'vip' => 550];

if ($method === 'GET') {
    $showtimeId = intval($_GET['showtime_id'] ?? 0);
    $search     = $_GET['search'] ?? '';

    $sql  = "SELECT b.id, b.guest_name, b.guest_phone, b.guest_email,
                    b.total_amount, b.booked_at,
                    m.title AS movie_title,
                    s.show_date, s.show_time, s.hall,
                    GROUP_CONCAT(CONCAT(se.row_label, se.col_num+1) ORDER BY se.row_label, se.col_num SEPARATOR ', ') AS seats,
                    COUNT(se.id) AS seat_count
             FROM bookings b
             JOIN showtimes s  ON s.id  = b.showtime_id
             JOIN movies m     ON m.id  = s.movie_id
             JOIN booking_seats bs ON bs.booking_id = b.id
             JOIN seats se     ON se.id = bs.seat_id
             WHERE 1=1";
    $args = [];

    if ($showtimeId) { $sql .= " AND b.showtime_id = ?"; $args[] = $showtimeId; }
    if ($search)     { $sql .= " AND (b.guest_name LIKE ? OR b.guest_phone LIKE ? OR b.guest_email LIKE ?)";
                       $args = array_merge($args, ["%$search%", "%$search%", "%$search%"]); }

    $sql .= " GROUP BY b.id ORDER BY b.booked_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($args);
    jsonResponse($stmt->fetchAll());

} elseif ($method === 'POST') {
    $data       = json_decode(file_get_contents('php://input'), true);
    $showtimeId = intval($data['showtime_id'] ?? 0);
    $seatIds    = $data['seat_ids'] ?? [];
    $guestName  = trim($data['guest_name'] ?? '');
    $guestPhone = trim($data['guest_phone'] ?? '');
    $guestEmail = trim($data['guest_email'] ?? '');

    if (!$showtimeId || empty($seatIds) || !$guestName)
        jsonError('showtime_id, seat_ids[], and guest_name are required');

    $placeholders = implode(',', array_fill(0, count($seatIds), '?'));

    // Lock and verify seats
    $seats = $pdo->prepare("
        SELECT id, seat_type, status FROM seats
        WHERE id IN ($placeholders) AND showtime_id = ?
        FOR UPDATE
    ");
    $seats->execute(array_merge($seatIds, [$showtimeId]));
    $seats = $seats->fetchAll();

    if (count($seats) !== count($seatIds))
        jsonError('One or more seat IDs are invalid for this showtime');

    foreach ($seats as $s) {
        if ($s['status'] !== 'available')
            jsonError("Seat is already booked");
    }

    global $PRICES;
    $total = array_sum(array_map(fn($s) => $PRICES[$s['seat_type']] ?? 350, $seats));

    $pdo->beginTransaction();
    try {
        $bStmt = $pdo->prepare("INSERT INTO bookings (showtime_id,guest_name,guest_phone,guest_email,total_amount) VALUES (?,?,?,?,?)");
        $bStmt->execute([$showtimeId, $guestName, $guestPhone, $guestEmail, $total]);
        $bookingId = $pdo->lastInsertId();

        $bsStmt   = $pdo->prepare("INSERT INTO booking_seats (booking_id,seat_id,price) VALUES (?,?,?)");
        $upStmt   = $pdo->prepare("UPDATE seats SET status='booked' WHERE id=?");
        foreach ($seats as $s) {
            $price = $PRICES[$s['seat_type']] ?? 350;
            $bsStmt->execute([$bookingId, $s['id'], $price]);
            $upStmt->execute([$s['id']]);
        }

        $pdo->commit();

        $booking = $pdo->query("
            SELECT b.*, m.title AS movie_title, s.show_date, s.show_time, s.hall
            FROM bookings b
            JOIN showtimes s ON s.id = b.showtime_id
            JOIN movies m    ON m.id = s.movie_id
            WHERE b.id = $bookingId
        ")->fetch();

        jsonResponse(['success' => true, 'booking' => $booking], 201);

    } catch (Exception $e) {
        $pdo->rollBack();
        jsonError('Booking failed: ' . $e->getMessage(), 500);
    }

} elseif ($method === 'DELETE') {
    $id = intval($_GET['id'] ?? 0);
    if (!$id) jsonError('id required');

    // Free up the seats first
    $seatIds = $pdo->prepare("SELECT seat_id FROM booking_seats WHERE booking_id=?");
    $seatIds->execute([$id]);
    $ids = $seatIds->fetchAll(PDO::FETCH_COLUMN);

    $pdo->beginTransaction();
    try {
        if ($ids) {
            $ph = implode(',', array_fill(0, count($ids), '?'));
            $pdo->prepare("UPDATE seats SET status='available' WHERE id IN ($ph)")->execute($ids);
        }
        $pdo->prepare("DELETE FROM bookings WHERE id=?")->execute([$id]);
        $pdo->commit();
        jsonResponse(['success' => true]);
    } catch (Exception $e) {
        $pdo->rollBack();
        jsonError('Cancel failed: ' . $e->getMessage(), 500);
    }

} else {
    jsonError('Method not allowed', 405);
}
