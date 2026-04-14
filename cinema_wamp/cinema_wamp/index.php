<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/install.php';
installDatabase();
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>CinemaHub — Seat Management</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{
  --bg:#0a0a0f;--bg2:#12121a;--bg3:#1c1c28;--bg4:#252535;
  --border:#2a2a3d;--border2:#3a3a55;
  --text:#e8e8f0;--text2:#9898b8;--text3:#5a5a78;
  --gold:#c9a84c;--gold2:#f0d48a;--gold3:#8a6a20;
  --green:#2a7d4f;--green2:#3da368;--green3:#1a4f32;
  --red:#8b2a2a;--red2:#c43a3a;
  --blue:#1a4a7a;--blue2:#2a6abf;
  --radius:10px;--radius2:6px;
  --shadow:0 4px 24px rgba(0,0,0,0.5);
}
body{background:var(--bg);color:var(--text);font-family:'Segoe UI',system-ui,sans-serif;min-height:100vh;}

/* ── Layout ── */
.layout{display:flex;min-height:100vh;}
.sidebar{width:220px;background:var(--bg2);border-right:1px solid var(--border);padding:0;flex-shrink:0;display:flex;flex-direction:column;}
.main{flex:1;overflow-y:auto;}

/* ── Sidebar ── */
.sidebar-logo{padding:24px 20px 16px;border-bottom:1px solid var(--border);}
.sidebar-logo h1{font-size:18px;font-weight:700;color:var(--gold);letter-spacing:.04em;}
.sidebar-logo p{font-size:11px;color:var(--text3);margin-top:2px;}
.nav{flex:1;padding:12px 0;}
.nav-item{display:flex;align-items:center;gap:10px;padding:10px 20px;cursor:pointer;font-size:13px;color:var(--text2);border-left:3px solid transparent;transition:all .15s;}
.nav-item:hover{background:var(--bg3);color:var(--text);}
.nav-item.active{background:var(--bg3);color:var(--gold);border-left-color:var(--gold);}
.nav-icon{font-size:16px;width:20px;text-align:center;}
.sidebar-footer{padding:16px 20px;border-top:1px solid var(--border);font-size:11px;color:var(--text3);}

/* ── Top bar ── */
.topbar{background:var(--bg2);border-bottom:1px solid var(--border);padding:14px 28px;display:flex;align-items:center;justify-content:space-between;}
.topbar h2{font-size:16px;font-weight:600;color:var(--text);}
.topbar-actions{display:flex;gap:8px;}

/* ── Page content ── */
.page{display:none;padding:24px 28px;}
.page.active{display:block;}

/* ── Cards ── */
.stat-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:24px;}
.stat-card{background:var(--bg2);border:1px solid var(--border);border-radius:var(--radius);padding:16px 18px;}
.stat-card .label{font-size:11px;color:var(--text3);text-transform:uppercase;letter-spacing:.06em;}
.stat-card .value{font-size:26px;font-weight:700;margin-top:4px;color:var(--text);}
.stat-card .value.gold{color:var(--gold);}
.stat-card .value.green{color:var(--green2);}
.stat-card .value.red{color:var(--red2);}
.stat-card .sub{font-size:11px;color:var(--text3);margin-top:2px;}

/* ── Tables ── */
.table-card{background:var(--bg2);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;margin-bottom:20px;}
.table-card-header{padding:14px 18px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;}
.table-card-header h3{font-size:14px;font-weight:600;color:var(--text);}
table{width:100%;border-collapse:collapse;font-size:13px;}
th{padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:var(--text3);text-transform:uppercase;letter-spacing:.05em;background:var(--bg3);border-bottom:1px solid var(--border);}
td{padding:11px 14px;border-bottom:1px solid var(--border);color:var(--text2);}
tr:last-child td{border-bottom:none;}
tr:hover td{background:var(--bg3);color:var(--text);}
.badge{display:inline-block;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:600;}
.badge-green{background:#1a3d2a;color:#4caf80;}
.badge-red{background:#3d1a1a;color:#cf6060;}
.badge-gold{background:#3d2e0d;color:#d4a840;}
.badge-blue{background:#0d2040;color:#4a90d4;}

/* ── Buttons ── */
.btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:var(--radius2);font-size:13px;font-weight:500;cursor:pointer;border:none;transition:all .15s;}
.btn-gold{background:var(--gold);color:#1a1000;}
.btn-gold:hover{background:var(--gold2);}
.btn-green{background:var(--green);color:#fff;}
.btn-green:hover{background:var(--green2);}
.btn-red{background:var(--red);color:#fff;}
.btn-red:hover{background:var(--red2);}
.btn-ghost{background:transparent;color:var(--text2);border:1px solid var(--border2);}
.btn-ghost:hover{background:var(--bg3);color:var(--text);}
.btn-sm{padding:5px 10px;font-size:12px;}
.btn:disabled{opacity:.4;cursor:not-allowed;}

/* ── Forms ── */
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
.form-group{display:flex;flex-direction:column;gap:5px;}
.form-group.full{grid-column:1/-1;}
label{font-size:12px;color:var(--text3);}
input,select,textarea{background:var(--bg3);border:1px solid var(--border2);border-radius:var(--radius2);padding:8px 10px;font-size:13px;color:var(--text);outline:none;transition:border .15s;}
input:focus,select:focus,textarea:focus{border-color:var(--gold3);}
select option{background:var(--bg3);}

/* ── Modal ── */
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:1000;align-items:center;justify-content:center;}
.modal-overlay.open{display:flex;}
.modal{background:var(--bg2);border:1px solid var(--border2);border-radius:var(--radius);padding:24px;width:480px;max-width:95vw;max-height:90vh;overflow-y:auto;box-shadow:var(--shadow);}
.modal h2{font-size:16px;font-weight:700;margin-bottom:16px;color:var(--text);}
.modal-actions{display:flex;gap:8px;justify-content:flex-end;margin-top:20px;}

/* ── Cinema seats ── */
.cinema-wrap{background:var(--bg2);border:1px solid var(--border);border-radius:var(--radius);padding:24px;margin-bottom:20px;}
.screen-wrap{text-align:center;margin-bottom:28px;}
.screen{display:inline-block;background:linear-gradient(to bottom,#f5f0e0,#c8b88a);border-radius:4px 4px 0 0;width:55%;min-width:200px;height:14px;position:relative;}
.screen::after{content:'SCREEN';position:absolute;bottom:-20px;left:50%;transform:translateX(-50%);font-size:10px;letter-spacing:.14em;color:var(--text3);}
.seating-grid{display:flex;flex-direction:column;align-items:center;gap:5px;margin-top:16px;}
.row-wrapper{display:flex;align-items:center;gap:6px;}
.row-label{font-size:10px;font-weight:700;color:var(--text3);min-width:14px;text-align:center;}
.row-seats{display:flex;gap:4px;}
.seat{width:30px;height:30px;border-radius:5px 5px 3px 3px;cursor:pointer;border:1.5px solid;transition:transform .1s,background .1s;display:flex;align-items:center;justify-content:center;font-size:8px;font-weight:600;position:relative;}
.seat:hover:not(.occupied){transform:scale(1.15);}
.seat.available{background:#1c1c28;border-color:#2a2a40;color:#5a5a78;}
.seat.booked{background:#2a1a1a;border-color:#3a2020;color:#5a3030;cursor:not-allowed;}
.seat.selected{background:var(--green3);border-color:var(--green2);color:#8fe0b0;}
.seat.vip.available{background:#1a1505;border-color:var(--gold3);color:var(--gold3);}
.seat.vip.selected{background:#3d2800;border-color:var(--gold);color:var(--gold2);}
.seat.vip.booked{background:#2a2010;border-color:#3a3010;cursor:not-allowed;}
.aisle{width:12px;}
.legend{display:flex;gap:14px;justify-content:center;flex-wrap:wrap;margin-top:16px;}
.legend-item{display:flex;align-items:center;gap:5px;font-size:11px;color:var(--text3);}
.legend-swatch{width:14px;height:14px;border-radius:3px;border:1.5px solid;}
.controls-bar{display:flex;gap:10px;align-items:center;margin-bottom:16px;flex-wrap:wrap;}
.controls-bar select{min-width:150px;}

/* ── Booking panel ── */
.booking-panel{display:grid;grid-template-columns:1fr auto auto;gap:12px;align-items:start;}
.selected-box{background:var(--bg3);border:1px solid var(--border);border-radius:var(--radius2);padding:12px 14px;min-height:52px;}
.selected-box .title{font-size:11px;color:var(--text3);margin-bottom:6px;}
.seat-tags{display:flex;flex-wrap:wrap;gap:4px;}
.seat-tag{background:var(--green3);color:#8fe0b0;border:1px solid var(--green);font-size:11px;padding:2px 7px;border-radius:12px;display:flex;align-items:center;gap:3px;cursor:pointer;}
.seat-tag.vip{background:#3d2800;color:var(--gold2);border-color:var(--gold3);}
.seat-tag .rm{font-size:13px;}
.price-box{background:var(--bg3);border:1px solid var(--border);border-radius:var(--radius2);padding:12px 16px;min-width:160px;font-size:12px;}
.price-row{display:flex;justify-content:space-between;color:var(--text2);margin-bottom:4px;}
.price-row.total{border-top:1px solid var(--border);padding-top:6px;margin-top:4px;font-size:14px;font-weight:700;color:var(--text);}

/* ── Toast ── */
#toast{position:fixed;bottom:24px;right:24px;background:var(--bg2);border:1px solid var(--border2);color:var(--text);padding:12px 18px;border-radius:var(--radius2);font-size:13px;box-shadow:var(--shadow);opacity:0;pointer-events:none;transition:opacity .3s;z-index:2000;max-width:320px;}
#toast.show{opacity:1;}
#toast.success{border-color:var(--green2);color:#8fe0b0;}
#toast.error{border-color:var(--red2);color:#f08080;}

/* ── Filters ── */
.filters{display:flex;gap:8px;align-items:center;flex-wrap:wrap;margin-bottom:16px;}
.filters input,.filters select{padding:7px 10px;font-size:13px;}
.search-input{min-width:200px;}

/* ── Responsive ── */
@media(max-width:768px){
  .sidebar{width:56px;}
  .nav-item span,.sidebar-logo p,.sidebar-logo h1,.sidebar-footer{display:none;}
  .sidebar-logo{padding:12px;text-align:center;}
  .nav-item{justify-content:center;padding:12px;}
  .stat-grid{grid-template-columns:1fr 1fr;}
  .booking-panel{grid-template-columns:1fr;}
  .form-grid{grid-template-columns:1fr;}
}
</style>
</head>
<body>

<div class="layout">
  <!-- SIDEBAR -->
  <aside class="sidebar">
    <div class="sidebar-logo">
      <h1>&#127916; CinemaHub</h1>
      <p>Seat Management System</p>
    </div>
    <nav class="nav">
      <div class="nav-item active" onclick="showPage('dashboard')">
        <span class="nav-icon">&#9685;</span><span>Dashboard</span>
      </div>
      <div class="nav-item" onclick="showPage('seats')">
        <span class="nav-icon">&#127917;</span><span>Seat Map</span>
      </div>
      <div class="nav-item" onclick="showPage('bookings')">
        <span class="nav-icon">&#128203;</span><span>Bookings</span>
      </div>
      <div class="nav-item" onclick="showPage('movies')">
        <span class="nav-icon">&#127909;</span><span>Movies</span>
      </div>
      <div class="nav-item" onclick="showPage('showtimes')">
        <span class="nav-icon">&#128337;</span><span>Showtimes</span>
      </div>
    </nav>
    <div class="sidebar-footer">v1.0 &bull; WAMP</div>
  </aside>

  <!-- MAIN -->
  <div class="main">

    <!-- DASHBOARD -->
    <div id="page-dashboard" class="page active">
      <div class="topbar"><h2>Dashboard</h2></div>
      <div style="padding:24px 28px;">
        <div class="stat-grid" id="dash-stats">
          <div class="stat-card"><div class="label">Total Bookings</div><div class="value" id="ds-total-b">...</div></div>
          <div class="stat-card"><div class="label">Today's Bookings</div><div class="value green" id="ds-today-b">...</div></div>
          <div class="stat-card"><div class="label">Total Revenue</div><div class="value gold" id="ds-revenue">...</div></div>
          <div class="stat-card"><div class="label">Today's Revenue</div><div class="value gold" id="ds-today-rev">...</div></div>
          <div class="stat-card"><div class="label">Total Seats</div><div class="value" id="ds-total-s">...</div></div>
          <div class="stat-card"><div class="label">Booked Seats</div><div class="value red" id="ds-booked-s">...</div></div>
          <div class="stat-card"><div class="label">Available Seats</div><div class="value green" id="ds-avail-s">...</div></div>
          <div class="stat-card"><div class="label">Occupancy Rate</div><div class="value gold" id="ds-occ">...</div></div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
          <div class="table-card">
            <div class="table-card-header"><h3>Revenue by Movie</h3></div>
            <table><thead><tr><th>Movie</th><th>Bookings</th><th>Revenue</th></tr></thead>
            <tbody id="dash-movies"></tbody></table>
          </div>
          <div class="table-card">
            <div class="table-card-header"><h3>Recent Bookings</h3></div>
            <table><thead><tr><th>Guest</th><th>Movie</th><th>Amount</th></tr></thead>
            <tbody id="dash-recent"></tbody></table>
          </div>
        </div>
      </div>
    </div>

    <!-- SEAT MAP -->
    <div id="page-seats" class="page">
      <div class="topbar">
        <h2>Seat Map</h2>
        <div class="topbar-actions">
          <button class="btn btn-green" id="btn-open-booking" onclick="openBookingModal()" disabled>&#10003; Book Selected</button>
          <button class="btn btn-ghost btn-sm" onclick="clearSeatSelection()">Clear</button>
        </div>
      </div>
      <div style="padding:24px 28px;">
        <div class="controls-bar">
          <select id="seat-movie" onchange="loadShowtimesForSeat()"><option value="">-- Select Movie --</option></select>
          <input type="date" id="seat-date" value="" onchange="loadShowtimesForSeat()"/>
          <select id="seat-showtime" onchange="loadSeats()"><option value="">-- Select Showtime --</option></select>
          <span id="seat-info" style="font-size:12px;color:var(--text3);margin-left:4px;"></span>
        </div>

        <div class="cinema-wrap" id="cinema-wrap" style="display:none;">
          <div class="screen-wrap"><div class="screen"></div></div>
          <div class="seating-grid" id="seating-grid"></div>
          <div class="legend">
            <div class="legend-item"><div class="legend-swatch" style="background:#1c1c28;border-color:#2a2a40;"></div>Available</div>
            <div class="legend-item"><div class="legend-swatch" style="background:#2a1a1a;border-color:#3a2020;"></div>Booked</div>
            <div class="legend-item"><div class="legend-swatch" style="background:var(--green3);border-color:var(--green2);"></div>Selected</div>
            <div class="legend-item"><div class="legend-swatch" style="background:#1a1505;border-color:var(--gold3);"></div>VIP</div>
          </div>
        </div>

        <div class="booking-panel" id="booking-panel" style="display:none;">
          <div class="selected-box">
            <div class="title">Selected Seats</div>
            <div class="seat-tags" id="seat-tags"><span style="font-size:12px;color:var(--text3)">None selected</span></div>
          </div>
          <div class="price-box">
            <div class="price-row"><span>Standard</span><span id="p-std">0 × ₱350</span></div>
            <div class="price-row"><span>VIP</span><span id="p-vip">0 × ₱550</span></div>
            <div class="price-row total"><span>Total</span><span id="p-total">₱0</span></div>
          </div>
        </div>
      </div>
    </div>

    <!-- BOOKINGS -->
    <div id="page-bookings" class="page">
      <div class="topbar"><h2>Bookings</h2></div>
      <div style="padding:24px 28px;">
        <div class="filters">
          <input class="search-input" type="text" id="book-search" placeholder="Search guest name, phone..." oninput="loadBookings()"/>
          <select id="book-showtime" onchange="loadBookings()"><option value="">All Showtimes</option></select>
          <button class="btn btn-ghost btn-sm" onclick="document.getElementById('book-search').value='';document.getElementById('book-showtime').value='';loadBookings()">Reset</button>
        </div>
        <div class="table-card">
          <div class="table-card-header">
            <h3>All Bookings</h3>
            <span id="book-count" style="font-size:12px;color:var(--text3);"></span>
          </div>
          <div style="overflow-x:auto;">
            <table>
              <thead><tr><th>#</th><th>Guest</th><th>Phone</th><th>Movie</th><th>Date</th><th>Time</th><th>Hall</th><th>Seats</th><th>Amount</th><th>Booked At</th><th></th></tr></thead>
              <tbody id="booking-tbody"></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- MOVIES -->
    <div id="page-movies" class="page">
      <div class="topbar">
        <h2>Movies</h2>
        <button class="btn btn-gold" onclick="openMovieModal()">&#43; Add Movie</button>
      </div>
      <div style="padding:24px 28px;">
        <div class="table-card">
          <table>
            <thead><tr><th>Title</th><th>Genre</th><th>Duration</th><th>Rating</th><th></th></tr></thead>
            <tbody id="movie-tbody"></tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- SHOWTIMES -->
    <div id="page-showtimes" class="page">
      <div class="topbar">
        <h2>Showtimes</h2>
        <button class="btn btn-gold" onclick="openShowtimeModal()">&#43; Add Showtime</button>
      </div>
      <div style="padding:24px 28px;">
        <div class="filters">
          <input type="date" id="st-filter-date" value="" onchange="loadShowtimes()"/>
          <select id="st-filter-movie" onchange="loadShowtimes()"><option value="">All Movies</option></select>
        </div>
        <div class="table-card">
          <table>
            <thead><tr><th>Movie</th><th>Date</th><th>Time</th><th>Hall</th><th>Available</th><th>Booked</th><th></th></tr></thead>
            <tbody id="showtime-tbody"></tbody>
          </table>
        </div>
      </div>
    </div>

  </div><!-- end main -->
</div><!-- end layout -->

<!-- BOOKING MODAL -->
<div class="modal-overlay" id="modal-booking">
  <div class="modal">
    <h2>&#128203; Confirm Booking</h2>
    <p id="modal-booking-summary" style="font-size:13px;color:var(--text2);margin-bottom:16px;"></p>
    <div class="form-grid">
      <div class="form-group full"><label>Full Name *</label><input id="bk-name" type="text" placeholder="Juan dela Cruz"/></div>
      <div class="form-group"><label>Phone</label><input id="bk-phone" type="text" placeholder="+63 9xx xxx xxxx"/></div>
      <div class="form-group"><label>Email</label><input id="bk-email" type="email" placeholder="email@example.com"/></div>
    </div>
    <div class="modal-actions">
      <button class="btn btn-ghost" onclick="closeModal('modal-booking')">Cancel</button>
      <button class="btn btn-green" onclick="confirmBooking()">&#10003; Confirm Booking</button>
    </div>
  </div>
</div>

<!-- MOVIE MODAL -->
<div class="modal-overlay" id="modal-movie">
  <div class="modal">
    <h2>&#127909; Add Movie</h2>
    <div class="form-grid">
      <div class="form-group full"><label>Title *</label><input id="mv-title" type="text" placeholder="Movie title"/></div>
      <div class="form-group"><label>Genre</label><input id="mv-genre" type="text" placeholder="Sci-Fi, Action..."/></div>
      <div class="form-group"><label>Duration (min)</label><input id="mv-dur" type="number" placeholder="120"/></div>
      <div class="form-group"><label>Rating</label>
        <select id="mv-rating"><option>G</option><option>PG</option><option>PG-13</option><option>R</option><option>NC-17</option></select>
      </div>
    </div>
    <div class="modal-actions">
      <button class="btn btn-ghost" onclick="closeModal('modal-movie')">Cancel</button>
      <button class="btn btn-gold" onclick="saveMovie()">Save Movie</button>
    </div>
  </div>
</div>

<!-- SHOWTIME MODAL -->
<div class="modal-overlay" id="modal-showtime">
  <div class="modal">
    <h2>&#128337; Add Showtime</h2>
    <div class="form-grid">
      <div class="form-group full"><label>Movie *</label>
        <select id="st-movie-id"><option value="">-- Select Movie --</option></select>
      </div>
      <div class="form-group"><label>Date *</label><input id="st-date" type="date"/></div>
      <div class="form-group"><label>Time *</label>
        <select id="st-time">
          <option>10:00 AM</option><option>1:30 PM</option><option>4:00 PM</option><option>7:30 PM</option><option>10:00 PM</option>
        </select>
      </div>
      <div class="form-group"><label>Hall</label>
        <select id="st-hall"><option>Hall 1</option><option>Hall 2</option><option>Hall 3</option></select>
      </div>
    </div>
    <div class="modal-actions">
      <button class="btn btn-ghost" onclick="closeModal('modal-showtime')">Cancel</button>
      <button class="btn btn-gold" onclick="saveShowtime()">Save Showtime</button>
    </div>
  </div>
</div>

<div id="toast"></div>

<script>
const BASE = 'api/';
let selectedSeats = [];
let currentShowtimeId = null;
let allSeatsData = [];

// ── Fetch helpers ────────────────────────────────────────────────────────────
async function api(endpoint, method='GET', body=null) {
  const opts = { method, headers:{'Content-Type':'application/json'} };
  if (body) opts.body = JSON.stringify(body);
  const r = await fetch(BASE + endpoint, opts);
  const data = await r.json();
  if (data.error) throw new Error(data.error);
  return data;
}

// ── Toast ────────────────────────────────────────────────────────────────────
function toast(msg, type='') {
  const el = document.getElementById('toast');
  el.textContent = msg;
  el.className = 'show ' + type;
  clearTimeout(el._t);
  el._t = setTimeout(() => el.className='', 3000);
}

// ── Navigation ───────────────────────────────────────────────────────────────
function showPage(name) {
  document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
  document.getElementById('page-'+name).classList.add('active');
  event.currentTarget.classList.add('active');
  if (name==='dashboard') loadDashboard();
  if (name==='seats')     { loadMovieDropdowns(); document.getElementById('seat-date').value = today(); }
  if (name==='bookings')  { loadBookingShowtimeFilter(); loadBookings(); }
  if (name==='movies')    loadMovies();
  if (name==='showtimes') { loadShowtimes(); loadMovieDropdowns(); }
}

function today() { return new Date().toISOString().slice(0,10); }

function openModal(id) { document.getElementById(id).classList.add('open'); }
function closeModal(id){ document.getElementById(id).classList.remove('open'); }

// ── Dashboard ────────────────────────────────────────────────────────────────
async function loadDashboard() {
  try {
    const d = await api('stats.php');
    document.getElementById('ds-total-b').textContent  = d.total_bookings;
    document.getElementById('ds-today-b').textContent  = d.today_bookings;
    document.getElementById('ds-revenue').textContent  = '₱'+Number(d.total_revenue).toLocaleString();
    document.getElementById('ds-today-rev').textContent= '₱'+Number(d.today_revenue).toLocaleString();
    document.getElementById('ds-total-s').textContent  = d.total_seats;
    document.getElementById('ds-booked-s').textContent = d.booked_seats;
    document.getElementById('ds-avail-s').textContent  = d.available_seats;
    const occ = d.total_seats ? Math.round(d.booked_seats/d.total_seats*100) : 0;
    document.getElementById('ds-occ').textContent = occ+'%';

    document.getElementById('dash-movies').innerHTML = d.by_movie.map(m=>`
      <tr><td>${m.title}</td><td>${m.bookings}</td><td>₱${Number(m.revenue).toLocaleString()}</td></tr>
    `).join('');

    document.getElementById('dash-recent').innerHTML = d.recent_bookings.map(b=>`
      <tr><td>${esc(b.guest_name)}</td><td style="max-width:130px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${esc(b.movie_title)}</td><td>₱${Number(b.total_amount).toLocaleString()}</td></tr>
    `).join('');
  } catch(e){ toast(e.message,'error'); }
}

// ── Movies ───────────────────────────────────────────────────────────────────
async function loadMovies() {
  const movies = await api('movies.php');
  document.getElementById('movie-tbody').innerHTML = movies.map(m=>`
    <tr>
      <td><strong>${esc(m.title)}</strong></td>
      <td>${esc(m.genre||'')}</td>
      <td>${m.duration_min ? m.duration_min+'m' : '-'}</td>
      <td><span class="badge badge-blue">${esc(m.rating||'')}</span></td>
      <td><button class="btn btn-red btn-sm" onclick="deleteMovie(${m.id})">Delete</button></td>
    </tr>
  `).join('');
}

function openMovieModal() { openModal('modal-movie'); }

async function saveMovie() {
  const title = document.getElementById('mv-title').value.trim();
  if (!title) { toast('Title is required','error'); return; }
  try {
    await api('movies.php','POST',{
      title, genre: document.getElementById('mv-genre').value,
      duration_min: parseInt(document.getElementById('mv-dur').value)||0,
      rating: document.getElementById('mv-rating').value
    });
    closeModal('modal-movie');
    loadMovies();
    toast('Movie added!','success');
  } catch(e){ toast(e.message,'error'); }
}

async function deleteMovie(id) {
  if (!confirm('Delete this movie and all its showtimes/bookings?')) return;
  try { await api('movies.php?id='+id,'DELETE'); loadMovies(); toast('Deleted','success'); }
  catch(e){ toast(e.message,'error'); }
}

// ── Showtimes ────────────────────────────────────────────────────────────────
async function loadMovieDropdowns() {
  const movies = await api('movies.php');
  const opts = movies.map(m=>`<option value="${m.id}">${esc(m.title)}</option>`).join('');
  ['seat-movie','st-movie-id','st-filter-movie'].forEach(id=>{
    const el = document.getElementById(id);
    if (!el) return;
    const prefix = el.options[0] && el.options[0].value==='' ? el.options[0].outerHTML : '';
    el.innerHTML = prefix + opts;
  });
}

async function loadShowtimes() {
  const date  = document.getElementById('st-filter-date').value || today();
  const movie = document.getElementById('st-filter-movie').value;
  let url = `showtimes.php?date=${date}`;
  if (movie) url += `&movie_id=${movie}`;
  const rows = await api(url);
  document.getElementById('showtime-tbody').innerHTML = rows.map(s=>`
    <tr>
      <td>${esc(s.movie_title)}</td>
      <td>${s.show_date}</td>
      <td>${esc(s.show_time)}</td>
      <td>${esc(s.hall)}</td>
      <td><span class="badge badge-green">${s.available_seats}</span></td>
      <td><span class="badge badge-red">${s.booked_seats}</span></td>
      <td><button class="btn btn-red btn-sm" onclick="deleteShowtime(${s.id})">Delete</button></td>
    </tr>
  `).join('') || '<tr><td colspan="7" style="text-align:center;color:var(--text3);padding:20px;">No showtimes found</td></tr>';
}

function openShowtimeModal() {
  document.getElementById('st-date').value = today();
  openModal('modal-showtime');
}

async function saveShowtime() {
  const movieId  = document.getElementById('st-movie-id').value;
  const showDate = document.getElementById('st-date').value;
  const showTime = document.getElementById('st-time').value;
  const hall     = document.getElementById('st-hall').value;
  if (!movieId||!showDate) { toast('Movie and date required','error'); return; }
  try {
    await api('showtimes.php','POST',{movie_id:movieId,show_date:showDate,show_time:showTime,hall});
    closeModal('modal-showtime');
    loadShowtimes();
    toast('Showtime added!','success');
  } catch(e){ toast(e.message,'error'); }
}

async function deleteShowtime(id) {
  if (!confirm('Delete this showtime and all its bookings?')) return;
  try { await api('showtimes.php?id='+id,'DELETE'); loadShowtimes(); toast('Deleted','success'); }
  catch(e){ toast(e.message,'error'); }
}

// ── Seat Map ─────────────────────────────────────────────────────────────────
async function loadShowtimesForSeat() {
  const movieId = document.getElementById('seat-movie').value;
  const date    = document.getElementById('seat-date').value || today();
  const sel     = document.getElementById('seat-showtime');
  sel.innerHTML = '<option value="">-- Select Showtime --</option>';
  document.getElementById('cinema-wrap').style.display='none';
  document.getElementById('booking-panel').style.display='none';
  if (!movieId) return;
  const rows = await api(`showtimes.php?movie_id=${movieId}&date=${date}`);
  rows.forEach(s=>{
    const o = new Option(`${s.show_time} — ${s.hall} (${s.available_seats} avail)`, s.id);
    sel.appendChild(o);
  });
}

async function loadSeats() {
  const sid = document.getElementById('seat-showtime').value;
  if (!sid) { document.getElementById('cinema-wrap').style.display='none'; return; }
  currentShowtimeId = sid;
  selectedSeats = [];
  const seats = await api(`seats.php?showtime_id=${sid}`);
  allSeatsData = seats;
  renderSeatGrid(seats);
  document.getElementById('cinema-wrap').style.display='block';
  document.getElementById('booking-panel').style.display='grid';

  // Info
  const avail = seats.filter(s=>s.status==='available').length;
  const booked= seats.filter(s=>s.status==='booked').length;
  document.getElementById('seat-info').textContent = `${avail} available · ${booked} booked`;
  updateSeatPanel();
}

function renderSeatGrid(seats) {
  const byRow = {};
  seats.forEach(s=>{ if(!byRow[s.row_label]) byRow[s.row_label]=[]; byRow[s.row_label].push(s); });
  const grid = document.getElementById('seating-grid');
  grid.innerHTML = '';
  Object.keys(byRow).sort().forEach(row=>{
    const wrap = document.createElement('div'); wrap.className='row-wrapper';
    const lbl = document.createElement('div'); lbl.className='row-label'; lbl.textContent=row;
    wrap.appendChild(lbl);
    const rowDiv = document.createElement('div'); rowDiv.className='row-seats';
    byRow[row].sort((a,b)=>a.col_num-b.col_num).forEach((s,i)=>{
      if (i===5) { const a=document.createElement('div'); a.className='aisle'; rowDiv.appendChild(a); }
      const el = document.createElement('div');
      const key = s.id;
      const isSelected = selectedSeats.includes(key);
      el.className = `seat ${s.seat_type} ${isSelected?'selected':s.status}`;
      el.textContent = s.col_num+1;
      el.title = s.status==='booked' ? `Booked by ${s.guest_name||'N/A'}` : `${s.row_label}${s.col_num+1} — ${s.seat_type} — ₱${s.price}`;
      if (s.status==='available') el.onclick = ()=>toggleSeat(s.id);
      rowDiv.appendChild(el);
    });
    wrap.appendChild(rowDiv); grid.appendChild(wrap);
  });
}

function toggleSeat(seatId) {
  const idx = selectedSeats.indexOf(seatId);
  if (idx===-1) {
    if (selectedSeats.length>=10){ toast('Max 10 seats per booking','error'); return; }
    selectedSeats.push(seatId);
  } else { selectedSeats.splice(idx,1); }
  renderSeatGrid(allSeatsData);
  updateSeatPanel();
}

function clearSeatSelection() { selectedSeats=[]; renderSeatGrid(allSeatsData); updateSeatPanel(); }

function updateSeatPanel() {
  const tags = document.getElementById('seat-tags');
  const btnBook = document.getElementById('btn-open-booking');
  if (!selectedSeats.length) {
    tags.innerHTML='<span style="font-size:12px;color:var(--text3)">None selected</span>';
    btnBook.disabled=true;
  } else {
    tags.innerHTML = selectedSeats.map(id=>{
      const s = allSeatsData.find(x=>x.id==id);
      if (!s) return '';
      return `<span class="seat-tag ${s.seat_type==='vip'?'vip':''}">${s.row_label}${s.col_num+1}<span class="rm" onclick="toggleSeat(${id})">×</span></span>`;
    }).join('');
    btnBook.disabled=false;
  }
  const std = selectedSeats.filter(id=>{ const s=allSeatsData.find(x=>x.id==id); return s&&s.seat_type==='standard'; });
  const vip = selectedSeats.filter(id=>{ const s=allSeatsData.find(x=>x.id==id); return s&&s.seat_type==='vip'; });
  const total = std.length*350 + vip.length*550;
  document.getElementById('p-std').textContent = `${std.length} × ₱350`;
  document.getElementById('p-vip').textContent = `${vip.length} × ₱550`;
  document.getElementById('p-total').textContent = `₱${total.toLocaleString()}`;
}

function openBookingModal() {
  if (!selectedSeats.length) return;
  const labels = selectedSeats.map(id=>{ const s=allSeatsData.find(x=>x.id==id); return s?s.row_label+(s.col_num+1):''; }).join(', ');
  const std = selectedSeats.filter(id=>{ const s=allSeatsData.find(x=>x.id==id); return s&&s.seat_type==='standard'; });
  const vip = selectedSeats.filter(id=>{ const s=allSeatsData.find(x=>x.id==id); return s&&s.seat_type==='vip'; });
  const total = std.length*350+vip.length*550;
  document.getElementById('modal-booking-summary').textContent = `Seats: ${labels} — Total: ₱${total.toLocaleString()}`;
  document.getElementById('bk-name').value='';
  document.getElementById('bk-phone').value='';
  document.getElementById('bk-email').value='';
  openModal('modal-booking');
}

async function confirmBooking() {
  const name  = document.getElementById('bk-name').value.trim();
  const phone = document.getElementById('bk-phone').value.trim();
  const email = document.getElementById('bk-email').value.trim();
  if (!name){ toast('Name is required','error'); return; }
  try {
    await api('bookings.php','POST',{
      showtime_id: currentShowtimeId,
      seat_ids: selectedSeats,
      guest_name: name, guest_phone: phone, guest_email: email
    });
    closeModal('modal-booking');
    selectedSeats=[];
    toast('Booking confirmed!','success');
    loadSeats();
  } catch(e){ toast(e.message,'error'); }
}

// ── Bookings list ─────────────────────────────────────────────────────────────
async function loadBookingShowtimeFilter() {
  const today_ = today();
  const rows = await api(`showtimes.php?date=${today_}`);
  const sel = document.getElementById('book-showtime');
  sel.innerHTML = '<option value="">All Showtimes</option>';
  rows.forEach(s=>{ const o=new Option(`${s.movie_title} ${s.show_date} ${s.show_time}`,s.id); sel.appendChild(o); });
}

async function loadBookings() {
  const search   = document.getElementById('book-search').value.trim();
  const showtime = document.getElementById('book-showtime').value;
  let url = 'bookings.php?';
  if (search)   url += `search=${encodeURIComponent(search)}&`;
  if (showtime) url += `showtime_id=${showtime}`;
  const rows = await api(url);
  document.getElementById('book-count').textContent = rows.length + ' record(s)';
  document.getElementById('booking-tbody').innerHTML = rows.map(b=>`
    <tr>
      <td>${b.id}</td>
      <td><strong>${esc(b.guest_name)}</strong></td>
      <td>${esc(b.guest_phone||'')}</td>
      <td style="max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${esc(b.movie_title)}</td>
      <td>${b.show_date}</td>
      <td>${esc(b.show_time)}</td>
      <td>${esc(b.hall)}</td>
      <td style="font-size:12px;">${esc(b.seats||'')}</td>
      <td><span class="badge badge-gold">₱${Number(b.total_amount).toLocaleString()}</span></td>
      <td style="font-size:11px;color:var(--text3);">${b.booked_at}</td>
      <td><button class="btn btn-red btn-sm" onclick="cancelBooking(${b.id})">Cancel</button></td>
    </tr>
  `).join('') || '<tr><td colspan="11" style="text-align:center;padding:20px;color:var(--text3);">No bookings found</td></tr>';
}

async function cancelBooking(id) {
  if (!confirm('Cancel this booking and free the seats?')) return;
  try { await api('bookings.php?id='+id,'DELETE'); loadBookings(); toast('Booking cancelled','success'); }
  catch(e){ toast(e.message,'error'); }
}

function esc(s){ const d=document.createElement('div'); d.textContent=String(s||''); return d.innerHTML; }

// ── Init ─────────────────────────────────────────────────────────────────────
loadDashboard();
document.getElementById('st-filter-date').value = today();
</script>
</body>
</html>
