<?php
require 'conn.php';
require 'init.php';

// === Pagination Variables ===
$limit = 30;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

// === Filters ===
$searchTerm = '';
if (isset($_GET['search'])) {
    $searchTerm = trim(mysqli_real_escape_string($conn, $_GET['search']));
}

$sortMethod = $_GET['sort'] ?? '';
$category   = $_GET['category'] ?? '';

// === BASE SQL ===
$sqlBase = "FROM items WHERE isOver = 0";

// Search filter
if ($searchTerm !== '') {
    $sqlBase .= " AND (itemName LIKE '%$searchTerm%' OR description LIKE '%$searchTerm%')";
}

// Category filter
if ($category !== '' && $category !== 'ALL') {
    $safeCat = mysqli_real_escape_string($conn, $category);
    $sqlBase .= " AND category = '$safeCat'";
}

// === SORTING ===
switch ($sortMethod) {
    case 'price_low':
        $orderBy = "ORDER BY price ASC";
        break;
    case 'price_high':
        $orderBy = "ORDER BY price DESC";
        break;
    case 'chronological':
        $orderBy = "ORDER BY itemID DESC";
        break;
    case 'oldest':
        $orderBy = "ORDER BY itemID ASC";
        break;
    default:
        $orderBy = "ORDER BY itemID DESC";
        break;
}

// === GET TOTAL ITEMS FOR PAGINATION ===
$countQuery = "SELECT COUNT(*) AS total " . $sqlBase;
$countResult = mysqli_query($conn, $countQuery);
$totalItems = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalItems / $limit);

// === FINAL SQL WITH LIMIT ===
$sql = "SELECT itemID, itemName, description, media, price, isOver, category 
        $sqlBase 
        $orderBy 
        LIMIT $limit OFFSET $offset";

$result = mysqli_query($conn, $sql);

$products = [];

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = [
            "id"    => $row['itemID'],
            "title" => $row['itemName'],
            "description" => $row['description'],
            "price" => $row['price'],
            "img"   => !empty($row['media']) ? $row['media'] : 'media/temp.jpg',
            "alt"   => $row['description']
        ];
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" href="./media/icon.png" type="icon.png" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
    <title>QUALITEES | Search/Items</title>

    <style>
        @import url('./media/stardom.css');

        body {
            background: #fff;
        }

        /* --- Search Bar --- */
        .search-bar {
            max-width: 420px;
            margin: 16px auto 12px auto;
            border-radius: 24px;
            border: 1.5px solid #ccc;
            padding: 4px 16px;
            display: flex;
            align-items: center;
        }

        .search-bar input {
            border: none;
            flex: 1;
            outline: none;
            background: transparent;
            font-size: 1.12rem;
            padding-left: 6px;
        }

        /* --- Sort Dropdown --- */
        .sortby-dropdown {
            position: relative;
            display: inline-flex;
            align-items: center;
            padding: 6px 16px;
            border: 1.5px solid #222;
            border-radius: 12px;
            background: transparent;
            font-size: 0.98rem;
            color: #222;
            cursor: pointer;
            user-select: none;
        }

        .sortby-dropdown svg {
            margin-left: 8px;
            transition: transform 0.3s ease;
            pointer-events: none;
        }

        .sortby-dropdown.open svg {
            transform: rotate(180deg);
        }

        .sortby-menu {
            position: absolute;
            top: 110%;
            left: 0;
            background: #fff;
            border: 1.5px solid #ddd;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
            border-radius: 6px;
            width: max-content;
            min-width: 180px;
            z-index: 1000;
            display: none;
            flex-direction: column;
            padding: 6px 0;
        }

        .sortby-menu.show {
            display: flex;
        }

        .sortby-menu button {
            background: transparent;
            border: none;
            padding: 8px 24px;
            text-align: left;
            font-size: 0.95rem;
            color: #222;
            cursor: pointer;
            transition: color 0.2s;
            display: block;
            width: 100%;
        }

        .sortby-menu button:hover,
        .sortby-menu button.active {
            color: #b33939;
            background-color: #f9f9f9;
        }

        /* --- Category Radios --- */
        .category-label {
            font-size: 1rem;
            color: #222;
            font-weight: 400;
            cursor: pointer;
            transition: color 0.2s;
        }

        .category-circle {
            width: 13px;
            height: 13px;
            border-radius: 50%;
            border: 1.5px solid #bbb;
            background: #fff;
            display: inline-block;
            transition: all 0.2s;
        }

        .category {
            cursor: pointer;
            position: relative;
        }

        /* actual radio button */
        .category input[type="radio"] {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }

        /* Hover State */
        .category:hover .category-label {
            color: #b33939;
        }

        .category:hover .category-circle {
            border-color: #b33939;
        }

        /* Checked/Active State */
        .category input[type="radio"]:checked~.category-label {
            color: #b33939;
            font-weight: 700;
        }

        .category input[type="radio"]:checked~.category-circle {
            border-color: #b33939;
            background: #b33939;
            transform: scale(1.1);
        }

        /* --- Layout & Card --- */
        .sortbar-divider {
            height: 24px;
            border-left: 1.5px solid #ccc;
            display: inline-block;
            margin: 0 18px;
        }

        .filter-btn {
            border: 1.5px solid #bbb;
            background: #fff;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: 8px;
            transition: border-color 0.2s;
            cursor: pointer;
            padding: 0;
        }

        .filter-btn:hover {
            border-color: #b33939;
        }

        .auction-card {
            min-height: 340px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border: 1.5px solid #e0e0e0;
            transition: border-color 0.2s;
        }

        .auction-card:hover {
            border-color: #b33939;
        }

        .card-img-top {
            width: 100%;
            height: 300px;
            object-fit: cover;
        }

        .card-title span {
            display: inline-block;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>

<body>
    <div style="position:sticky; z-index:1000; top: 0; background-color:white">
        <?php
        if (isset($_SESSION['userID']) && $_SESSION['isAdmin'] == 1) {
            include './headerA.php';
        } else {
            include './header.php';
        }
        ?>

        <!-- ONE MAIN FORM for Search, Sort, and Filter -->
        <form method="get" action="" id="searchForm">

            <!-- Search Bar -->
            <div class="search-bar my-3">
                <input type="text" name="search" placeholder="Search items..." value="<?= htmlspecialchars($searchTerm) ?>" />
                <button type="submit" aria-label="Search" style="background:none; border:none; cursor:pointer;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#aaa" viewBox="0 0 16 16">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" />
                    </svg>
                </button>
            </div>

            <div class="text-muted ms-1 mb-3 text-center" style="font-size:0.95rem;">
                <?= $searchTerm === '' ? 'Showing all items' : 'Search result for "' . htmlspecialchars($searchTerm) . '"' ?>
                <?php if ($category && $category !== 'ALL') echo " in " . htmlspecialchars($category); ?>
            </div>

            <!-- Filter / Sort Toolbar -->
            <div class="d-flex align-items-center justify-content-center mb-4 gap-3 flex-wrap">

                <!-- Hidden Sort Input -->
                <input type="hidden" name="sort" id="sortInput" value="<?= htmlspecialchars($sortMethod) ?>">

                <!-- Custom Sort Dropdown -->
                <div class="position-relative">
                    <div id="sortByBtn" class="sortby-dropdown" tabindex="0">
                        Sort by
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z" />
                        </svg>
                    </div>
                    <div id="sortByMenu" class="sortby-menu" role="menu">
                        <button type="button" onclick="setSort('chronological')" class="<?= $sortMethod == 'chronological' ? 'active' : '' ?>">Chronological</button>
                        <button type="button" onclick="setSort('price_low')" class="<?= $sortMethod == 'price_low' ? 'active' : '' ?>">Price low to high</button>
                        <button type="button" onclick="setSort('price_high')" class="<?= $sortMethod == 'price_high' ? 'active' : '' ?>">Price high to low</button>
                        <button type="button" onclick="setSort('oldest')" class="<?= $sortMethod == 'oldest' ? 'active' : '' ?>">Oldest</button>
                    </div>
                </div>

                <span class="sortbar-divider"></span>

                <!-- Category Radio Buttons -->
                <div class="d-flex align-items-center gap-4 flex-wrap">
                    <!-- Note: 'onchange="this.form.submit()"' for immediate filtering when clicked. 
                            Remove if dont want-->

                    <label class="category text-center">
                        <input type="radio" name="category" value="ALL" <?= ($category == '' || $category == 'ALL') ? 'checked' : '' ?> onchange="this.form.submit()">
                        <div class="category-label">ALL</div>
                        <div class="category-circle mt-2"></div>
                    </label>

                    <label class="category text-center">
                        <input type="radio" name="category" value="JEWELRY" <?= $category == 'JEWELRY' ? 'checked' : '' ?> onchange="this.form.submit()">
                        <div class="category-label">JEWELRY</div>
                        <div class="category-circle mt-2"></div>
                    </label>

                    <label class="category text-center">
                        <input type="radio" name="category" value="FINE ARTS" <?= $category == 'FINE ARTS' ? 'checked' : '' ?> onchange="this.form.submit()">
                        <div class="category-label">FINE ARTS</div>
                        <div class="category-circle mt-2"></div>
                    </label>

                    <label class="category text-center">
                        <input type="radio" name="category" value="CARS" <?= $category == 'CARS' ? 'checked' : '' ?> onchange="this.form.submit()">
                        <div class="category-label">CARS</div>
                        <div class="category-circle mt-2"></div>
                    </label>

                    <label class="category text-center">
                        <input type="radio" name="category" value="WATCHES" <?= $category == 'WATCHES' ? 'checked' : '' ?> onchange="this.form.submit()">
                        <div class="category-label">WATCHES</div>
                        <div class="category-circle mt-2"></div>
                    </label>

                    <label class="category text-center">
                        <input type="radio" name="category" value="OTHERS" <?= $category == 'OTHERS' ? 'checked' : '' ?> onchange="this.form.submit()">
                        <div class="category-label">OTHERS</div>
                        <div class="category-circle mt-2"></div>
                    </label>

                    <!-- Filter Submit Button -->
                    <button type="submit" class="filter-btn ms-3" aria-label="Filter">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#666" viewBox="0 0 16 16">
                            <path d="M6.5 10V13.5a.5.5 0 0 0 .8.4l2-1.429V10l4.634-5.115A1 1 0 0 0 13.884 3H2.116a1 1 0 0 0-.75 1.885L6.5 10ZM1 3.5A1.5 1.5 0 0 1 2.5 2h11A1.5 1.5 0 0 1 15 3.5c0 .323-.103.63-.293.879L10.5 10.197V13a2 2 0 0 1-3.2 1.6l-2-1.429A1 1 0 0 1 5 13v-2.803L1.293 4.379A1.5 1.5 0 0 1 1 3.5Z" />
                        </svg>
                    </button>
                </div>
            </div>
        </form>
    </div>
    </div>
    <div class="container">
        <!-- Cards Grid -->
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php if (empty($products)): ?>
                <div class="col w-100">
                    <p class="text-center text-muted" style="font-size: 1.2rem; margin-top: 50px;">
                        No items found matching your filters.
                    </p>
                </div>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="col">
                        <!-- Wrap the entire card in an anchor -->
                        <a href="productpage.php?id=<?= $product['id']; ?>" class="text-decoration-none text-dark">
                            <div class="card auction-card h-100">
                                <img src="<?= htmlspecialchars($product['img']); ?>" class="card-img-top" alt="<?= htmlspecialchars($product['alt']); ?>" />
                                <div class="card-body text-center">
                                    <div class="card-title"><span><?= htmlspecialchars($product['title']); ?></span></div>
                                    <div class="card-text time-label"><?= htmlspecialchars($product['description']); ?></div>
                                    <div class="card-text mt-1 mb-0" style="color: #333;">
                                        ₱<?= number_format($product['price'], 2); ?>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- PAGINATION -->
        <div class="d-flex justify-content-center mt-4 mb-5">

            <nav>
                <ul class="pagination">

                    <!-- PREV BUTTON -->
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link"
                                href="?page=<?= $page - 1 ?>&search=<?= $searchTerm ?>&category=<?= $category ?>&sort=<?= $sortMethod ?>">
                                ← Prev
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- PAGE NUMBERS -->
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                            <a class="page-link"
                                href="?page=<?= $i ?>&search=<?= $searchTerm ?>&category=<?= $category ?>&sort=<?= $sortMethod ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <!-- NEXT BUTTON -->
                    <?php if ($page < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link"
                                href="?page=<?= $page + 1 ?>&search=<?= $searchTerm ?>&category=<?= $category ?>&sort=<?= $sortMethod ?>">
                                Next →
                            </a>
                        </li>
                    <?php endif; ?>

                </ul>
            </nav>

        </div>

    </div>

    <script>
        const sortByBtn = document.getElementById('sortByBtn');
        const sortByMenu = document.getElementById('sortByMenu');
        const sortInput = document.getElementById('sortInput');
        const mainForm = document.getElementById('searchForm');

        // Toggle Sort Dropdown
        sortByBtn.addEventListener('click', () => {
            sortByMenu.classList.toggle('show');
            sortByBtn.classList.toggle('open');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!sortByBtn.contains(e.target) && !sortByMenu.contains(e.target)) {
                sortByMenu.classList.remove('show');
                sortByBtn.classList.remove('open');
            }
        });

        // Function called by sort buttons
        function setSort(value) {
            sortInput.value = value;
            mainForm.submit(); // Submit the whole form (includes search & category)
        }
    </script>
    <br>
    <?php include './footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>