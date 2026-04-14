<?php
include 'db.php';
$result = $conn->query("SELECT * FROM seats");
$seats = [];
while($row = $result->fetch_assoc()) {
    $seats[] = $row;
}
echo json_encode($seats);
?>