<?php
include 'conn.php'; // $conn

// Active Products: items where isOver = 0
$sqlActiveProducts = "SELECT COUNT(*) AS active_count FROM items WHERE isOver = 0";
$resActiveProducts = $conn->query($sqlActiveProducts);
$activeCount = 0;
if ($resActiveProducts) {
  $row = $resActiveProducts->fetch_assoc();
  $activeCount = (int)$row['active_count'];
}

// Pending Orders: distinct referenceno where orderStatus = 0
$sqlPending = "SELECT COUNT(DISTINCT referenceno) AS pending_count FROM receipt WHERE orderStatus = 0";
$resPending = $conn->query($sqlPending);
$pendingCount = 0;
if ($resPending) {
  $row = $resPending->fetch_assoc();
  $pendingCount = (int)$row['pending_count'];
}

// Active Accounts: users where isActive = 1 AND isAdmin = 0
$sqlAccounts = "SELECT COUNT(*) AS acc_count FROM users WHERE isActive = 1 AND isAdmin = 0";
$resAccounts = $conn->query($sqlAccounts);
$accountsCount = 0;
if ($resAccounts) {
  $row = $resAccounts->fetch_assoc();
  $accountsCount = (int)$row['acc_count'];
}

// Total Products Offered: all items
$sqlProducts = "SELECT COUNT(*) AS prod_count FROM items";
$resProducts = $conn->query($sqlProducts);
$totalProducts = 0;
if ($resProducts) {
  $row = $resProducts->fetch_assoc();
  $totalProducts = (int)$row['prod_count'];
}

// Total Sales: SUM of tPrice from receipt
$sqlSales = "SELECT COALESCE(SUM(tPrice),0) AS total_sales FROM receipt";
$resSales = $conn->query($sqlSales);
$totalSales = 0;
if ($resSales) {
  $row = $resSales->fetch_assoc();
  $totalSales = (float)$row['total_sales'];
}

// Delivered: count distinct referenceno where orderStatus != 0 (count each referenceno once)
$sqlDelivered = "SELECT COUNT(DISTINCT referenceno) AS delivered_count FROM receipt WHERE orderStatus <> 0";
$resDelivered = $conn->query($sqlDelivered);
$deliveredCount = 0;
if ($resDelivered) {
  $row = $resDelivered->fetch_assoc();
  $deliveredCount = (int)$row['delivered_count'];
}

// Items list (left column) – top 10 active items, include tSold, tSales, inventory
$sqlTop  = "SELECT itemID, itemName, description, price, tSold, tSales, inventory
            FROM items 
            WHERE isOver = 0 
            ORDER BY itemID DESC 
            LIMIT 10";
$resultItems = $conn->query($sqlTop);

// All active items (hidden list) - limited to 10 per your request
$sqlAll  = "SELECT itemID, itemName, description, price, tSold, tSales, inventory
            FROM items 
            WHERE isOver = 0 
            ORDER BY itemID DESC
            LIMIT 10";
$resultAllItems = $conn->query($sqlAll);

// Orders list – group by referenceno, limited to 10 each

// Pending orders (orderStatus = 0)
$sqlPendingOrders = "
    SELECT r.referenceno,
           u.firstName,
           u.lastName,
           GROUP_CONCAT(i.itemName SEPARATOR ', ') AS items,
           SUM(r.tPrice) AS total_price
    FROM receipt r
    INNER JOIN users u ON r.userID = u.userID
    INNER JOIN items i ON r.itemID = i.itemID
    WHERE r.orderStatus = 0
    GROUP BY r.referenceno, u.firstName, u.lastName
    ORDER BY MIN(r.receiptID) DESC
    LIMIT 10
";
$resultPendingOrders = $conn->query($sqlPendingOrders);

// Order history (orderStatus != 0)
$sqlHistoryOrders = "
    SELECT r.referenceno,
           u.firstName,
           u.lastName,
           GROUP_CONCAT(i.itemName SEPARATOR ', ') AS items,
           SUM(r.tPrice) AS total_price
    FROM receipt r
    INNER JOIN users u ON r.userID = u.userID
    INNER JOIN items i ON r.itemID = i.itemID
    WHERE r.orderStatus <> 0
    GROUP BY r.referenceno, u.firstName, u.lastName
    ORDER BY MIN(r.receiptID) DESC
    LIMIT 10
";
$resultHistoryOrders = $conn->query($sqlHistoryOrders);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="icon" href="./media/icon.png" type="icon.png" />
  <title>QUALITEES | Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    @import url('./media/stardom.css');

    body {
      background-color: #fff;
      color: #111;
      font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    }

    .dash-wrapper {
      max-width: 1320px;
      margin: 32px auto 40px auto;
      padding: 0 32px;
    }

    .dash-title {
      font-family: 'stardom', serif;
      font-size: 2.4rem;
      font-weight: 600;
      letter-spacing: 1px;
    }

    .dash-divider {
      border-top: 1px solid #e0e0e0;
      margin-top: 8px;
      margin-bottom: 32px;
    }

    .section-label {
      font-size: 0.95rem;
      color: #555;
      margin-bottom: 2px;
    }

    .section-number {
      font-weight: 700;
      font-size: 1.1rem;
      margin-bottom: 12px;
    }

    .thin-divider {
      border-top: 1px solid #e0e0e0;
      margin: 10px 0 30px 0;
    }

    .all-chip {
      border-radius: 999px;
      border: 1px solid #aaa;
      padding: 1px 10px;
      font-size: 0.75rem;
      background-color: #fff;
      cursor: pointer;
      transition: all 0.2s ease;
    }

    .item-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 4px;
    }

    .item-name {
      font-weight: 600;
      font-size: 0.95rem;
    }

    .item-price {
      font-size: 0.9rem;
      color: #444;
    }

    .view-more {
      font-size: 0.8rem;
      color: #999;
      cursor: pointer;
      text-decoration: none;
      transition: color 0.2s ease;
    }

    .item-block {
      background-color: #fff;
      border-radius: 12px;
      padding: 16px;
      margin-bottom: 50px;
      transition: box-shadow 0.3s ease, transform 0.3s ease;
      cursor: pointer;
    }

    .item-block:hover {
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
      transform: translateY(-4px);
    }

    .stat-block-right {
      text-align: right;
      margin-bottom: 16px;
    }

    .stat-label-right {
      font-size: 0.9rem;
      color: #555;
    }

    .stat-number-right {
      font-weight: 700;
      font-size: 1.05rem;
    }

    .btn-add-product {
      border-radius: 10px;
      padding: 0.7rem 2.4rem;
      font-weight: 600;
      letter-spacing: 0.04em;
      font-size: 0.95rem;
      border-color: #111;
      color: #111;
      background-color: #fff;
      transition: all 0.2s ease;
    }

    .exit-btn {
      border: none;
      background: transparent;
      font-size: 1.4rem;
      line-height: 1;
      cursor: pointer;
      text-decoration: none;
      color: #111;
      padding: 4px 10px;
      border-radius: 999px;
      transition: all 0.2s ease;
    }

    .all-chip:hover,
    .btn-add-product:hover,
    .btn-add-product:focus,
    .exit-btn:hover,
    .exit-btn:focus {
      background-color: #b33939;
      border-color: #b33939;
      color: #fff;
    }

    .all-chip.active {
      background-color: #000 !important;
      color: #fff !important;
      border-color: #000 !important;
    }

    .all-chip.active:hover {
      background-color: #b33939 !important;
      border-color: #b33939 !important;
      color: #fff !important;
    }

    .view-more:hover {
      color: #b33939;
    }

    @media (max-width: 991px) {
      .dash-wrapper {
        padding: 0 20px;
      }
    }
  </style>
</head>

<body>
  <div class="dash-wrapper">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center">
      <div class="dash-title">Qualitees Dashboard</div>
      <a href="logout.php" class="exit-btn" aria-label="Logout">&rarr;</a>
    </div>
    <div class="dash-divider"></div>

    <!-- Top headings row -->
    <div class="row mb-2">
      <div class="col-lg-4 col-md-5">
        <div class="section-label">Active Products</div>
        <div class="section-number"><?php echo $activeCount; ?></div>
      </div>
      <div class="col-lg-4 col-md-4">
        <div class="section-label">Pending Orders</div>
        <div class="section-number"><?php echo $pendingCount; ?></div>
      </div>
      <div class="col-lg-4 col-md-3 text-lg-end text-md-end text-start">
        <div class="section-label">Active Accounts</div>
        <div class="section-number"><?php echo $accountsCount; ?></div>
      </div>
    </div>

    <!-- Middle row thin dividers + stats -->
    <div class="row align-items-center mb-2">
      <div class="col-lg-4 col-md-5">
        <div class="thin-divider" style="margin:4px 0 6px 0;"></div>
      </div>
      <div class="col-lg-4 col-md-4">
        <div class="thin-divider" style="margin:4px 0 6px 0;"></div>
      </div>
      <div class="col-lg-4 col-md-3">
        <div class="stat-block-right" style="margin-bottom:10px;">
          <div class="stat-label-right">Total Products Offered</div>
          <div class="stat-number-right"><?php echo $totalProducts; ?></div>
        </div>
        <div class="stat-block-right" style="margin-bottom:6px;">
          <div class="stat-label-right">Total Sales</div>
          <div class="stat-number-right">₱<?php echo number_format($totalSales, 2); ?></div>
        </div>
        <div class="stat-block-right" style="margin-bottom:0;">
          <div class="stat-label-right">Delivered</div>
          <div class="stat-number-right"><?php echo $deliveredCount; ?></div>
        </div>
      </div>
    </div>

    <!-- Items lists + Orders + Add product -->
    <div class="row align-items-start">
      <!-- Items List (no media) -->
      <div class="col-lg-4 col-md-5 mb-4">
        <div class="item-header">
          <div class="section-label mb-0">Items List</div>
          <a href="./itemsort.php"><button id="all-items-btn" class="all-chip" type="button">All</button></a>
        </div>
        <div class="thin-divider"></div>

        <div id="items-list">
          <?php
          if ($resultItems && $resultItems->num_rows > 0) {
            while ($item = $resultItems->fetch_assoc()) {
              $itemID = (int)$item['itemID'];
              $name  = htmlspecialchars(mb_strimwidth($item['itemName'], 0, 50, '...'));
              $price = number_format($item['price'], 2);

              // tSold, tSales, inventory -> compute stock
              $tSold_raw = (int)$item['tSold'];
              $tSales_raw = (float)$item['tSales'];
              $inventory_raw = (int)$item['inventory'];
              $stock_calc = $inventory_raw - $tSold_raw;
              if ($stock_calc < 0) $stock_calc = 0;

              // Format values
              $tSold_display = number_format($tSold_raw); // integer representation
              $tSales_display = number_format($tSales_raw, 2);

              // Make whole block clickable to productpage.php?id=...
              echo '<div class="item-block" onclick="location.href=\'productpage.php?id=' . $itemID . '\'">';
              echo '<div class="item-name">' . $name . '</div>';
              echo '<div style="margin-top:6px;">';
              echo '<div class="item-price">₱' . $price . '</div>';
              echo '</div>';
              echo '<div style="margin-top:8px; text-align:right; font-size:0.85rem; color:#555;">'

                . 'T Sales: ₱' . $tSales_display . ' | '
                . 'T Sold: ' . $tSold_display . ' | '
                . 'T Stock: ' . $stock_calc
                . '</div>';
              echo '</div>';
            }
          } else {
            echo '<div>No active items found.</div>';
          }
          ?>
        </div>

        <!-- idk how to remove it without messing up the structure of html-->
        <div id="all-items-list" style="display:none;">
          <?php
          if ($resultAllItems && $resultAllItems->num_rows > 0) {
            while ($item = $resultAllItems->fetch_assoc()) {
              $itemID = (int)$item['itemID'];
              $name  = htmlspecialchars(mb_strimwidth($item['itemName'], 0, 50, '...'));
              $price = number_format($item['price'], 2);

              // tSold, tSales, inventory -> compute stock
              $tSold_raw = (int)$item['tSold'];
              $tSales_raw = (float)$item['tSales'];
              $inventory_raw = (int)$item['inventory'];
              $stock_calc = $inventory_raw - $tSold_raw;
              if ($stock_calc < 0) $stock_calc = 0;

              // Format values
              $tSold_display = number_format($tSold_raw); // integer representation
              $tSales_display = number_format($tSales_raw, 2);

              // Make whole block clickable to productpage.php?id=...
              echo '<div class="item-block" onclick="location.href=\'productpage.php?id=' . $itemID . '\'">';
              echo '<div class="item-name">' . $name . '</div>';
              echo '<div style="margin-top:6px;">';
              echo '<div class="item-price">₱' . $price . '</div>';
              echo '</div>';
              echo '<div style="margin-top:8px; text-align:right; font-size:0.85rem; color:#555;">'
                . '₱' . $tSold_display . ' | '
                . 'Sales: ₱' . $tSales_display . ' | '
                . 'Stock: ' . $stock_calc
                . '</div>';
              echo '</div>';
            }
          } else {
            echo '<div>No active items found.</div>';
          }
          ?>
        </div>
      </div>

      <!-- All Orders: Pending / History -->
      <div class="col-lg-4 col-md-4 mb-4">
        <div class="item-header">
          <div class="section-label mb-0">All Orders</div>
          <div>
            <button id="btn-pending" class="all-chip active" type="button">Pending</button>
            <button id="btn-history" class="all-chip" type="button">History</button>
            <a href="./index.php"><button id="btn-all-orders" class="all-chip" type="button">All</button></a>
          </div>
        </div>
        <div class="thin-divider"></div>

        <!-- Pending Orders -->
        <div id="orders-pending">
          <?php
          if ($resultPendingOrders && $resultPendingOrders->num_rows > 0) {
            while ($row = $resultPendingOrders->fetch_assoc()) {
              $customer = htmlspecialchars($row['firstName'] . ' ' . $row['lastName']);
              $itemsStr = htmlspecialchars($row['items']);
              $total    = number_format($row['total_price'], 2);
              $ref = htmlspecialchars($row['referenceno']);
              // Whole block clickable to receiptxsummary.php?ref=...
              echo '<div class="item-block" onclick="location.href=\'receiptxsummary.php?ref=' . urlencode($ref) . '\'">';
              echo '<div class="item-name">' . $customer . '</div>';
              echo '<div class="item-price" style="font-size:0.85rem; color:#666;">' . $itemsStr . '</div>';
              echo '<div class="d-flex justify-content-between align-items-center" style="margin-top:6px;">';
              echo '<div class="item-price">₱' . $total . '</div>';
              echo '<div class="view-more">' . htmlspecialchars($ref) . '</div>';
              echo '</div></div>';
            }
          } else {
            echo '<div>No pending orders.</div>';
          }
          ?>
        </div>

        <!-- Order History -->
        <div id="orders-history" style="display:none;">
          <?php
          if ($resultHistoryOrders && $resultHistoryOrders->num_rows > 0) {
            while ($row = $resultHistoryOrders->fetch_assoc()) {
              $customer = htmlspecialchars($row['firstName'] . ' ' . $row['lastName']);
              $itemsStr = htmlspecialchars($row['items']);
              $total    = number_format($row['total_price'], 2);
              $ref = htmlspecialchars($row['referenceno']);
              // Whole block clickable to receiptxsummary.php?ref=...
              echo '<div class="item-block" onclick="location.href=\'receiptxsummary.php?ref=' . urlencode($ref) . '\'">';
              echo '<div class="item-name">' . $customer . '</div>';
              echo '<div class="item-price" style="font-size:0.85rem; color:#666;">' . $itemsStr . '</div>';
              echo '<div class="d-flex justify-content-between align-items-center" style="margin-top:6px;">';
              echo '<div class="item-price">₱' . $total . '</div>';
              echo '<div class="view-more">' . htmlspecialchars($ref) . '</div>';
              echo '</div></div>';
            }
          } else {
            echo '<div>No order history.</div>';
          }
          ?>
        </div>
      </div>

      <!-- Right: Add Product bottom-right -->
      <div class="col-lg-4 col-md-3 mb-4 d-flex flex-column justify-content-end">
        <div class="text-lg-end text-md-end text-start mt-4 pt-5">
          <a href="add_product.php" class="btn btn-add-product">Add Product+</a>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Items list toggle
    const allBtn = document.getElementById('all-items-btn');
    const topList = document.getElementById('items-list');
    const fullList = document.getElementById('all-items-list');

    allBtn.addEventListener('click', () => {
      if (allBtn.classList.contains('active')) {
        fullList.style.display = 'none';
        topList.style.display = 'block';
        allBtn.classList.remove('active');
      } else {
        fullList.style.display = 'block';
        topList.style.display = 'none';
        allBtn.classList.add('active');
      }
    });

    // Orders pending/history toggle
    const btnPending = document.getElementById('btn-pending');
    const btnHistory = document.getElementById('btn-history');
    const ordersPend = document.getElementById('orders-pending');
    const ordersHist = document.getElementById('orders-history');

    btnPending.addEventListener('click', () => {
      btnPending.classList.add('active');
      btnHistory.classList.remove('active');
      ordersPend.style.display = 'block';
      ordersHist.style.display = 'none';
    });

    btnHistory.addEventListener('click', () => {
      btnHistory.classList.add('active');
      btnPending.classList.remove('active');
      ordersPend.style.display = 'none';
      ordersHist.style.display = 'block';
    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>