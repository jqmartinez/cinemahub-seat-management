# CinemaHub — Cinema Seat Management System
## Setup on WAMP Server

### Requirements
- WampServer 3.x (Apache + PHP 7.4+ + MySQL 5.7+)

---

### Installation Steps

1. **Copy the folder**
   - Copy the entire `cinema_wamp` folder into your WAMP `www` directory:
   ```
   C:\wamp64\www\cinema_wamp\
   ```

2. **Start WAMP**
   - Open WampServer and make sure Apache and MySQL are running (tray icon should be green)

3. **Open in browser**
   - Visit: http://localhost/cinema_wamp/
   - The database and all tables will be **created automatically** on first load
   - Sample movies, showtimes, and seats are seeded automatically

4. **Done!** No manual SQL import needed.

---

### File Structure
```
cinema_wamp/
├── index.php            ← Main app (frontend + install trigger)
├── includes/
│   ├── db.php           ← DB connection config
│   └── install.php      ← Auto-creates DB, tables, and seed data
└── api/
    ├── movies.php        ← GET/POST/DELETE movies
    ├── showtimes.php     ← GET/POST/DELETE showtimes
    ├── seats.php         ← GET seats by showtime
    ├── bookings.php      ← GET/POST/DELETE bookings
    └── stats.php         ← Dashboard statistics
```

---

### Database Credentials (default WAMP)
Configured in `includes/db.php`:
| Setting  | Value       |
|----------|-------------|
| Host     | localhost   |
| User     | root        |
| Password | *(empty)*   |
| Database | cinema_db   |

If you changed your MySQL root password in WAMP, edit `DB_PASS` in `includes/db.php`.

---

### Features
- **Dashboard** — live stats: bookings, revenue, occupancy rate
- **Seat Map** — interactive cinema grid; select and book seats in real-time
- **Bookings** — full list with search, filter, and cancel (frees seats)
- **Movies** — add and delete movies
- **Showtimes** — add showtimes per movie per day; seats auto-created

### Pricing
| Seat Type | Price   |
|-----------|---------|
| Standard  | ₱350    |
| VIP (D,E) | ₱550    |

### Default Data
- 5 movies seeded
- Showtimes for today + 6 days ahead (5 times/day per movie)
- 80 seats per showtime (rows A–H, 10 seats per row; D & E are VIP)
