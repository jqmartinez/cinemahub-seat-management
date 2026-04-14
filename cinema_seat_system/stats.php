<?php
require_once __DIR__ . '/../includes/db.php';

$pdo = getDB();

$today = date('Y-m-d');

$totalBookings  = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
$todayBookings  = $pdo->query("SELECT COUNT(*) FROM bookings WHERE DATE(booked_at)='$today'")->fetchColumn();
$totalRevenue   = $pdo->query("SELECT COALESCE(SUM(total_amount),0) FROM bookings")->fetchColumn();
$todayRevenue   = $pdo->query("SELECT COALESCE(SUM(total_amount),0) FROM bookings WHERE DATE(booked_at)='$today'")->fetchColumn();
$totalSeats     = $pdo->query("SELECT COUNT(*) FROM seats")->fetchColumn();
$bookedSeats    = $pdo->query("SELECT COUNT(*) FROM seats WHERE status='booked'")->fetchColumn();
$availableSeats = $totalSeats - $bookedSeats;

// Revenue by movie
$byMovie = $pdo->query("
    SELECT m.title, COUNT(b.id) AS bookings, COALESCE(SUM(b.total_amount),0) AS revenue
    FROM movies m
    LEFT JOIN showtimes s  ON s.movie_id = m.id
    LEFT JOIN bookings b   ON b.showtime_id = s.id
    GROUP BY m.id
    ORDER BY revenue DESC
")->fetchAll();

// Recent bookings
$recent = $pdo->query("
    SELECT b.id, b.guest_name, b.total_amount, b.booked_at,
           m.title AS movie_title, s.show_time
    FROM bookings b
    JOIN showtimes s ON s.id = b.showtime_id
    JOIN movies m    ON m.id = s.movie_id
    ORDER BY b.booked_at DESC
    LIMIT 10
")->fetchAll();

jsonResponse([
    'total_bookings'  => (int)$totalBookings,
    'today_bookings'  => (int)$todayBookings,
    'total_revenue'   => (float)$totalRevenue,
    'today_revenue'   => (float)$todayRevenue,
    'total_seats'     => (int)$totalSeats,
    'booked_seats'    => (int)$bookedSeats,
    'available_seats' => (int)$availableSeats,
    'by_movie'        => $byMovie,
    'recent_bookings' => $recent,
]);
