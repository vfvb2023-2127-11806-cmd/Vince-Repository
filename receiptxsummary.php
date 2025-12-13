<?php
include 'init.php';
include 'conn.php';

if (!isset($_SESSION['userID'])) {
    echo "User not logged in.";
    exit;
}

$loggedInUserID = $_SESSION['userID'];
$isAdmin = $_SESSION['isAdmin'] ?? 0; // get admin flag from session

// Get reference number
$referenceno = $_GET['ref'] ?? $_POST['ref'] ?? null;
if (!$referenceno) {
    echo "No reference number provided.";
    exit;
}

$referenceno = mysqli_real_escape_string($conn, $referenceno);

// Get receipt + user info
$receiptQuery = "
    SELECT r.userID, r.orderStatus, u.firstName, u.lastName, u.email, u.address
    FROM receipt r
    JOIN users u ON r.userID = u.userID
    WHERE r.referenceno = '$referenceno'
    LIMIT 1
";
$receiptResult = mysqli_query($conn, $receiptQuery);
$receiptRow = mysqli_fetch_assoc($receiptResult);

if (!$receiptRow) {
    echo "Receipt not found.";
    exit;
}

// Get items
$itemsQuery = "
    SELECT i.itemName, r.qty, r.tPrice
    FROM receipt r
    JOIN items i ON r.itemID = i.itemID
    WHERE r.referenceno = '$referenceno'
";
$itemsResult = mysqli_query($conn, $itemsQuery);

$total = 0;
$items = [];
while ($row = mysqli_fetch_assoc($itemsResult)) {
    $items[] = $row;
    $total += $row['tPrice'];
}

// Handle admin status update
if ($isAdmin == 1 && isset($_POST['updateStatus'])) {
    $newStatus = ($_POST['updateStatus'] === 'Delivered') ? 1 : 0;
    $updateQuery = "UPDATE receipt SET orderStatus = $newStatus WHERE referenceno = '$referenceno'";
    mysqli_query($conn, $updateQuery);
    $receiptRow['orderStatus'] = $newStatus; // reflect change immediately
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QUALITEES | Receipt/Summary</title>
    <link rel="icon" href="./media/icon.png" type="image/png">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }

        .receipt {
            max-width: 420px;
            margin: auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 24px;
        }

        .header {
            text-align: center;
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 16px;
            color: #333;
        }

        .item-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            border-bottom: 1px solid #eee;
        }

        .item-row:last-child {
            border-bottom: none;
        }

        .total {
            text-align: right;
            font-weight: bold;
            font-size: 16px;
            margin-top: 12px;
            color: #222;
        }

        .info {
            margin-top: 20px;
            font-size: 14px;
            color: #555;
        }

        .info p {
            margin: 4px 0;
        }

        .thank-you {
            text-align: center;
            margin-top: 24px;
            font-size: 14px;
            color: #666;
            letter-spacing: 1px;
        }

        .admin-buttons {
            text-align: center;
            margin-top: 20px;
        }

        .admin-buttons button {
            margin: 5px;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .delivered {
            background-color: #4CAF50;
            color: white;
        }

        .ongoing {
            background-color: #f39c12;
            color: white;
        }
    </style>
</head>
<div style="position:sticky; z-index:1000; top: 0; background-color:white">
    <?php include './headerSum.php'; ?>
</div>

<body>
    <div class="receipt">
        <div class="header">QUALITEES</div>

        <?php foreach ($items as $item): ?>
            <div class="item-row">
                <span><?= htmlspecialchars($item['itemName']) ?></span>
                <span><?= $item['qty'] ?> × ₱<?= number_format($item['tPrice'] / $item['qty'], 2) ?></span>
            </div>
        <?php endforeach; ?>

        <div class="total">Total: ₱<?= number_format($total, 2) ?></div>

        <div class="info">
            <p>To: <?= htmlspecialchars($receiptRow['firstName'] . ' ' . $receiptRow['lastName']) ?></p>
            <p>Drop: <?= htmlspecialchars($receiptRow['address']) ?></p>
            <p>Contact: <?= htmlspecialchars($receiptRow['email']) ?></p>
            <p>Reference No: <?= htmlspecialchars($referenceno) ?></p>
            <p>Status: <?= $receiptRow['orderStatus'] == 1 ? "Delivered" : "Ongoing" ?></p>
        </div>

        <?php if ($isAdmin == 1): ?>
            <div class="admin-buttons">
                <form method="post">
                    <button type="submit" name="updateStatus" value="Delivered" class="delivered">Delivered</button>
                    <button type="submit" name="updateStatus" value="Ongoing" class="ongoing">Ongoing</button>
                </form>
            </div>
        <?php endif; ?>

        <div class="thank-you">THANK YOU</div>
    </div>
</body>

</html>