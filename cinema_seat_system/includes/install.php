<?php
require_once __DIR__ . '/db.php';

function installDatabase() {
    $pdo = getDB();

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS movies (
            id           INT AUTO_INCREMENT PRIMARY KEY,
            title        VARCHAR(200) NOT NULL,
            genre        VARCHAR(100),
            duration_min INT,
            rating       VARCHAR(20),
            poster_color VARCHAR(20) DEFAULT '#1a1a2e',
            created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;

        CREATE TABLE IF NOT EXISTS showtimes (
            id         INT AUTO_INCREMENT PRIMARY KEY,
            movie_id   INT NOT NULL,
            show_date  DATE NOT NULL,
            show_time  VARCHAR(20) NOT NULL,
            hall       VARCHAR(50) NOT NULL DEFAULT 'Hall 1',
            FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;

        CREATE TABLE IF NOT EXISTS seats (
            id          INT AUTO_INCREMENT PRIMARY KEY,
            showtime_id INT NOT NULL,
            row_label   CHAR(1) NOT NULL,
            col_num     TINYINT NOT NULL,
            seat_type   ENUM('standard','vip') NOT NULL DEFAULT 'standard',
            status      ENUM('available','booked') NOT NULL DEFAULT 'available',
            UNIQUE KEY uq_seat (showtime_id, row_label, col_num),
            FOREIGN KEY (showtime_id) REFERENCES showtimes(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;

        CREATE TABLE IF NOT EXISTS bookings (
            id           INT AUTO_INCREMENT PRIMARY KEY,
            showtime_id  INT NOT NULL,
            guest_name   VARCHAR(150) NOT NULL,
            guest_phone  VARCHAR(30),
            guest_email  VARCHAR(150),
            total_amount DECIMAL(10,2) NOT NULL,
            booked_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (showtime_id) REFERENCES showtimes(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;

        CREATE TABLE IF NOT EXISTS booking_seats (
            booking_id INT NOT NULL,
            seat_id    INT NOT NULL,
            price      DECIMAL(10,2) NOT NULL,
            PRIMARY KEY (booking_id, seat_id),
            FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
            FOREIGN KEY (seat_id)    REFERENCES seats(id)    ON DELETE CASCADE
        ) ENGINE=InnoDB;
    ");

    // Seed movies only if empty
    $count = $pdo->query("SELECT COUNT(*) FROM movies")->fetchColumn();
    if ($count == 0) {
        $pdo->exec("
            INSERT INTO movies (title, genre, duration_min, rating, poster_color) VALUES
            ('Dune: Messiah',  'Sci-Fi',  165, 'PG-13', '#2c1654'),
            ('Starfall',       'Action',  132, 'PG-13', '#1a3a5c'),
            ('The Last Orbit', 'Drama',   118, 'R',     '#1a2e1a'),
            ('Neon Requiem',   'Thriller',140, 'R',     '#3a1a1a'),
            ('Echo Protocol',  'Sci-Fi',  155, 'PG-13', '#1a2e3a');
        ");

        // Seed showtimes: today + next 6 days, 5 times/day per movie
        $movies = $pdo->query("SELECT id FROM movies")->fetchAll(PDO::FETCH_COLUMN);
        $times  = ['10:00 AM','1:30 PM','4:00 PM','7:30 PM','10:00 PM'];
        $halls  = ['Hall 1','Hall 2','Hall 1','Hall 2','Hall 3'];

        $stmt = $pdo->prepare("INSERT INTO showtimes (movie_id, show_date, show_time, hall) VALUES (?,?,?,?)");
        for ($d = 0; $d < 7; $d++) {
            $date = date('Y-m-d', strtotime("+$d days"));
            foreach ($movies as $mid) {
                foreach ($times as $i => $t) {
                    $stmt->execute([$mid, $date, $t, $halls[$i]]);
                }
            }
        }

        // Seed seats for every showtime
        $showIds = $pdo->query("SELECT id FROM showtimes")->fetchAll(PDO::FETCH_COLUMN);
        $vipRows = ['D','E'];
        $allRows = ['A','B','C','D','E','F','G','H'];

        $seatStmt = $pdo->prepare("INSERT IGNORE INTO seats (showtime_id, row_label, col_num, seat_type) VALUES (?,?,?,?)");
        foreach ($showIds as $sid) {
            foreach ($allRows as $row) {
                $type = in_array($row, $vipRows) ? 'vip' : 'standard';
                for ($col = 0; $col < 10; $col++) {
                    $seatStmt->execute([$sid, $row, $col, $type]);
                }
            }
        }
    }

    return true;
}
