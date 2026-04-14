<?php
include 'db.php';
$data = json_decode(file_get_contents("php://input"), true);

foreach ($data['seats'] as $seat_id) {
    $check = $conn->query("SELECT is_booked FROM seats WHERE id=$seat_id");
    $row = $check->fetch_assoc();

    if ($row['is_booked'] == 1) {
        echo "Seat already booked!";
        exit;
    }

    $conn->query("UPDATE seats SET is_booked=1 WHERE id=$seat_id");
    $conn->query("INSERT INTO bookings (seat_id, user_name) VALUES ($seat_id, 'Guest')");
}

echo "Booking successful!";
?>