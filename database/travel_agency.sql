-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 10, 2026 at 10:51 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `travel_agency`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `date_booked` datetime NOT NULL DEFAULT current_timestamp(),
  `personalised_price` decimal(10,2) NOT NULL,
  `nr_adults` int(11) NOT NULL DEFAULT 1,
  `nr_children` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `package_id`, `date_booked`, `personalised_price`, `nr_adults`, `nr_children`) VALUES
(3, 1, 1, '2025-05-20 14:44:35', 0.00, 0, 0),
(4, 1, 1, '2025-05-20 14:57:30', 1660.13, 2, 1),
(5, 8, 1, '2025-05-27 00:07:45', 1660.13, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `message_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `message` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `other_travellers`
--

CREATE TABLE `other_travellers` (
  `traveller_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `surname` varchar(100) NOT NULL,
  `birthday` date DEFAULT NULL,
  `nationality` varchar(100) DEFAULT NULL,
  `passport_number` varchar(50) DEFAULT NULL,
  `traveller_type` enum('child','adult') NOT NULL,
  `gender` enum('male','female','other') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `other_travellers`
--

INSERT INTO `other_travellers` (`traveller_id`, `booking_id`, `name`, `surname`, `birthday`, `nationality`, `passport_number`, `traveller_type`, `gender`) VALUES
(1, 5, 'hg', 'gytftygy', '2025-05-27', 'ygyugy', 'hjjhghh', 'adult', 'male'),
(2, 5, 'hygy', 'yuf', '2025-05-27', 'ytfgj', 'gyugyu', 'adult', 'female'),
(3, 5, 'ygyhjyt', 'gtygygt', '2025-05-01', 'gtgyug', 'gggyut', 'child', 'female');

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE `packages` (
  `package_id` int(11) NOT NULL,
  `departure_city` varchar(100) NOT NULL,
  `destination_city` varchar(100) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `transport_type` enum('bus','flight','train','ship','mixed') NOT NULL,
  `accommodation_type` enum('hotel','airbnb','resort') NOT NULL,
  `hotel_name` varchar(150) DEFAULT NULL,
  `num_nights` int(11) NOT NULL,
  `num_days` int(11) NOT NULL,
  `departure_date` date NOT NULL,
  `return_date` date NOT NULL,
  `base_price` decimal(10,2) NOT NULL,
  `includes_meals` tinyint(1) DEFAULT 0,
  `includes_tour_guide` tinyint(1) DEFAULT 0,
  `max_capacity` int(11) NOT NULL,
  `img1` varchar(2083) DEFAULT NULL,
  `img2` varchar(2083) DEFAULT NULL,
  `img3` varchar(2083) DEFAULT NULL,
  `img4` varchar(2083) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `packages`
--

INSERT INTO `packages` (`package_id`, `departure_city`, `destination_city`, `name`, `description`, `transport_type`, `accommodation_type`, `hotel_name`, `num_nights`, `num_days`, `departure_date`, `return_date`, `base_price`, `includes_meals`, `includes_tour_guide`, `max_capacity`, `img1`, `img2`, `img3`, `img4`, `status`, `created_at`) VALUES
(1, 'Tirana', 'Dubai', 'Arabian Desert Expedition', 'Fly from Tirana to Dubai for a luxurious 6-day escape into the golden dunes. Enjoy thrilling desert safaris, camel rides, traditional Emirati cuisine, and the vibrant nightlife of Dubai. Stay in a premium resort and experience unmatched Arabian hospitality.', 'flight', 'resort', 'Ocean Breeze Resort', 5, 6, '2025-05-22', '2025-05-28', 699.00, 1, 1, 20, '/images/listing/1.jpg', '/images/listing/1.2.jpg', '/images/listing/1.3.jpg', '/images/listing/1.4.jpg', 'active', '2025-05-17 23:59:39'),
(2, 'Tirana', 'Rome', 'Roman Empire Escape', 'Board a comfortable bus from Tirana to Rome and explore the timeless wonders of the Eternal City. Visit the Colosseum, Vatican Museums, and Trevi Fountain. Taste authentic Italian pasta and wine while staying in a charming airbnb near the city center.', 'bus', 'airbnb', 'Centro Storico Stay', 4, 5, '2025-06-05', '2025-06-09', 299.00, 1, 0, 15, '/images/listing/2.jpg', '/images/listing/2.2.jpg', '/images/listing/2.3.jpg', '/images/listing/2.4.jpg', 'active', '2025-05-17 23:59:39'),
(3, 'Tirana', 'Istanbul', 'Mystic East Meets West Tour', 'Take a scenic train ride from Tirana to Istanbul, where ancient culture meets modern energy. Visit the Hagia Sophia, Grand Bazaar, and sail on the Bosphorus. Stay in a comfortable hotel and immerse in Turkish delights and traditions.', 'train', 'hotel', 'Sultan’s View Hotel', 6, 7, '2025-06-10', '2025-06-17', 399.00, 1, 1, 18, '/images/listing/3.jpg', '/images/listing/3.2.jpg', '/images/listing/3.3.jpg', '/images/listing/3.4.jpg', 'active', '2025-05-17 23:59:39'),
(4, 'Tirana', 'Santorini', 'Greek Island Dream', 'Fly from Tirana to the romantic island of Santorini. Enjoy spectacular sunsets, volcanic beaches, and white-washed villages. Indulge in Mediterranean cuisine and stay in a beautiful resort overlooking the caldera.', 'flight', 'resort', 'Blue Horizon Resort', 5, 6, '2025-07-01', '2025-07-07', 589.00, 1, 1, 12, '/images/listing/4.jpg', '/images/listing/4.2.jpg', '/images/listing/4.3.jpg', '/images/listing/4.4.jpg', 'active', '2025-05-17 23:59:39'),
(5, 'Tirana', 'Vienna', 'Austrian Symphony Tour', 'Take a smooth train journey from Tirana to Vienna and experience the classical charm of Austria. Visit Schönbrunn Palace, attend a Mozart concert, and enjoy coffee culture. Stay in a stylish hotel in the heart of Vienna.', 'train', 'hotel', 'Vienna Central Hotel', 4, 5, '2025-06-15', '2025-06-19', 350.00, 1, 0, 14, '/images/listing/5.jpg', '/images/listing/5.2.jpg', '/images/listing/5.3.jpg', '/images/listing/5.4.jpg', 'active', '2025-05-17 23:59:39'),
(6, 'Tirana', 'Barcelona', 'Catalan Adventure Escape', 'Fly to Barcelona for an unforgettable city escape. Wander through Gaudí’s masterpieces, enjoy sunny beaches, and immerse in Spanish nightlife. Your Airbnb is nestled in the Gothic Quarter, giving you authentic local vibes.', 'flight', 'airbnb', 'Gothic Quarter Loft', 5, 6, '2025-07-10', '2025-07-15', 479.00, 1, 1, 16, '/images/listing/6.jpg', '/images/listing/6.2.jpg', '/images/listing/6.3.jpg', '/images/listing/6.4.jpg', 'active', '2025-05-17 23:59:39'),
(7, 'Tirana', 'Split', 'Adriatic Coastal Retreat', 'Take a scenic bus ride to Split, Croatia. Enjoy crystal-clear beaches, island hopping, and delicious seafood. Discover Roman history at Diocletian’s Palace. Stay in a cozy airbnb by the sea.', 'bus', 'airbnb', 'Dalmatian Coast Home', 4, 5, '2025-07-20', '2025-07-24', 249.00, 0, 1, 10, '/images/listing/7.jpg', '/images/listing/7.2.jpg', '/images/listing/7.3.jpg', '/images/listing/7.4.jpg', 'active', '2025-05-17 23:59:39'),
(8, 'Tirana', 'Paris', 'Romantic Paris Getaway', 'Fly to the City of Lights and fall in love with its timeless charm. Visit the Eiffel Tower, Louvre, and enjoy a Seine River cruise. Dine at Parisian cafés and stay in a beautiful hotel near Montmartre.', 'flight', 'hotel', 'Hotel Belle Vue Paris', 5, 6, '2025-07-28', '2025-08-02', 599.00, 1, 1, 20, '/images/listing/8.jpg', '/images/listing/8.2.jpg', '/images/listing/8.3.jpg', '/images/listing/8.4.jpg', 'active', '2025-05-17 23:59:39'),
(9, 'Tirana', 'Saranda', 'Riviera Relaxation Voyage', 'Take a mixed transport journey (bus + ferry) to Saranda, Albania’s hidden gem. Visit Ksamil beaches, Blue Eye spring, and Butrint ruins. Stay in a luxury hotel overlooking the Ionian Sea.', 'mixed', 'hotel', 'Ionian Bliss Hotel', 5, 6, '2025-08-05', '2025-08-11', 199.00, 1, 0, 25, '/images/listing/9.jpg', '/images/listing/9.2.jpg', '/images/listing/9.3.jpg', '/images/listing/9.4.jpg', 'active', '2025-05-17 23:59:39'),
(10, 'Tirana', 'Amsterdam', 'Canals and Culture Tour', 'Fly to Amsterdam and explore a city full of charm, bikes, and art. Visit the Van Gogh Museum, Anne Frank House, and cruise the canals. Stay in a modern airbnb close to the Jordaan district.', 'flight', 'airbnb', 'Jordaan Modern Flat', 4, 5, '2025-08-15', '2025-08-19', 499.00, 1, 1, 18, '/images/listing/10.jpg', '/images/listing/10.2.jpg', '/images/listing/10.3.jpg', '/images/listing/10.4.jpg', 'active', '2025-05-17 23:59:39'),
(11, 'Tirana', 'Budapest', 'Danube Discovery Journey', 'Take a scenic train trip to Budapest and unwind in historic thermal baths, taste Hungarian goulash, and walk along the Danube. Stay in a resort offering views of Buda Castle.', 'train', 'resort', 'Danube Pearl Resort', 5, 6, '2025-08-20', '2025-08-26', 359.00, 1, 0, 22, '/images/listing/11.jpg', '/images/listing/11.2.jpg', '/images/listing/11.3.jpg', '/images/listing/11.4.jpg', 'active', '2025-05-17 23:59:39'),
(12, 'Tirana', 'Cairo', 'Pharaoh’s Trail Expedition', 'Fly to Cairo for an immersive adventure through ancient Egypt. Visit the Pyramids of Giza, cruise the Nile, and explore bustling bazaars. Stay at a luxury hotel overlooking the Nile River.', 'flight', 'hotel', 'Nile Crown Hotel', 6, 7, '2025-09-01', '2025-09-07', 649.00, 1, 1, 19, '/images/listing/12.jpg', '/images/listing/12.2.jpg', '/images/listing/12.3.jpg', '/images/listing/12.4.jpg', 'active', '2025-05-17 23:59:39');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `user_id`, `package_id`, `rating`, `comment`, `created_at`) VALUES
(1, 1, 1, 5, 'Amazing tour! The itinerary was perfectly organized, and the guide was very knowledgeable. Highly recommend!', '2025-05-18 00:00:50'),
(2, 2, 2, 4, 'Great experience overall, loved the cultural sites and the local food. A few long travel days but worth it.', '2025-05-18 00:00:50'),
(3, 3, 1, 4, 'The scenery was breathtaking and accommodations were comfortable. Would have liked more free time though.', '2025-05-18 00:00:50'),
(4, 4, 12, 3, 'Good trip but the pace felt a bit rushed at times. Some parts felt a little crowded with other tourists.', '2025-05-18 00:00:50'),
(5, 5, 1, 5, 'Excellent value for money! Every detail was taken care of and the activities were fun and engaging.', '2025-05-18 00:00:50'),
(6, 6, 1, 5, 'One of the best tours I’ve been on. Friendly staff, beautiful places, and smooth logistics throughout.', '2025-05-18 00:00:50'),
(7, 1, 1, 5, 'I love Dubai', '2025-05-18 00:32:57');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `birthday` date DEFAULT NULL,
  `passport_number` varchar(50) DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `nationality` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `profile_pic` varchar(2083) DEFAULT '/images/client/default.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password_hash`, `first_name`, `last_name`, `birthday`, `passport_number`, `gender`, `nationality`, `phone`, `role`, `created_at`, `profile_pic`) VALUES
(1, 'viola_zeneli', 'vzeneli23@epoka.edu.al', '<hashed_password>', 'Viola', 'Zeneli', '2005-03-31', NULL, NULL, 'Albanian', NULL, 'admin', '2025-05-18 00:00:11', '/images/client/default.jpg'),
(2, 'sidrit_rrustemi', 'srrustemi23@epoka.edu.al', '<hashed_password>', 'Sidrit', 'Rrustemi', '2005-06-20', NULL, NULL, 'Albanian', NULL, 'admin', '2025-05-18 00:00:11', '/images/client/default.jpg'),
(3, 'noel_zani', 'nzani23@epoka.edu.al', '<hashed_password>', 'Noel', 'Zani', '2005-02-15', NULL, NULL, 'Albanian', NULL, 'user', '2025-05-18 00:00:11', '/images/client/default.jpg'),
(4, 'nikol_dalipi', 'ndalipi23@epoka.edu.al', '<hashed_password>', 'Nikol', 'Dalipi', '2004-02-13', NULL, NULL, 'Albanian', NULL, 'user', '2025-05-18 00:00:11', '/images/client/default.jpg'),
(5, 'artemisa_hasalami', 'ahasalami23@epoka.edu.al', '<hashed_password>', 'Artemisa', 'Hasalami', '2005-06-25', NULL, NULL, 'Albanian', NULL, 'user', '2025-05-18 00:00:11', '/images/client/default.jpg'),
(6, 'denisa_korra', 'dkorra23@epoka.edu.al', '<hashed_password>', 'Denisa', 'Korra', '2005-04-02', NULL, NULL, 'Albanian', NULL, 'user', '2025-05-18 00:00:11', '/images/client/default.jpg'),
(7, 'megi_almadhi', 'malamdhi23@epoka.edu.al', '<hashed_password>', 'Megi', 'Almadhi', '2005-06-09', NULL, NULL, 'Albanian', NULL, 'user', '2025-05-18 00:00:11', '/images/client/default.jpg'),
(8, 'viola', 'vzeneli@gmail.com', '$2y$10$C.MSdEzsDqaocKPOZALm8eixCkuvSsV8R02cOijDYXiBeF31w4WHO', 'viola', 'zeneli', '2005-03-31', 'k55331060', 'female', 'Albania', '123', 'user', '2025-05-26 23:58:25', '/images/client/default.jpg'),
(9, 'admin', 'violazeneli2005@gmail.com', '$2y$10$bdqK5jPGdhhddNEpUhjV3.ACHGDCKMM58cocLnTVENLuWyf08mp56', 'Viola', 'Zeneli', '2005-03-31', 'V12345678Z', 'female', 'Albanian', '1234567890', 'admin', '2026-10-10 22:41:53', '/images/client/default.jpg'),
(10, 'user', 'mateozeneli@gmail.com', '$2y$10$0eLSzXIlkFscg4tA1cTfT.1CF9ub3zBzleMYuQk0G6y2WWvBJqrh.', 'Mateo', 'Zeneli', '2007-02-21', 'M12345678Z', 'male', 'Albanian', '1234567890', 'user', '2026-10-10 22:43:29', '/images/client/default.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `wishlist_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`wishlist_id`, `user_id`, `package_id`, `date_added`) VALUES
(1, 1, 1, '2025-05-18 00:33:30'),
(2, 8, 1, '2025-05-27 00:08:18');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `package_id` (`package_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`message_id`);

--
-- Indexes for table `other_travellers`
--
ALTER TABLE `other_travellers`
  ADD PRIMARY KEY (`traveller_id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`package_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `package_id` (`package_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`wishlist_id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`package_id`),
  ADD KEY `package_id` (`package_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `other_travellers`
--
ALTER TABLE `other_travellers`
  MODIFY `traveller_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `package_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `wishlist_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`package_id`) REFERENCES `packages` (`package_id`) ON DELETE CASCADE;

--
-- Constraints for table `other_travellers`
--
ALTER TABLE `other_travellers`
  ADD CONSTRAINT `other_travellers_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`package_id`) REFERENCES `packages` (`package_id`) ON DELETE CASCADE;

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`package_id`) REFERENCES `packages` (`package_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
