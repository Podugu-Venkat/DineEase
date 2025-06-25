<?php
require_once '../config.php';

if (isset($_GET['process_reservation'])) {
    session_start();
    // Retrieve reservation details from GET parameters
    $customer_name    = $_GET['customer_name']    ?? '';
    $reservation_time = $_GET['reservation_time'] ?? '';
    $reservation_date = $_GET['reservation_date'] ?? '';
    $table_id         = $_GET['table_id']         ?? '';
    $preordered       = $_GET['preordered']       ?? '';
    
    // Fetch table capacity from restaurant_tables
    $select_query_capacity = "SELECT capacity FROM restaurant_tables WHERE table_id='$table_id';";
    $results_capacity = mysqli_query($link, $select_query_capacity);
    if ($results_capacity && $row = mysqli_fetch_assoc($results_capacity)) {
        $head_count = $row['capacity'];
        // Convert time and date into proper MySQL formats
        $reservation_time_db = date("H:i:s", strtotime($reservation_time));
        $reservation_date_db = date("Y-m-d", strtotime($reservation_date));
        
        // Insert into Reservations without manual reservation_id (assumes auto-increment)
        $insert_query1 = "INSERT INTO Reservations (customer_name, table_id, reservation_time, reservation_date, head_count, pre_ordered_items) 
                           VALUES ('$customer_name', '$table_id', '$reservation_time_db', '$reservation_date_db', '$head_count', '$preordered');";
        mysqli_query($link, $insert_query1);
        // Ensure the returned id is cast to an integer and log it for debugging
        $reservation_id = (int) mysqli_insert_id($link);
        error_log("Reservation ID generated: " . $reservation_id);
        
        // Insert into Table_Availability using the generated reservation_id
        $insert_query2 = "INSERT INTO Table_Availability (availability_id, table_id, reservation_date, reservation_time, status) 
                           VALUES ('$reservation_id', '$table_id', '$reservation_date_db', '$reservation_time_db', 'no');";
        mysqli_query($link, $insert_query2);
        
        $_SESSION['customer_name'] = $customer_name;
        $_SESSION['reservation_id'] = $reservation_id;
        header("Location: reservePage.php?reservation=success&reservation_id=$reservation_id");
        exit;
    }
}

$sqlmainDishes = "SELECT * FROM Menu WHERE item_category = 'Main Dishes' ORDER BY item_type; ";
$resultmainDishes = mysqli_query($link, $sqlmainDishes);
$mainDishes = mysqli_fetch_all($resultmainDishes, MYSQLI_ASSOC);

$sqldrinks = "SELECT * FROM Menu WHERE item_category = 'Drinks' ORDER BY item_type; ";
$resultdrinks = mysqli_query($link, $sqldrinks);
$drinks = mysqli_fetch_all($resultdrinks, MYSQLI_ASSOC);

$sqlsides = "SELECT * FROM Menu WHERE item_category = 'Side Snacks' ORDER BY item_type; ";
$resultsides = mysqli_query($link, $sqlsides);
$sides = mysqli_fetch_all($resultsides, MYSQLI_ASSOC);

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reservation UI</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background:rgb(255, 255, 255);
      color: #fff;
    }

    .container {
      max-width: 1200px; /* widened container */
      margin: 40px auto;
      padding: 30px;
      background: #1e1e1e;
      border-radius: 20px;
      box-shadow: 0 4px 25px rgba(0,0,0,0.3);
    }

    h2 {
      margin-bottom: 30px;
      font-size: 28px;
    }

    .section {
      margin-bottom: 35px;
    }

    label {
      font-weight: bold;
      display: block;
      margin-bottom: 10px;
      font-size: 18px;
    }

    .scroll-box {
      display: flex;
      overflow-x: hidden; /* scrollbar removed */
      gap: 14px;
      padding-bottom: 10px;
      cursor: grab;
    }

    .scroll-box button {
      min-width: 70px;
      padding: 12px 18px;
      border: 2px solid #444;
      border-radius: 14px;
      background: #2a2a2a;
      color: white;
      cursor: pointer;
      font-size: 16px;
      transition: 0.2s;
    }

    .scroll-box button.active {
      border-color: #ff6a00;
      background: #ff6a00;
      color: black;
    }

    .time-section {
      margin-top: 10px;
      background: #2b2b2b;
      border-radius: 16px;
      padding: 20px;
      position: relative;
    }

    .time-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      cursor: pointer;
      margin-bottom: 15px;
    }

    .time-header h3 {
      margin: 0;
      font-size: 20px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .time-header.selected {
      background-color: #ff6a00;
      color: black;
    }

    .time-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
      gap: 14px;
    }

    .time-grid button {
      padding: 12px;
      font-size: 15px;
      background: #1e1e1e;
      border: 1px solid #555;
      border-radius: 12px;
      color: #fff;
      cursor: pointer;
      transition: 0.2s;
    }

    .time-grid button:hover {
      background: #ff6a00;
      color: black;
    }

    .time-grid button.active {
      background: #ff6a00;
      color: black;
    }

    .proceed {
      width: 100%;
      padding: 16px;
      background: #888;
      border: none;
      border-radius: 12px;
      font-size: 18px;
      cursor: not-allowed;
      margin-top: 30px;
    }

    .dropdown-arrow {
      font-size: 20px;
      user-select: none;
    }

    #date-select button {
      text-align: center;
      
    }

    #guest-select {
      gap: 6px;              /* reduced gap */
      justify-content: flex-end; /* align buttons to the right */
    }

    #guest-select button {
      padding: 12px 25px;      /* reduced padding */
      min-width: auto;          /* allow flexible width */
    }

    /* Add scrollbar styling for menu container */
    .menu-container::-webkit-scrollbar {
      width: 10px;
    }
    .menu-container::-webkit-scrollbar-track {
      background: gray;
    }
    .menu-container::-webkit-scrollbar-thumb {
      background: white;
      border-radius: 10px;
    }

    /* Added Reservation Overlay styles */
    #reservationOverlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.9);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      opacity: 0;
      visibility: hidden;
      transition: opacity 0.5s ease, visibility 0.5s ease;
    }
    #reservationOverlay.active {
      opacity: 1;
      visibility: visible;
    }
    #reservationOverlay img {
      max-width: 200px;
      margin-bottom: 20px;
    }
    #reservationOverlay h1 {
      color: #fff;
      font-size: 32px;
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- Added logo at the top -->
    <div style="text-align: center; margin-bottom: 20px;">
      <a href="http://localhost/restaurant-management/customerSide/home/home.php">
        <img src="logo.png" alt="Logo" style="max-width: 200px;">
      </a>
    </div>
    <h2>Book a Table</h2>

    <!-- Modified Guest Selection and Customer Name -->
    <div class="section" style="display: flex; gap: 50px;">
      <div style="flex: 1;">
        <label>Customer Name</label>
        <input type="text" id="customer-name" oninput="updateProceedButton()" placeholder="Enter name" style="width: 100%; padding: 12px; font-size: 16px; border: 2px solid #444; border-radius: 14px; background: #2a2a2a; color: #fff;">
      </div>
      <div style="flex: 1;">
        <label>No. of Guests</label>
        <div class="scroll-box" id="guest-select"></div>
      </div>
    </div>

    <!-- Date (moved below Guest Selection) -->
    <div class="section">
      <label>When are you visiting?</label>
      <div class="scroll-box" id="date-select"></div>
    </div>

    <!-- Lunch Time -->
    <div class="section time-section">
      <div class="time-header" onclick="toggleSection('lunch-grid', this)">
        <h3>☀️ Lunch <span style="font-weight: normal;">12:00 PM to 5:00 PM</span></h3>
        <span class="dropdown-arrow">⬇️</span>
      </div>
      <div class="time-grid" id="lunch-grid"></div>
    </div>

    <!-- Dinner Time -->
    <div class="section time-section">
      <div class="time-header" onclick="toggleSection('dinner-grid', this)">
        <h3>🌙 Dinner <span style="font-weight: normal;">5:00 PM to 11:00 PM</span></h3>
        <span class="dropdown-arrow">⬇️</span>
      </div>
      <div class="time-grid" id="dinner-grid"></div>
    </div>

    <!-- Table Selection -->
    <div class="section">
      <label>Table selection</label>
      <div class="scroll-box" id="table-select"></div>
    </div>

    <!-- Offer Selection -->
    <div class="section" style="margin-top: 20px;">
      <label>Select offer to proceed</label>
      <div style="background: #1e1e1e; padding: 20px; border-radius: 16px; margin-top: 10px;">
        <div style="margin-bottom: 15px;">
          <strong style="color: #aaa;">REGULAR OFFER</strong>
        </div>
        <div style="display: flex; flex-direction: column; gap: 15px;">
          <!-- Standard Table Booking -->
          <div style="display: flex; align-items: center; background: #2a2a2a; padding: 15px; border-radius: 12px; cursor: pointer; border: 2px solid transparent;" onclick="selectOffer(this, 'standard')">
            <input type="radio" name="offer" id="standard-offer" style="margin-right: 15px; accent-color: #ff6a00;">
            <div>
              <strong style="color: #fff;">Standard table booking</strong>
              <div style="color: #aaa; font-size: 14px;">Booking Fee: FREE</div>
            </div>
          </div>
          <!-- VIP Table Booking -->
          <div style="display: flex; align-items: center; background: #2a2a2a; padding: 15px; border-radius: 12px; cursor: pointer; border: 2px solid transparent;" onclick="selectOffer(this, 'vip')">
            <input type="radio" name="offer" id="vip-offer" style="margin-right: 15px; accent-color: #ff6a00;">
            <div>
              <strong style="color: #fff;">VIP table booking</strong>
              <div style="color: #aaa; font-size: 14px;">Pre-menu order required</div>
            </div>
          </div>
        </div>
        <div style="margin-top: 15px; color: #aaa; font-size: 14px;">
          Coupons & additional offers available during bill payment
        </div>
      </div>
    </div>

    <!-- VIP Menu Order Section (hidden by default) -->
    <div id="vip-menu-order" style="display:none; margin-bottom:30px;">
      <section style="background:#1e1e1e; padding:20px; border-radius:16px;">
        <h2 style="color:#fff; text-align:center; margin-bottom:20px;">Pre-Order Menu (VIP Only)</h2>
        <div class="menu-container" style="max-height:300px; overflow-y: auto;">
          <div class="blue msg" style="display: flex; gap: 20px;">
            <div class="mainDish" style="flex: 1;">
              <h1 style="text-align:center">MAIN DISHES</h1>
              <?php if(isset($mainDishes) && is_array($mainDishes) && count($mainDishes) > 0): ?>
                <?php foreach ($mainDishes as $item): ?>
                  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <div>
                      <span class="item-name" style="color:#fff;"><strong><?php echo $item['item_name']; ?></strong></span>
                      <span class="item-price" style="color:#ff6a00;">Rs.<?php echo $item['item_price']; ?></span><br>
                      <span class="item_type" style="color:#aaa; font-size:13px;"><i><?php echo $item['item_type']; ?></i></span>
                    </div>
                    <div>
                      <button class="add-btn" onclick="addToCart(this)" style="background:#1e1e1e; color:#00ffcc; border:none; padding:5px 10px; border-radius:5px; cursor:pointer;">ADD</button>
                      <div class="counter" style="display:none; align-items:center; gap:5px;">
                        <button onclick="decrement(this)" style="background:#1e1e1e; color:#00ffcc; border:none; padding:5px 10px; border-radius:5px; cursor:pointer;">-</button>
                        <span style="color:#00ffcc;">0</span>
                        <button onclick="increment(this)" style="background:#1e1e1e; color:#00ffcc; border:none; padding:5px 10px; border-radius:5px; cursor:pointer;">+</button>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <p style="color:#fff;">No main dishes available.</p>
              <?php endif; ?>
            </div>
            <div class="sideDish" style="flex: 1;">
              <h1 style="text-align:center">SIDE DISHES</h1>
              <?php if(isset($sides) && is_array($sides) && count($sides) > 0): ?>
                <?php foreach ($sides as $item): ?>
                  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <div>
                      <span class="item-name" style="color:#fff;"><strong><?php echo $item['item_name']; ?></strong></span>
                      <span class="item-price" style="color:#ff6a00;">Rs.<?php echo $item['item_price']; ?></span><br>
                      <span class="item_type" style="color:#aaa; font-size:13px;"><i><?php echo $item['item_type']; ?></i></span>
                    </div>
                    <div>
                      <button class="add-btn" onclick="addToCart(this)" style="background:#1e1e1e; color:#00ffcc; border:none; padding:5px 10px; border-radius:5px; cursor:pointer;">ADD</button>
                      <div class="counter" style="display:none; align-items:center; gap:5px;">
                        <button onclick="decrement(this)" style="background:#1e1e1e; color:#00ffcc; border:none; padding:5px 10px; border-radius:5px; cursor:pointer;">-</button>
                        <span style="color:#00ffcc;">0</span>
                        <button onclick="increment(this)" style="background:#1e1e1e; color:#00ffcc; border:none; padding:5px 10px; border-radius:5px; cursor:pointer;">+</button>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <p style="color:#fff;">No side dishes available.</p>
              <?php endif; ?>
            </div>
            <div class="drinks" style="flex: 1;">
              <h1 style="text-align:center">DRINKS</h1>
              <?php if(isset($drinks) && is_array($drinks) && count($drinks) > 0): ?>
                <?php foreach ($drinks as $item): ?>
                  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <div>
                      <span class="item-name" style="color:#fff;"><strong><?php echo $item['item_name']; ?></strong></span>
                      <span class="item-price" style="color:#ff6a00;">Rs.<?php echo $item['item_price']; ?></span><br>
                      <span class="item_type" style="color:#aaa; font-size:13px;"><i><?php echo $item['item_type']; ?></i></span>
                    </div>
                    <div>
                      <button class="add-btn" onclick="addToCart(this)" style="background:#1e1e1e; color:#00ffcc; border:none; padding:5px 10px; border-radius:5px; cursor:pointer;">ADD</button>
                      <div class="counter" style="display:none; align-items:center; gap:5px;">
                        <button onclick="decrement(this)" style="background:#1e1e1e; color:#00ffcc; border:none; padding:5px 10px; border-radius:5px; cursor:pointer;">-</button>
                        <span style="color:#00ffcc;">0</span>
                        <button onclick="increment(this)" style="background:#1e1e1e; color:#00ffcc; border:none; padding:5px 10px; border-radius:5px; cursor:pointer;">+</button>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <p style="color:#fff;">No drinks available.</p>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </section>
    </div>

    <button class="proceed" onclick="handleProceed()">Proceed</button>
  </div>

  <!-- Added Reservation Overlay -->
  <div id="reservationOverlay">
    <a href="http://localhost/restaurant-management/customerSide/home/home.php">
      <img src="logo.png" alt="Logo">
    </a>
    <h1>Reservation is done</h1>
  </div>

  <script>
    // Populate date selector with next 20 days
    const dateSelect = document.getElementById("date-select");
    const today = new Date();
    for (let i = 0; i < 20; i++) { // list next 20 days
        const date = new Date();
        date.setDate(today.getDate() + i);
        const btn = document.createElement("button");
        const day = date.toLocaleDateString('en-US', { weekday: 'short' });
        const formattedDate = date.toLocaleDateString('en-US', { day: '2-digit', month: 'short' });
        btn.textContent = `${day} ${formattedDate}`;
        // Added: set data attribute for date in yyyy-mm-dd format
        btn.dataset.date = date.toISOString().split('T')[0];
        btn.onclick = () => {
            if (btn.classList.contains("active")) {
                btn.classList.remove("active");
            } else {
                document.querySelectorAll("#date-select button").forEach(b => b.classList.remove("active"));
                btn.classList.add("active");
            }
            updateTimeSlotsAvailability(); // update time slot availability based on system time
            updateProceedButton(); // Update proceed button
            checkAvailability(); // Check table availability
        };
        dateSelect.appendChild(btn);
    }

    // Populate guest selector with numbers 1 to 10 and update table selection accordingly
    const guestSelect = document.getElementById("guest-select");
    for (let i = 1; i <= 10; i++) {
        const btn = document.createElement("button");
        btn.textContent = i;
        btn.onclick = () => {
            if (btn.classList.contains("active")) {
                btn.classList.remove("active");
                updateTableSelection();
            } else {
                guestSelect.querySelectorAll("button").forEach(b => b.classList.remove("active"));
                btn.classList.add("active");
                updateTableSelection();
            }
            updateProceedButton(); // Update proceed button
            checkAvailability(); // Check table availability
        };
        guestSelect.appendChild(btn);
    }

    // Draggable functionality function
    function addDraggable(el) {
      let isDown = false, startX, scrollLeft;
      el.addEventListener('mousedown', (e) => {
          isDown = true;
          el.style.cursor = "grabbing";
          startX = e.pageX - el.offsetLeft;
          scrollLeft = el.scrollLeft;
      });
      el.addEventListener('mouseleave', () => {
          isDown = false;
          el.style.cursor = "grab";
      });
      el.addEventListener('mouseup', () => {
          isDown = false;
          el.style.cursor = "grab";
      });
      el.addEventListener('mousemove', (e) => {
          if (!isDown) return;
          e.preventDefault();
          const x = e.pageX - el.offsetLeft;
          const walk = (x - startX) * 2; // adjust scroll speed
          el.scrollLeft = scrollLeft - walk;
      });
    }

    addDraggable(dateSelect);
    addDraggable(guestSelect);

    // Generate time slots
    function generateSlots(start, end) {
      const slots = [];
      let current = new Date();
      const [startHour, startMinute] = start.split(':').map(Number);
      const [endHour, endMinute] = end.split(':').map(Number);

      current.setHours(startHour, startMinute, 0, 0);
      const endTime = new Date();
      endTime.setHours(endHour, endMinute, 0, 0);

      while (current <= endTime) {
        slots.push(current.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }));
        current.setMinutes(current.getMinutes() + 15);
      }
      return slots;
    }

    const lunchSlots = generateSlots("12:00", "17:00");
    const dinnerSlots = generateSlots("17:00", "23:00");

    const lunchGrid = document.getElementById("lunch-grid");
    const dinnerGrid = document.getElementById("dinner-grid");

    lunchSlots.forEach(time => {
      const btn = document.createElement("button");
      btn.textContent = time;
      btn.onclick = () => {
        if (btn.classList.contains("active")) {
          btn.classList.remove("active");
          let lunchHeader = lunchGrid.previousElementSibling;
          lunchHeader.querySelector("h3").innerHTML = "☀️ Lunch <span style=\"font-weight: normal;\">12:00 PM to 5:00 PM</span>";
        } else {
          lunchGrid.querySelectorAll("button").forEach(b => b.classList.remove("active"));
          btn.classList.add("active");
          // Remove selection from dinner group and reset its header
          dinnerGrid.querySelectorAll("button").forEach(b => b.classList.remove("active"));
          let dinnerHeader = dinnerGrid.previousElementSibling;
          dinnerHeader.querySelector("h3").innerHTML = "🌙 Dinner <span style=\"font-weight: normal;\">5:00 PM to 11:00 PM</span>";
          // Update lunch header with the selected time slot
          let lunchHeader = lunchGrid.previousElementSibling;
          lunchHeader.querySelector("h3").innerHTML = "☀️ Lunch (Selected: " + time + ")";
          closeAllTimeSections();
        }
        updateProceedButton(); // Update proceed button
        checkAvailability(); // Check table availability
      };
      lunchGrid.appendChild(btn);
    });

    dinnerSlots.forEach(time => {
      const btn = document.createElement("button");
      btn.textContent = time;
      btn.onclick = () => {
        if (btn.classList.contains("active")) {
          btn.classList.remove("active");
          let dinnerHeader = dinnerGrid.previousElementSibling;
          dinnerHeader.querySelector("h3").innerHTML = "🌙 Dinner <span style=\"font-weight: normal;\">5:00 PM to 11:00 PM</span>";
        } else {
          dinnerGrid.querySelectorAll("button").forEach(b => b.classList.remove("active"));
          btn.classList.add("active");
          // Remove selection from lunch group and reset its header
          lunchGrid.querySelectorAll("button").forEach(b => b.classList.remove("active"));
          let lunchHeader = lunchGrid.previousElementSibling;
          lunchHeader.querySelector("h3").innerHTML = "☀️ Lunch <span style=\"font-weight: normal;\">12:00 PM to 5:00 PM</span>";
          // Update dinner header with the selected time slot
          let dinnerHeader = dinnerGrid.previousElementSibling;
          dinnerHeader.querySelector("h3").innerHTML = "🌙 Dinner (Selected: " + time + ")";
          closeAllTimeSections();
        }
        updateProceedButton(); // Update proceed button
        checkAvailability(); // Check table availability
      };
      dinnerGrid.appendChild(btn);
    });

    function closeAllTimeSections() {
      const lg = document.getElementById("lunch-grid");
      const dg = document.getElementById("dinner-grid");
      lg.style.display = "none";
      dg.style.display = "none";
      // Reset dropdown arrows
      lg.previousElementSibling.querySelector(".dropdown-arrow").textContent = "⬇️";
      dg.previousElementSibling.querySelector(".dropdown-arrow").textContent = "⬇️";
    }

    // Table options with capacity property
    const tableOptions = [
      { id: 1, label: "4 people (Id: 1)", capacity: 4 },
      { id: 2, label: "4 people (Id: 2)", capacity: 4 },
      { id: 3, label: "4 people (Id: 3)", capacity: 4 },
      { id: 4, label: "6 people (Id: 4)", capacity: 6 },
      { id: 5, label: "6 people (Id: 5)", capacity: 6 },
      { id: 6, label: "6 people (Id: 6)", capacity: 6 },
      { id: 7, label: "8 people (Id: 7)", capacity: 8 },
      { id: 8, label: "8 people (Id: 8)", capacity: 8 },
      { id: 9, label: "10 people (Id: 9)", capacity: 10 },
      { id: 10, label: "10 people (Id: 10)", capacity: 10 }
    ];

    const tableSelect = document.getElementById("table-select");

    // Function to update table selection based on selected guest count
    function updateTableSelection() {
      const selectedGuestBtn = document.querySelector("#guest-select button.active");
      tableSelect.innerHTML = ""; // Clear previous options
      if (!selectedGuestBtn) {
        // Show all table options if no guest is selected
        tableOptions.forEach(option => {
          const btn = document.createElement("button");
          btn.textContent = option.label;
          btn.onclick = () => {
              if (btn.classList.contains("active")) {
                  btn.classList.remove("active");
              } else {
                  tableSelect.querySelectorAll("button").forEach(b => b.classList.remove("active"));
                  btn.classList.add("active");
              }
              updateProceedButton(); // Update proceed button
          };
          tableSelect.appendChild(btn);
        });
      } else {
        const guestCount = parseInt(selectedGuestBtn.textContent);
        tableOptions.forEach(option => {
          if (option.capacity >= guestCount) {
            const btn = document.createElement("button");
            btn.textContent = option.label;
            btn.onclick = () => {
              if (btn.classList.contains("active")) {
                  btn.classList.remove("active");
              } else {
                  tableSelect.querySelectorAll("button").forEach(b => b.classList.remove("active"));
                  btn.classList.add("active");
              }
              updateProceedButton(); // Update proceed button
            };
            tableSelect.appendChild(btn);
          }
        });
      }
    }

    // Collapsible section
    function toggleSection(id, header) {
      const grid = document.getElementById(id);
      const arrow = header.querySelector('.dropdown-arrow');
      if (grid.style.display === "none" || !grid.style.display) {
        grid.style.display = "grid";
        arrow.textContent = "⬆️";
      } else {
        grid.style.display = "none";
        arrow.textContent = "⬇️";
      }
    }

    // Collapse all time slots initially
    document.getElementById("lunch-grid").style.display = "none";
    document.getElementById("dinner-grid").style.display = "none";

    // Call updateTableSelection initially to show all tables
    updateTableSelection();

    // Function to handle offer selection
    function selectOffer(element, offerType) {
      document.querySelectorAll("[name='offer']").forEach(radio => {
        radio.parentElement.style.borderColor = "transparent"; // Reset border
      });
      element.style.borderColor = "#ff6a00"; // Highlight selected offer
      document.getElementById(`${offerType}-offer`).checked = true; // Check the radio button
      if (offerType === 'vip'){
        document.getElementById("vip-menu-order").style.display = "block";
      } else {
        document.getElementById("vip-menu-order").style.display = "none";
      }
    }

    function addToCart(button) {
      const parent = button.parentElement;
      button.style.display = 'none'; // Hide the "ADD" button
      const counter = parent.querySelector('.counter');
      counter.style.display = 'flex'; // Show the counter
      counter.querySelector('span').textContent = '1'; // Set initial count to 1
    }

    function increment(button) {
      const counter = button.parentElement;
      const countSpan = counter.querySelector('span');
      let count = parseInt(countSpan.textContent);
      countSpan.textContent = count + 1; // Increment the count
    }

    function decrement(button) {
      const counter = button.parentElement;
      const countSpan = counter.querySelector('span');
      let count = parseInt(countSpan.textContent);
      if (count > 1) {
        countSpan.textContent = count - 1; // Decrement the count
      } else {
        // If count reaches 0, hide the counter and show the "ADD" button
        counter.style.display = 'none';
        const addButton = counter.parentElement.querySelector('.add-btn');
        addButton.style.display = 'inline-block';
      }
    }

    // Add new function to update time slot availability based on current system time if today is selected
    function updateTimeSlotsAvailability() {
        const selected = document.querySelector("#date-select button.active");
        const todayStr = new Date().toISOString().split('T')[0];
        if (selected && selected.dataset.date === todayStr) {
            const now = new Date();
            const nowMinutes = now.getHours() * 60 + now.getMinutes();
            document.querySelectorAll("#lunch-grid button, #dinner-grid button").forEach(btn => {
                const match = btn.textContent.match(/(\d{1,2}):(\d{2})\s*(AM|PM)/i);
                if(match) {
                    let hr = parseInt(match[1]);
                    const min = parseInt(match[2]);
                    const period = match[3].toUpperCase();
                    if (period === "PM" && hr !== 12) hr += 12;
                    if (period === "AM" && hr === 12) hr = 0;
                    const slotMinutes = hr * 60 + min;
                    if (slotMinutes <= nowMinutes) {
                        btn.disabled = true;
                        btn.style.background = "#888";  // greyed out
                        btn.style.color = "#ccc";
                        btn.style.cursor = "not-allowed";
                    } else {
                        btn.disabled = false;
                        btn.style.background = "#1e1e1e";
                        btn.style.color = "#fff";
                        btn.style.cursor = "pointer";
                    }
                }
            });
        } else {
            document.querySelectorAll("#lunch-grid button, #dinner-grid button").forEach(btn => {
                btn.disabled = false;
                btn.style.background = "#1e1e1e";
                btn.style.color = "#fff";
                btn.style.cursor = "pointer";
            });
        }
    }

    // Add new function to update the proceed button state
    function updateProceedButton() {
        const customerName = document.getElementById("customer-name").value.trim();
        const guestSelected = document.querySelector("#guest-select button.active");
        const dateSelected = document.querySelector("#date-select button.active");
        const timeSelected = document.querySelector("#lunch-grid button.active, #dinner-grid button.active");
        const tableSelected = document.querySelector("#table-select button.active");

        const proceedButton = document.querySelector(".proceed");
        if (customerName && guestSelected && dateSelected && timeSelected && tableSelected) {
            proceedButton.style.background = "#ff6a00";
            proceedButton.style.cursor = "pointer";
        } else {
            proceedButton.style.background = "#888";
            proceedButton.style.cursor = "not-allowed";
        }
    }

    // Modified handleProceed() to display overlay, trigger print dialog, and process redirection
    function handleProceed(){
        const customerName = document.getElementById("customer-name").value.trim();
        const guestEl = document.querySelector("#guest-select button.active");
        const dateEl = document.querySelector("#date-select button.active");
        const timeEl = document.querySelector("#lunch-grid button.active, #dinner-grid button.active");
        const tableEl = document.querySelector("#table-select button.active");
        if (!(customerName && guestEl && dateEl && timeEl && tableEl)) {
            return;
        }
        // Build URL for processing reservation on the same page
        const guestCount = guestEl.textContent;
        const reservationDate = dateEl.dataset.date;
        const reservationTime = timeEl.textContent;
        const tableMatch = tableEl.textContent.match(/\(Id:\s*(\d+)\)/);
        const tableId = tableMatch ? tableMatch[1] : '';
        let preordered = [];
        if(document.getElementById("vip-menu-order").style.display !== "none"){
            document.querySelectorAll("#vip-menu-order .menu-container .mainDish > div, #vip-menu-order .menu-container .sideDish > div, #vip-menu-order .menu-container .drinks > div").forEach(container => {
                const counterEl = container.querySelector(".counter");
                if(counterEl && counterEl.style.display !== "none"){
                    let qtyEl = counterEl.querySelector("span");
                    let quantity = parseInt(qtyEl.textContent);
                    if(quantity > 0){
                        const nameEl = container.querySelector(".item-name strong");
                        const priceEl = container.querySelector(".item-price");
                        if(nameEl && priceEl){
                            let itemName = nameEl.textContent;
                            let itemPrice = parseFloat(priceEl.textContent.replace("Rs.",""));
                            preordered.push({item_name: itemName, quantity: quantity, item_price: itemPrice});
                        }
                    }
                }
            });
        }
        const url = new URL("http://localhost/restaurant-management/adminSide/panel/reservation-panel.php");
        url.searchParams.set("process_reservation", "1");
        url.searchParams.set("customer_name", customerName);
        url.searchParams.set("head_count", guestCount);
        url.searchParams.set("reservation_date", reservationDate);
        url.searchParams.set("reservation_time", reservationTime);
        url.searchParams.set("table_id", tableId);
        url.searchParams.set("preordered", JSON.stringify(preordered));
        
        // Show reservation overlay immediately
        document.getElementById("reservationOverlay").classList.add("active");
        
        // Open the receipt printer page (reservationReceipt.php) after 1000ms
        setTimeout(() => {
            const printUrl = new URL("http://localhost/restaurant-management/customerSide/CustomerReservation/reservationReceipt.php");
            printUrl.searchParams.set("customer_name", customerName);
            printUrl.searchParams.set("reservation_date", reservationDate);
            printUrl.searchParams.set("reservation_time", reservationTime);
            printUrl.searchParams.set("table_id", tableId);
            printUrl.searchParams.set("preordered", JSON.stringify(preordered));
            
            window.open(printUrl.toString(), '_blank');
        }, 1000);
        
        // Overlay timer (hide overlay after 2000ms)
        setTimeout(() => {
            document.getElementById("reservationOverlay").classList.remove("active");
        }, 4000);
        
        // Separate redirection timer (redirect after 4000ms)
        setTimeout(() => {
            window.location.href = url.toString();
        }, 4000);
    }

    // New function to check table availability and disable occupied tables
    function checkAvailability() {
        const dateEl = document.querySelector("#date-select button.active");
        const guestEl = document.querySelector("#guest-select button.active");
        const timeEl = document.querySelector("#lunch-grid button.active, #dinner-grid button.active");
        if (!dateEl || !guestEl || !timeEl) return;
        
        const reservation_date = dateEl.dataset.date;
        const head_count = guestEl.textContent;
        const reservation_time = timeEl.textContent;
        
        fetch(`availability.php?ajax=1&reservation_date=${reservation_date}&head_count=${head_count}&reservation_time=${reservation_time}`)
          .then(response => response.json())
          .then(data => {
            // Disable table buttons that have a reserved table ID
            document.querySelectorAll("#table-select button").forEach(btn => {
                const match = btn.textContent.match(/\(Id:\s*(\d+)\)/);
                if (match) {
                    const tableId = match[1];
                    if (data.reserved.includes(tableId)) {
                        btn.disabled = true;
                        btn.style.background = "#888";
                        btn.style.cursor = "not-allowed";
                    } else {
                        btn.disabled = false;
                        btn.style.background = "";
                        btn.style.cursor = "pointer";
                    }
                }
            });
          });
    }
  </script>
</body>
</html>
