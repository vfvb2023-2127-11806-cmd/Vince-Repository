-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 12, 2025 at 09:13 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `qualitees_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cartID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `itemID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cartID`, `userID`, `itemID`) VALUES
(22, 7, 5),
(26, 8, 1),
(27, 8, 5);

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `itemID` int(11) NOT NULL,
  `itemName` varchar(255) NOT NULL,
  `description` varchar(10000) NOT NULL,
  `media` varchar(100) NOT NULL,
  `category` varchar(24) NOT NULL,
  `price` int(11) NOT NULL,
  `inventory` int(11) NOT NULL,
  `tSold` int(11) NOT NULL,
  `tSales` float NOT NULL,
  `isOver` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`itemID`, `itemName`, `description`, `media`, `category`, `price`, `inventory`, `tSold`, `tSales`, `isOver`) VALUES
(1, 'Nissin Cup Noodles Hot Creamy Seafood', 'Net Weight 40g', 'https://drive.google.com/thumbnail?id=1ttnceDEuyKF0PYECV2599BPhzDHrPKWA&sz=h1080', 'OTHERS', 40, 800, 4, 160, 0),
(2, 'Dolce & Gabbana Jacket shirt', '2XL Brown Cotton', 'https://drive.google.com/thumbnail?id=1aMpFpx1e45lravGwBHOib5jqqwSURAsa&sz=h1080', 'OTHERS', 7200, 50, 2, 14400, 0),
(3, 'Pink Floyd North American Tour 1994 Brockum Single Stitch shirt', 'Large White Cotton', 'https://drive.google.com/thumbnail?id=1IWjpIw70yyFDSoRc1FQluNeZChz8SNLq&sz=h1080', 'OTHERS', 12200, 69, 2, 24400, 0),
(4, 'Wile E. Coyote Tultex Cartoon T-Shirt shirt', 'Large Block Colour Cotton', 'https://drive.google.com/thumbnail?id=1h-4C25CbhVUUv_GmHb0tWODoOTNOX3CA&sz=h1080', 'OTHERS', 10000, 67, 13, 130000, 0),
(5, 'car jacket', 'car', 'https://drive.google.com/thumbnail?id=1gpP2KE2IlVopd6TwjkW1b5V7kBECVspQ&sz=h1080', 'OTHERS', 1, 100, 22, 22, 0),
(6, 'The Addams Family 1991 T-Shirt', 'Amazing Shirt', 'https://drive.google.com/thumbnail?id=1V_X73jA9zRhfklwEIbEGClRs0qEOM4yB&sz=h1080', 'OTHERS', 10240, 40, 4, 40960, 0),
(7, 'The Addams Family 1991 T-Shirt', 'Amazing Shirt', 'https://drive.google.com/thumbnail?id=1V_X73jA9zRhfklwEIbEGClRs0qEOM4yB&sz=h1080', 'OTHERS', 10400, 40, 0, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `receipt`
--

CREATE TABLE `receipt` (
  `receiptID` int(11) NOT NULL,
  `referenceno` varchar(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `itemID` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `tPrice` int(11) NOT NULL,
  `orderStatus` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `receipt`
--

INSERT INTO `receipt` (`receiptID`, `referenceno`, `userID`, `itemID`, `qty`, `tPrice`, `orderStatus`) VALUES
(1, '43', 8, 5, 1, 1, 1),
(2, '67', 8, 1, 2, 80, 0),
(3, '67', 8, 4, 3, 30000, 0),
(4, '67', 8, 3, 1, 12200, 0),
(5, '7F52C80EFB', 7, 5, 20, 20, 0),
(6, '7F52C80EFB', 7, 1, 2, 80, 0),
(7, '94C78A9753', 7, 4, 10, 100000, 1),
(8, '53558B24BB', 7, 3, 1, 12200, 1),
(9, '53558B24BB', 7, 2, 1, 7200, 1),
(10, '1F935C8372', 8, 6, 4, 40960, 0),
(11, '229AB18489', 8, 5, 1, 1, 0),
(12, '5D6753D1A1', 8, 2, 1, 7200, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userID` int(11) NOT NULL,
  `firstName` varchar(100) NOT NULL,
  `lastName` varchar(100) NOT NULL,
  `email` varchar(254) NOT NULL,
  `password` varchar(64) NOT NULL,
  `address` varchar(255) NOT NULL,
  `isAdmin` tinyint(1) NOT NULL,
  `isActive` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userID`, `firstName`, `lastName`, `email`, `password`, `address`, `isAdmin`, `isActive`) VALUES
(1, 'Nathan', 'Gillman', 'natg20@gmail.com', 'nattwenty', 'Tokyo Guinobatan, Albay', 0, 1),
(2, 'main', 'sue', 'natg20@gmail.com', '$2y$10$KDF4lTsHx6XrxXUzvyCSD.VQPwwOCBINl6q6VUVkdeK.0clnGcRLS', 'ivandoe', 0, 1),
(3, 'main', 'sue', 'thegreatesttechnicianthateverlived@raccoon.hands', '$2y$10$8Tg0U7YVcPaxjTYL9Sq8MOiVAjiVtqlA7xOfnJa8dbhi46lIY0mdu', 'ivandoe', 0, 1),
(4, '', '', 'admin@root.com', '$2y$10$egK8wKepGXhvFxBIPlAGo.1lIjiHBb3b0xEwis3XAlubiDmUrkKbi', '', 1, 1),
(5, 'chad', 'champion', 'number@one.uk', '$2y$10$rx6SmhTItMVwwTbhShU0t.W9rdtzhqY59rHaSglZI5eYn3fLO95QK', 'bri\'ish', 0, 1),
(6, 'mama', 'papa', 'mama@papa.kama', '$2y$10$FZh4EEADa/pkYkXVhQzkY.CqMtHhfC0sDkDsN7gVWWKlEeykuTueS', 'grandpa', 0, 1),
(7, 'boboiboy', 'yaya', 'email@email.email', '$2y$10$n.3HFuLN6oeLexYtJEoxIuXSb4vtyV6yyn227SCRC5qBWjm5zD/cm', 'malaysia', 0, 1),
(8, 'brown', 'house', 't@t.t', '$2y$10$XjQKAJ.AeeOUDLiWNake/eajgzncfeIey0X/e8HewY2TN6QiaDNv2', 'soundcheck', 0, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cartID`),
  ADD KEY `itemIDlinkwl` (`itemID`),
  ADD KEY `userIDlinkwl` (`userID`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`itemID`);

--
-- Indexes for table `receipt`
--
ALTER TABLE `receipt`
  ADD PRIMARY KEY (`receiptID`),
  ADD KEY `userIDlink` (`userID`),
  ADD KEY `itemIDlink` (`itemID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cartID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `itemID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `receipt`
--
ALTER TABLE `receipt`
  MODIFY `receiptID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `itemIDlinkwl` FOREIGN KEY (`itemID`) REFERENCES `items` (`ItemID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `userIDlinkwl` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `receipt`
--
ALTER TABLE `receipt`
  ADD CONSTRAINT `itemIDlink` FOREIGN KEY (`itemID`) REFERENCES `items` (`ItemID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `userIDlink` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
