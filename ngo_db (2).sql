-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Oct 06, 2026 at 06:14 PM
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
-- Database: `ngo_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(150) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `status` enum('unread','read','replied') NOT NULL DEFAULT 'unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `donations`
--

CREATE TABLE `donations` (
  `id` int(11) NOT NULL,
  `donor_name` varchar(255) NOT NULL,
  `email` varchar(150) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_channel` varchar(100) NOT NULL,
  `purpose` varchar(255) DEFAULT NULL,
  `proof_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `venue` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `time_range` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `title`, `date`, `venue`, `description`, `created_at`, `time_range`, `category`) VALUES
(1, '𝗚𝗨𝗟𝗔𝗬𝗔𝗔𝗡: 𝗧𝗵𝗲 𝗣𝗹𝗮𝗻𝘁-𝗕𝗮𝘀𝗲𝗱 𝗙𝗲𝘀𝘁𝗶𝘃𝗮𝗹', '2026-11-23', 'PUP Manila', '<p>Ready to transform our food systems? The Youth for Just Food Systems, in partnership with PUP, is looking for passionate change-makers for GULAYAAN: The Plant-Based Festival at PUP Manila! This isn\'t just a festival-it\'s a movement for climate action, animal justice, public health, and just food systems. We need YOU to help us build a more just and sustainable future.</p>', '2026-06-24 18:03:06', '1:00 PM - 4:00 PM', 'Campaign'),
(2, 'Annual Plant-Based Festival 2026', '2026-09-18', 'University Main Amphitheater', 'A vibrant celebration of food justice featuring collaborative plant-forward food booths, local student culinary cook-offs, and critical eco-justice campaign panels.', '2026-06-24 18:46:19', '9:00 AM - 5:00 PM', 'Festival'),
(3, 'Democratize Food Futures in PUP: Student and Youth Multi-Stakeholder Convening on the PUP Food System', '2026-05-16', 'Study Corner Co-Working Space Sta. Mesa, Manila', '<p><span style=\"background-color: rgb(255, 255, 255); color: rgb(8, 8, 9);\">Join us for the </span><strong style=\"background-color: rgb(255, 255, 255); color: rgb(8, 8, 9);\">“Democratize Food Futures in PUP: Student and Youth Multi-Stakeholder Convening on the PUP Food System” </strong><span style=\"background-color: rgb(255, 255, 255); color: rgb(8, 8, 9);\">this </span><strong style=\"background-color: rgb(255, 255, 255); color: rgb(8, 8, 9);\">May 16, 2026</strong><span style=\"background-color: rgb(255, 255, 255); color: rgb(8, 8, 9);\"> with availability options at the Study Corner Co-working Space near PUP College of Communications.</span></p><p><br></p><p><br></p>', '2026-06-30 13:40:35', '8:00 AM - 12:00 PM', 'Discussion');

-- --------------------------------------------------------

--
-- Table structure for table `leaders`
--

CREATE TABLE `leaders` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `role` varchar(100) NOT NULL,
  `bio` text NOT NULL,
  `image_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leaders`
--

INSERT INTO `leaders` (`id`, `name`, `role`, `bio`, `image_path`) VALUES
(2, 'CASTLE REYNERA', 'National Convener', '<p class=\"ql-align-justify\"><strong style=\"color: rgb(0, 0, 0); background-color: transparent;\">Castle (they/them)</strong><span style=\"color: rgb(0, 0, 0); background-color: transparent;\"> is a community organizer and justice-driven advocate working to transform Philippine food systems toward equity, sustainability, and compassion. They serve as the National Convener of </span><strong style=\"color: rgb(0, 0, 0); background-color: transparent;\">Youth for Just Food Systems</strong><span style=\"color: rgb(0, 0, 0); background-color: transparent;\">, leading the movement’s strategic direction and the development of the country’s first </span><strong style=\"color: rgb(0, 0, 0); background-color: transparent;\">Youth Agenda for Just Food Systems</strong><span style=\"color: rgb(0, 0, 0); background-color: transparent;\">.</span></p><p class=\"ql-align-justify\"><br></p><p class=\"ql-align-justify\"><span style=\"color: rgb(0, 0, 0); background-color: transparent;\">Castle is also the </span><strong style=\"color: rgb(0, 0, 0); background-color: transparent;\">Executive Director and Founder of the Center for Asia Farming Solutions</strong><span style=\"color: rgb(0, 0, 0); background-color: transparent;\">, advancing high-welfare, climate-aligned farming systems across the region. With a BA in Public Administration and over five years of national and grassroots organizing, they bridge academic rigor with lived community realities to challenge extractive agricultural systems. Across their work, Castle </span><strong style=\"color: rgb(0, 0, 0); background-color: transparent;\">amplifies youth and farmer voices</strong><span style=\"color: rgb(0, 0, 0); background-color: transparent;\">, driving a just transition for people, animals, and the planet.</span></p><p><br></p>', 'uploads/1782565621_1.png'),
(3, 'RICH LEE RASCO', 'Deputy National Convener', '<p class=\"ql-align-justify\"><strong style=\"background-color: transparent; color: rgb(0, 0, 0);\">Rich (he/him)</strong><span style=\"background-color: transparent; color: rgb(0, 0, 0);\"> is an activist and advocate for a food system that centers justice towards animals, people, and the environment. He serves as the </span><strong style=\"background-color: transparent; color: rgb(0, 0, 0);\">Deputy National Convener </strong><span style=\"background-color: transparent; color: rgb(0, 0, 0);\">of </span><strong style=\"background-color: transparent; color: rgb(0, 0, 0);\">Youth for Just Food Systems </strong><span style=\"background-color: transparent; color: rgb(0, 0, 0);\">who manages the organization’s growing movement and leading campaigns.&nbsp;</span></p><p class=\"ql-align-justify\"><br></p><p class=\"ql-align-justify\"><strong style=\"background-color: transparent; color: rgb(0, 0, 0);\">Rich </strong><span style=\"background-color: transparent; color: rgb(0, 0, 0);\">is also a junior Philosophy student at Polytechnic University of the Philippines. He aspires to be an </span><strong style=\"background-color: transparent; color: rgb(0, 0, 0);\">animal ethicist</strong><span style=\"background-color: transparent; color: rgb(0, 0, 0);\"> through writing his undergraduate research in dismantling oppressive systems that perpetuate violence to animals, people, and the environment. Transcending beyond the academe, aims to change the food system through activism, leadership development, launching campaigns, and promoting veganism as an avenue to create meaningful change that intersects with the critical issues of animals, people, and the environment.&nbsp;</span></p><p><br></p>', 'uploads/1782565660_2.png'),
(4, 'KIENE CALANSINGIN', 'Director for Mobilisation', '<p class=\"ql-align-justify\"><strong style=\"background-color: transparent; color: rgb(0, 0, 0);\">Kiene</strong><span style=\"background-color: transparent; color: rgb(0, 0, 0);\"> </span><strong style=\"background-color: transparent; color: rgb(0, 0, 0);\">(she/her)</strong><span style=\"background-color: transparent; color: rgb(0, 0, 0);\"> is a sophomore Cooperative student at Polytechnic University of the Philippines. Driven by her compassion and advocacy in supporting community development. She propagated her expertise, values, and knowledge to amplify voices for the youth and take part in long-term sustainability planning.</span></p><p class=\"ql-align-justify\"><br></p><p class=\"ql-align-justify\"><span style=\"background-color: transparent; color: rgb(0, 0, 0);\">In a call to action for a just, sustainable and equitable food system, she centralizes work in&nbsp;mobilizing the youth by empowering them towards collaboratively impactful actions that could reach beyond the constituents of Youth for Just Food Systems but to an intersective and diverse oppression as a whole.</span></p>', 'uploads/1782565692_3.png'),
(5, 'DARWIN PORSONA', 'Director for Programs', '<p class=\"ql-align-justify\"><strong style=\"background-color: transparent; color: rgb(0, 0, 0);\">Darwin (he/him)</strong><span style=\"background-color: transparent; color: rgb(0, 0, 0);\"> is a Junior Philosophy student at Polytechnic University of the Philippines, his interest in philosophy grew when he was in his sophomore year and allowed himself to write about Aesthetic colliding with the Indigenous Culture and he is also interested in writing about the rights of indigenous people and planning to write about the harmful effect of human behavior/doing to the animals. He is inspired by the one and only Kara David when it comes to writing and to get the story of the people who are not always being heard off.</span></p><p class=\"ql-align-justify\"><br></p><p class=\"ql-align-justify\"><span style=\"background-color: transparent; color: rgb(0, 0, 0);\">He promotes the rights of the IP community towards the land grabbing of the other. Darwin also advocates to stop the cigarette use for the prevention of smoke to cause harm not only to humans but to animals and plants. He is a crafter who values eco-friendly materials that helps to reduce the use of materials that are harmful to everyone.</span></p>', 'uploads/1782565718_4.png');

-- --------------------------------------------------------

--
-- Table structure for table `memberships`
--

CREATE TABLE `memberships` (
  `id` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(150) NOT NULL,
  `interest` varchar(255) NOT NULL,
  `motivation` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) NOT NULL DEFAULT 'Pending',
  `rejection_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `memberships`
--

INSERT INTO `memberships` (`id`, `fullname`, `email`, `interest`, `motivation`, `created_at`, `status`, `rejection_reason`) VALUES
(3, 'Chelsea Anne Galang', 'chelsea@gmail.com', 'Sustainable Dining', 'I want to inspire', '2026-06-30 11:53:37', 'Approved', NULL),
(4, 'Eldridge Camaya', 'eldridgecamaya9@gmail.com', 'Sustainable Dining', 'Because', '2026-09-29 18:09:41', 'Pending', NULL),
(6, 'Jasmine Annika Pereye', 'jasmineannikaa@gmail.com', 'Plant-Based Festivals', 'Because', '2026-09-29 18:31:02', 'Approved', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE `modules` (
  `id` int(11) NOT NULL,
  `module_number` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `modules`
--

INSERT INTO `modules` (`id`, `module_number`, `title`, `description`, `file_path`, `updated_at`, `type`) VALUES
(8, 1, 'Introduction to Farmed Animal Advocacy', NULL, 'modules/module_1_1782663396.pdf', '2026-06-28 16:16:36', 'Required Core Reading Link'),
(9, 2, 'Impacts of Animal Agriculture: Part 1', NULL, 'modules/module_2_1782664110_0.pdf', '2026-06-28 16:28:30', 'Required Core Reading Link'),
(10, 3, 'Impacts of Animal Agriculture: Part 2', NULL, 'modules/module_3_1782664110_1.pdf', '2026-06-28 16:28:30', 'Required Core Reading Link'),
(11, 4, 'Welfare and Rights Issues in Animal Agriculture: Part 1', NULL, 'modules/module_4_1782664110_2.pdf', '2026-06-28 16:28:30', 'Required Core Reading Link'),
(12, 5, 'Welfare and Rights Issues in Animal Agriculture: Part 2', NULL, 'modules/module_5_1782664110_3.pdf', '2026-06-28 16:28:30', 'Required Core Reading Link'),
(13, 6, 'Real Case Studies', NULL, 'modules/module_6_1782664110_4.pdf', '2026-06-28 16:28:30', 'Practical Project Sheet'),
(14, 7, 'What can you do', NULL, 'modules/module_7_1782664110_5.pdf', '2026-06-28 16:28:30', 'Required Core Reading Link'),
(15, 8, 'How to create plans', NULL, 'modules/module_8_1782664110_6.pdf', '2026-06-28 16:28:30', 'Practical Project Sheet');

-- --------------------------------------------------------

--
-- Table structure for table `news_articles`
--

CREATE TABLE `news_articles` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `summary` text NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `article_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `news_articles`
--

INSERT INTO `news_articles` (`id`, `title`, `summary`, `image_path`, `article_date`, `created_at`) VALUES
(4, ' Memorandum of Understanding  with Youth for Just Food Systems ', '<p><span style=\"background-color: rgb(255, 255, 255); color: rgb(8, 8, 9);\">We formally signed a Memorandum of Understanding with Youth for Just Food Systems on May 15, 2026, to strengthen initiatives toward building sustainable food systems for a healthier and greener future.</span></p><p><br></p><p><span style=\"background-color: rgb(255, 255, 255); color: rgb(8, 8, 9);\">This collaboration aims to advance educational support, collaborative research, policy advocacy, and community-centered programs that promote mental and physical well-being, environmental awareness, and sustainable food practices within the University community.</span></p><p><br></p><p><span style=\"background-color: rgb(255, 255, 255); color: rgb(8, 8, 9);\">Anchored on the United Nations Sustainable Development Goals, this partnership further reinforces PUP’s commitment to fostering a more sustainable, equitable, and resilient future for every Iskolar ng Bayan.</span></p>', 'uploads/1782565899_704715775_1444987777655421_7052559511025053478_n.jpg', '2026-05-15', '2026-06-27 13:11:39');

-- --------------------------------------------------------

--
-- Table structure for table `site_content`
--

CREATE TABLE `site_content` (
  `section_name` varchar(100) NOT NULL,
  `content_text` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `site_content`
--

INSERT INTO `site_content` (`section_name`, `content_text`, `image_path`) VALUES
('about_who_we_are', '<p class=\\\"ql-align-justify\\\"><strong style=\\\"background-color: transparent; color: rgb(0, 0, 0);\\\">​Youth for Just Food Systems </strong><span style=\\\"background-color: transparent; color: rgb(0, 0, 0);\\\">is a non-profit, nonstock youth-led organization driving transformative change in how food is produced, distributed, and consumed. We equip young leaders, foster restorative initiatives, and stand in solidarity with those affected by injustices, especially farmed animals, forging inclusive partnerships to heal communities and ecosystems. Guided by liberation and transparent accountability, we confront harmful structures and deliver lasting, justice-centered outcomes. Our aim is building youth-powered just food systems with the vision that just food systems will liberate communities, animals, and ecosystems.</span></p><p><br></p>', NULL),
('homepage_hero', '<h1 class=\\\"ql-align-center\\\"><strong style=\\\"background-color: transparent; color: rgb(255, 255, 255);\\\">Building Youth-Powered Just Food Systems Together</strong></h1><p><br></p>', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `submissions`
--

CREATE TABLE `submissions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `uploaded_by` varchar(255) DEFAULT NULL,
  `module_number` int(11) NOT NULL,
  `file_name` text NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `file_path` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'student',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `name`, `fullname`, `email`, `password`, `role`, `created_at`) VALUES
(23, 'Admin', 'YFJS', '', 'admin@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '2026-10-03 07:56:59'),
(27, 'Juan Dela Cruz', '', '', 'users@gmail.com', '$2y$10$jXLF5ANcKzf11lvD/mA/7OzqAoppg6CXWExSTTyCyH.PyFnq805fy', 'student', '2026-10-03 08:14:31');

-- --------------------------------------------------------

--
-- Table structure for table `volunteers`
--

CREATE TABLE `volunteers` (
  `id` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(150) NOT NULL,
  `skills` text DEFAULT NULL,
  `interests` text DEFAULT NULL,
  `availability` varchar(255) DEFAULT NULL,
  `activities` text DEFAULT NULL,
  `affiliation` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) NOT NULL DEFAULT 'Pending',
  `status_updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `rejection_reason` varchar(500) DEFAULT NULL,
  `status_notified` tinyint(1) DEFAULT 0,
  `type` enum('Volunteer','Member') NOT NULL DEFAULT 'Volunteer',
  `motivation` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `volunteers`
--

INSERT INTO `volunteers` (`id`, `fullname`, `email`, `skills`, `interests`, `availability`, `activities`, `affiliation`, `created_at`, `status`, `status_updated_at`, `rejection_reason`, `status_notified`, `type`, `motivation`) VALUES
(11, 'Eldridge Camaya', 'eldridgecamaya9@gmail.com', NULL, NULL, NULL, 'Festival Support, Kitchen Capacity', 'Student / Youth', '2026-09-29 18:12:39', 'Pending', '2026-09-29 18:12:39', NULL, 0, 'Volunteer', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `volunteer_notifications`
--

CREATE TABLE `volunteer_notifications` (
  `id` int(11) NOT NULL,
  `volunteer_id` int(11) NOT NULL,
  `previous_status` varchar(50) DEFAULT NULL,
  `new_status` varchar(50) NOT NULL,
  `notification_sent` tinyint(1) DEFAULT 0,
  `notification_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `admin_notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `donations`
--
ALTER TABLE `donations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `leaders`
--
ALTER TABLE `leaders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `memberships`
--
ALTER TABLE `memberships`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `module_number` (`module_number`);

--
-- Indexes for table `news_articles`
--
ALTER TABLE `news_articles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `site_content`
--
ALTER TABLE `site_content`
  ADD PRIMARY KEY (`section_name`);

--
-- Indexes for table `submissions`
--
ALTER TABLE `submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `volunteers`
--
ALTER TABLE `volunteers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_email` (`email`);

--
-- Indexes for table `volunteer_notifications`
--
ALTER TABLE `volunteer_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `volunteer_id` (`volunteer_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `donations`
--
ALTER TABLE `donations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `leaders`
--
ALTER TABLE `leaders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `memberships`
--
ALTER TABLE `memberships`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `modules`
--
ALTER TABLE `modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `news_articles`
--
ALTER TABLE `news_articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `submissions`
--
ALTER TABLE `submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `volunteers`
--
ALTER TABLE `volunteers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `volunteer_notifications`
--
ALTER TABLE `volunteer_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `submissions`
--
ALTER TABLE `submissions`
  ADD CONSTRAINT `submissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `volunteer_notifications`
--
ALTER TABLE `volunteer_notifications`
  ADD CONSTRAINT `volunteer_notifications_ibfk_1` FOREIGN KEY (`volunteer_id`) REFERENCES `volunteers` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
