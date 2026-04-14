
<?php
$conn = new mysqli("localhost", "", "", "cinema_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>


<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
<title>Cinema Booking</title>
<style>
body { font-family: Arial; text-align:center; }
.grid { display:grid; grid-template-columns: repeat(10, 40px); gap:10px; justify-content:center; }
.seat {
    width:40px; height:40px;
    background:green;
    cursor:pointer;
}
.booked { background:red; cursor:not-allowed; }
.selected { background:yellow; }
</style>
</head>
<body>

<h2>Select Your Seat</h2>
<div class="grid" id="seatGrid"></div>

<button onclick="bookSeats()">Book Selected</button>

<script>
let selectedSeats = [];

function loadSeats() {
    fetch('getSeats.php')
    .then(res => res.json())
    .then(data => {
        let grid = document.getElementById('seatGrid');
        grid.innerHTML = '';
        data.forEach(seat => {
            let div = document.createElement('div');
            div.classList.add('seat');
            if (seat.is_booked == 1) div.classList.add('booked');
            div.innerText = seat.seat_number;

            div.onclick = () => {
                if (seat.is_booked == 1) return;
                div.classList.toggle('selected');
                if (selectedSeats.includes(seat.id)) {
                    selectedSeats = selectedSeats.filter(id => id !== seat.id);
                } else {
                    selectedSeats.push(seat.id);
                }
            };
            grid.appendChild(div);
        });
    });
}

function bookSeats() {
    fetch('book.php', {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify({seats:selectedSeats})
    })
    .then(res=>res.text())
    .then(msg=>{
        alert(msg);
        selectedSeats = [];
        loadSeats();
    });
}

setInterval(loadSeats, 2000); 
loadSeats();
</script>

</body>
</html>
