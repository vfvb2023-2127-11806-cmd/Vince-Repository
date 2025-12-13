<?php
include_once "conn.php";
require 'init.php';
// HERO //
// ---FETCH HERO DATA (Latest Item) ---
$heroQuery = "SELECT * FROM items where isOver = 0 ORDER BY itemID DESC LIMIT 1";
$heroResult = mysqli_query($conn, $heroQuery);
$heroItem = mysqli_fetch_assoc($heroResult);

// Set default values in case database is empty
$heroName = "Coming Soon";
$heroPrice = "0";
$heroImg = "./media/temp.png"; // fallback image

if ($heroItem) {
    $heroName = $heroItem['itemName'];
    $heroPrice = $heroItem['price'];
    $heroImg = $heroItem['media'];
}

// ---(SALE) ---
$gridQuery = "SELECT * FROM items where isOver = 0 ORDER BY itemID DESC LIMIT 8";
$gridResult = mysqli_query($conn, $gridQuery);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QUALITEES | Homepage</title>
    <link rel="icon" href="./media/icon.png" type="image/png">

    <!-- Bootstrap + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Custom Font -->
    <style>
        @import url('./media/stardom.css');

        body {
            font-family: 'Poppins', sans-serif;
        }

        /* Banner */
        .hero {
            position: relative;
            color: white;
            height: 90vh;
        }

        .hero-content {
            position: absolute;
            bottom: 40px;
            left: 40px;
            background: rgba(0, 0, 0, 0.4);
            color: white;
            padding: 20px 30px;
            border: none;
            border-radius: 6px;
            max-width: 400px;
        }

        .hero-content h1 {
            font-family: 'Stardom-Regular', sans-serif;
            font-size: 3rem;
        }

        .hero-btn {
            background-color: #b33939;
            color: white;
            padding: 10px 20px;
            text-transform: uppercase;
            border: none;
            transition: color 0.4s
        }

        .hero-btn:hover {
            background: white;
            color: #b33939;
        }

        /* Section Titles */
        .section-title {
            font-family: 'Stardom-Regular', sans-serif;
            font-size: 2rem;
            text-align: center;
            margin: 2rem 0 1rem;
        }

        .section-title::after {
            content: '';
            display: block;
            width: 80px;
            height: 3px;
            background: #b33939;
            margin: 10px auto;
        }

        /* Product Grid */
        .product-card img {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }

        .product-card .bid-btn {
            border: 1px solid #b33939;
            background: none;
            color: #b33939;
            text-transform: uppercase;
            padding: 5px 20px;
            margin-top: 10px;
        }

        .product-card .bid-btn:hover {
            background: #b33939;
            color: white;
        }

        /* Ending Soon */
        .ending-soon {
            background: #f9f9f9;
            padding: 3rem 0;
        }

        .ending-soon .highlight {
            color: #b33939;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div style="position:sticky; z-index:1000; top: 0;">
        <?php include './header.php'; ?>
    </div>
    <div class="container">
        <!-- Hero Banner -->
        <section class="hero d-flex align-items-center" style="background: url('<?php echo $heroImg; ?>') center/cover no-repeat;">
            <div class="hero-content">
                <h1><?php echo htmlspecialchars($heroName); ?></h1>
                <p>Latest Arrival</p>
                <a href="productpage.php?id=<?php echo $heroItem['itemID']; ?>">
                    <button class="hero-btn">BUY NOW - ₱<?php echo number_format($heroPrice, 2); ?></button>
                </a>

            </div>
        </section>
        <br>
    </div>

    <!-- Qualitees Banner -->
    <section class="text-center py-4" style="background: #E5E4E2">
        <h1 style="font-family: 'Stardom-Regular'; font-size: 64px;">QUALITEES</h1>
        <p class="text-muted" style="font-size: 24px;">Bid with Confidence, Win with Trust</p>
    </section>
    <section class="bg-dark">
        <br>
        <br>
        <br>
    </section>

    <div class="container">
        <!-- Sale Section -->
        <section class="container my-5">
            <h2 class="section-title">Sale</h2>
            <div class="text-end mb-3">
                <a href="./itemsort.php"><button class="btn btn-outline-dark btn-sm">View All</button></a>
            </div>

            <div class="row g-4">
                <?php
                // Check if there are items
                if (mysqli_num_rows($gridResult) > 0) {
                    // Loop through the database results
                    while ($row = mysqli_fetch_assoc($gridResult)) {
                ?>
                        <div class="col-6 col-md-3">
                            <div class="product-card text-center">
                                <!-- Display Item Image -->
                                <img src="<?php echo htmlspecialchars($row['media']); ?>" alt="<?php echo htmlspecialchars($row['itemName']); ?>">

                                <!-- Display Item Name -->
                                <p class="mt-2 fw-semibold"><?php echo htmlspecialchars($row['itemName']); ?></p>

                                <!-- Display Item Price -->
                                <a href="productpage.php?id=<?php echo $row['itemID']; ?>">
                                    <button class="bid-btn">₱ <?php echo number_format($row['price'], 2); ?></button>
                                </a>

                            </div>
                        </div>
                <?php
                    } // End While Loop
                } else {
                    echo "<p class='text-center'>No items currently on sale.</p>";
                }
                ?>
            </div>
        </section>
    </div>

    <?php include './footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>