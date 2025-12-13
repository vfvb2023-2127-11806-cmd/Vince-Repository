<?php
include 'conn.php';
include 'init.php';

$userID = $_SESSION['userID'];

function renderOrders($conn, $userID, $statusLabel, $orderStatus)
{
    // Get all orders for this user with given status
    $query = "SELECT referenceno, itemName, qty, tPrice, media
              FROM receipt
              JOIN items USING (itemID)
              WHERE userID = $userID AND orderStatus = $orderStatus
              ORDER BY referenceno DESC";

    $result = mysqli_query($conn, $query);

    // Group by referenceno
    $orders = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $ref = $row['referenceno'];
        if (!isset($orders[$ref])) {
            $orders[$ref] = ['items' => [], 'total' => 0];
        }
        $orders[$ref]['items'][] = $row;
        $orders[$ref]['total'] += $row['tPrice'];
    }

    echo '<section class="section"><h2 class="section-title">' . $statusLabel . '</h2>';

    foreach ($orders as $ref => $group) {
        echo '<div class="order-group" data-ref="' . htmlspecialchars($ref) . '">';

        foreach ($group['items'] as $item) {
            echo '
            <div class="item-entry">
                <div class="item-details">
                    <img src="' . htmlspecialchars($item['media']) . '" alt="item" class="item-img">
                    <div class="item-text">
                        <span class="item-name">' . htmlspecialchars($item['itemName']) . '</span>
                        <span class="item-x"> x </span>
                        <span class="item-qty">' . $item['qty'] . '</span>
                    </div>
                    <p class="item-amount">₱' . number_format($item['tPrice'], 2) . '</p>
                </div>
            </div>';
        }

        echo '
            <div class="ref-summary">
                <p class="text-muted">Reference No: ' . htmlspecialchars($ref) . '</p>
                <p class="text-muted">Total: ₱' . number_format($group['total'], 2) . '</p>
            </div>
        </div> <!-- end order-group -->
        <hr class="separator">';
    }

    echo '</section>';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QUALITEES | Ordered & History</title>
    <link rel="icon" href="./media/icon.png" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        @import url('../stardom.css');

        body {
            font-family: 'Stardom-Regular', sans-serif;
            background-color: #f8f9fa;
        }

        .receipt-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 1rem;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .section-title {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 0.5rem;
        }

        .item-entry {
            margin-bottom: 1rem;
        }

        .item-details {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .item-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            margin-right: 1rem;
            border-radius: 4px;
        }

        .item-text {
            flex-grow: 1;
        }

        .item-name {
            font-weight: normal;
        }

        .item-x {
            color: #b33939;
            margin: 0 4px;
        }

        .item-qty {
            font-weight: bold;
        }

        .item-amount {
            color: #28a745;
            font-weight: bold;
            margin-left: auto;
        }

        .ref-summary {
            text-align: right;
            margin-top: -0.5rem;
            margin-bottom: 1rem;
        }

        .separator {
            border-top: 1px solid #dee2e6;
            margin: 1rem 0;
        }

        .order-group {
            padding: 0.5rem;
            border-radius: 6px;
            transition: background-color 0.2s ease, transform 0.1s ease;
            cursor: pointer;
        }

        .order-group:hover {
            background-color: #f1f1f1;
            transform: scale(1.01);
        }
    </style>
</head>

<div style="position:sticky; z-index:1000; top: 0; background-color:white">
    <?php include './headerC.php'; ?>
</div>

<body>
    <div class="receipt-container">
        <?php renderOrders($conn, $userID, 'Items Ordered', 0); ?>
        <?php renderOrders($conn, $userID, 'Order History', 1); ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

<script>
    $(document).ready(function() {
        $(".order-group").on("click", function() {
            let ref = $(this).data("ref");
            if (ref) {
                window.location.href = "receiptxsummary.php?ref=" + ref;
            }
        });
    });
</script>
<?php include './footer.php'; ?>

</html>