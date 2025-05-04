-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 01, 2024 at 06:57 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mentalhealthapp`
--

-- --------------------------------------------------------

--
-- Table structure for table `badges`
--

CREATE TABLE `badges` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `unlocked` tinyint(1) NOT NULL,
  `criteria` varchar(255) DEFAULT NULL,
  `dateUnlocked` date DEFAULT NULL,
  `image` blob DEFAULT NULL,
  `progress` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `badges`
--

INSERT INTO `badges` (`id`, `title`, `unlocked`, `criteria`, `dateUnlocked`, `image`, `progress`) VALUES
(1, 'Novice', 1, 'Login 1 day', '2024-01-01', 0x22433a78616d70706874646f63730861646765736c6576656c312e706e6722, 100),
(2, 'Intermediate', 0, 'Login 7 days', NULL, 0x22433a78616d70706874646f63730861646765736c6576656c322e706e6722, 3),
(3, 'Expert', 0, 'Reach Level 10', NULL, 0x22433a78616d70706874646f63730861646765736c6576656c332e706e6722, 5),
(4, 'Master', 0, 'Complete 10 tasks', NULL, 0x22433a78616d70706874646f63730861646765736c6576656c342e706e6722, 2);

-- --------------------------------------------------------

--
-- Table structure for table `emergency_contacts`
--

CREATE TABLE `emergency_contacts` (
  `contact_id` int(11) NOT NULL,
  `ID` int(11) NOT NULL,
  `contact_name` varchar(255) NOT NULL,
  `contact_number` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `emergency_contacts`
--

INSERT INTO `emergency_contacts` (`contact_id`, `ID`, `contact_name`, `contact_number`) VALUES
(1, 38, 'Shannon Styles', '0283883729'),
(2, 39, 'mum', '0765562131');

-- --------------------------------------------------------

--
-- Table structure for table `journal`
--

CREATE TABLE `journal` (
  `journal_id` int(11) NOT NULL,
  `ID` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image_url` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `quote` varchar(255) DEFAULT NULL,
  `font_type` varchar(100) NOT NULL,
  `font_color` varchar(7) DEFAULT NULL,
  `font_size` int(11) NOT NULL DEFAULT 16
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `journal`
--

INSERT INTO `journal` (`journal_id`, `ID`, `file_name`, `file_content`, `created_at`, `image_url`, `title`, `quote`, `font_type`, `font_color`, `font_size`) VALUES
(2, 37, 'Friyay', 'hiii', '2024-11-29 20:28:28', '', NULL, NULL, '', '#333333', 16),
(3, 37, 'mood for today', 'cool yooo', '2024-11-29 20:35:31', '', NULL, NULL, '', '#333333', 16),
(4, 38, 'Coffee', 'i hope this works', '2024-11-29 20:59:44', '', NULL, NULL, '', '#333333', 16),
(6, 39, 'hey', 'hi', '2024-12-01 11:01:42', '../journal_final/butterfly.jpg', 'cccccccc', 'ccccccccc', 'Arial', '0', 34),
(7, 40, 'hi', 'harrrrry', '2024-12-01 11:47:55', '../journal_final/space.jpg', 'hiiiiiii', 'byeeeeeeeeee', 'Arial', '0', 16),
(8, 40, 'shan', 'dj', '2024-12-01 14:57:55', '../journal_final/space.jpg', 'hiiiiiii', 'byeeeeeeeeee', 'Arial', '0', 16);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL,
  `ID` int(11) NOT NULL,
  `therapist_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `subscription_id` int(11) NOT NULL,
  `ID` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `next_payment_date` date DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `task_id` int(11) NOT NULL,
  `task_name` varchar(255) NOT NULL,
  `task_type` enum('daily','weekly','monthly') NOT NULL,
  `xp_points` int(11) NOT NULL,
  `task_description` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`task_id`, `task_name`, `task_type`, `xp_points`, `task_description`) VALUES
(11, 'Log Mood', 'daily', 10, 'Record your mood for the day.'),
(12, 'Drink Water', 'daily', 5, 'Drink at least 8 glasses of water.'),
(13, 'Take a Walk', 'daily', 15, 'Walk for at least 30 minutes.'),
(14, 'Meditate', 'daily', 20, 'Meditate for at least 10 minutes.'),
(15, 'Read a Book', 'daily', 15, 'Read for at least 20 minutes.'),
(16, 'Practice Gratitude', 'daily', 10, 'Write down 3 things you are grateful for.'),
(17, 'Stretching Routine', 'daily', 15, 'Perform a 10-minute stretching routine.'),
(18, 'Digital Detox', 'daily', 25, 'Spend a day without social media.'),
(19, 'Listen to Music', 'daily', 10, 'Listen to a new genre or artist for 30 minutes.'),
(20, 'Cook a Healthy Meal', 'daily', 20, 'Prepare a nutritious meal for yourself.'),
(21, 'Write a Positive Affirmation', 'daily', 10, 'Write and recite a positive affirmation.'),
(22, 'Unplug Before Bed', 'daily', 15, 'Turn off screens 1 hour before bedtime.'),
(23, 'Engage in a Hobby', 'daily', 20, 'Spend at least 30 minutes on a hobby.'),
(24, 'Help Someone', 'daily', 15, 'Do a small act of kindness for someone.'),
(25, 'Review Daily Goals', 'daily', 10, 'Reflect on and adjust your daily goals.'),
(26, 'Reflect on the Week', 'weekly', 30, 'Write a reflection on your week.'),
(27, 'Complete a Challenge', 'weekly', 50, 'Complete a personal challenge (e.g., fitness).'),
(28, 'Connect with a Friend', 'weekly', 20, 'Reach out to a friend or family member.'),
(29, 'Journal Entry', 'weekly', 25, 'Write a journal entry about your feelings.'),
(30, 'Plan for the Week', 'weekly', 15, 'Set goals for the upcoming week.'),
(31, 'Attend a Local Event', 'weekly', 40, 'Participate in a community event or gathering.'),
(32, 'Try a New Recipe', 'weekly', 30, 'Cook a new dish you’ve never tried before.'),
(33, 'Volunteer', 'weekly', 50, 'Spend time volunteering for a cause.'),
(34, 'Explore a New Place', 'weekly', 25, 'Visit a new park, café, or area in your city.'),
(35, 'Digital Declutter', 'weekly', 20, 'Organize your digital files and emails.'),
(36, 'Family Game Night', 'weekly', 30, 'Spend quality time playing games with family.'),
(37, 'Learn Something New', 'weekly', 40, 'Take an online course or tutorial.'),
(38, 'Write a Letter', 'weekly', 15, 'Write a letter to a friend or family member.'),
(39, 'Attend a Fitness Class', 'weekly', 30, 'Join a fitness or yoga class.'),
(40, 'Self-Care Activity', 'weekly', 50, 'Dedicate time to a self-care activity (e.g., spa day).'),
(41, 'Monthly Review', 'monthly', 50, 'Review your month and set new goals.'),
(42, 'Attend a Workshop', 'monthly', 75, 'Attend a mental health or personal development workshop.'),
(43, 'Volunteer', 'monthly', 100, 'Participate in a community service activity.'),
(44, 'Self-Care Day', 'monthly', 80, 'Dedicate a day to self-care activities.'),
(45, 'Complete a Book', 'monthly', 60, 'Finish reading a book and summarize it.'),
(46, 'Create a Vision Board', 'monthly', 70, 'Design a vision board for your goals and dreams.'),
(47, 'Family Outing', 'monthly', 60, 'Plan and enjoy a day out with family.'),
(48, 'Financial Review', 'monthly', 50, 'Review your finances and set a budget for the month.'),
(49, 'Explore a New Hobby', 'monthly', 75, 'Start a new hobby or craft project.'),
(50, 'Reflect on Personal Growth', 'monthly', 40, 'Write about your personal growth over the month.'),
(51, 'Attend a Support Group', 'monthly', 80, 'Participate in a support group or discussion.'),
(52, 'Plan a Weekend Getaway', 'monthly', 100, 'Organize a short trip or getaway for relaxation.'),
(53, 'Create a Monthly Playlist', 'monthly', 30, 'Curate a playlist of songs that inspire you.'),
(54, 'Write a Blog Post', 'monthly', 50, 'Share your thoughts or experiences in a blog post.'),
(55, 'Set Long-Term Goals', 'monthly', 60, 'Define your long-term goals and aspirations.');

-- --------------------------------------------------------

--
-- Table structure for table `therapists`
--

CREATE TABLE `therapists` (
  `therapist_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `userloginreg`
--

CREATE TABLE `userloginreg` (
  `ID` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(200) NOT NULL,
  `timecreated` timestamp NOT NULL DEFAULT current_timestamp(),
  `points` int(255) NOT NULL,
  `level` int(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `contact` varchar(10) NOT NULL,
  `reset_token` varchar(255) NOT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `profile_pic` blob DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `userloginreg`
--

INSERT INTO `userloginreg` (`ID`, `username`, `email`, `password`, `timecreated`, `points`, `level`, `name`, `contact`, `reset_token`, `reset_expires`, `profile_pic`) VALUES
(37, 'hales', 'haleygovender21@gmail.com', '$2y$10$U1s9hHZqLBQe.zbWLCUHfuFNhbukwX1xhGzA735xr65AGVzsgdhN2', '2024-11-29 18:57:40', 0, 0, 'Haley Govender', '+274827489', '', NULL, 0x75706c6f6164732f39393362303532393664636534393264646437313536616266346261633666662e6a7067),
(38, 'tommy', 'tomj@gmail.com', '$2y$10$AHxrt199X6ybshXbLIbKI.0DKhi5P1PHifj0PpTeWeGijr0hWZ0Hu', '2024-11-29 20:58:36', 0, 0, 'Tom Jery', '+272378379', '', NULL, NULL),
(39, 'shannonsahdeo', 'shannonlsahdeo@gmail.com', '$2y$10$LXVjqqlQjsnI9K0WwNTer.DhhUxfRWKqjE6FBJYW9iAGP3osKEa0i', '2024-11-30 13:48:25', 0, 0, 'Shannon Leigh Sahdeo', '+276701246', 'eba7d53cfc5aa82525edf3fe32a071f2a2fd308115014c1a6e2ebe953051a9fc', '2024-12-01 18:21:25', 0x75706c6f6164732f32363266313333323436306234303431373135336336333031636561303930392e6a7067),
(40, 'harrystyles', 'harry@gmail.com', '$2y$10$iziH.55vd8Vrz.obZujFN.8vvY6i/fE7XAcxO09K8DSOmyMPF5j9m', '2024-11-30 13:54:33', 0, 0, 'Harry Styles', '+447684393', '', NULL, 0x75706c6f6164732f30333165313834636466353538633961316562623038346637383234633236312e6a7067);

-- --------------------------------------------------------

--
-- Table structure for table `user_tasks`
--

CREATE TABLE `user_tasks` (
  `user_task_id` int(11) NOT NULL,
  `ID` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `is_complete` tinyint(1) DEFAULT 0,
  `completed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `weekly_reflections`
--

CREATE TABLE `weekly_reflections` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `week_start_date` date DEFAULT NULL,
  `question_1` text DEFAULT NULL,
  `question_2` text DEFAULT NULL,
  `question_3` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `weekly_reflections`
--

INSERT INTO `weekly_reflections` (`id`, `username`, `week_start_date`, `question_1`, `question_2`, `question_3`, `created_at`) VALUES
(1, 'tommy', '2024-11-25', 'our website is coming together', 'alot of coding issues', 'finish my degree', '2024-11-30 13:16:22'),
(2, 'shannonsahdeo', '2024-11-25', 'we managed to do alot of coding', 'time managemnet', 'ill be done, so nothing', '2024-11-30 13:50:08'),
(3, 'harrystyles', '2024-11-25', 'hi', 'hi', 'hi', '2024-12-01 11:29:43');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `badges`
--
ALTER TABLE `badges`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `emergency_contacts`
--
ALTER TABLE `emergency_contacts`
  ADD PRIMARY KEY (`contact_id`),
  ADD KEY `ID` (`ID`);

--
-- Indexes for table `journal`
--
ALTER TABLE `journal`
  ADD PRIMARY KEY (`journal_id`),
  ADD KEY `ID` (`ID`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `ID` (`ID`),
  ADD KEY `therapist_id` (`therapist_id`);

--
-- Indexes for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`subscription_id`),
  ADD KEY `ID` (`ID`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`task_id`);

--
-- Indexes for table `therapists`
--
ALTER TABLE `therapists`
  ADD PRIMARY KEY (`therapist_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `userloginreg`
--
ALTER TABLE `userloginreg`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `user_tasks`
--
ALTER TABLE `user_tasks`
  ADD PRIMARY KEY (`user_task_id`),
  ADD KEY `ID` (`ID`),
  ADD KEY `task_id` (`task_id`);

--
-- Indexes for table `weekly_reflections`
--
ALTER TABLE `weekly_reflections`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `emergency_contacts`
--
ALTER TABLE `emergency_contacts`
  MODIFY `contact_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `journal`
--
ALTER TABLE `journal`
  MODIFY `journal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `subscription_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `task_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `therapists`
--
ALTER TABLE `therapists`
  MODIFY `therapist_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `userloginreg`
--
ALTER TABLE `userloginreg`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `user_tasks`
--
ALTER TABLE `user_tasks`
  MODIFY `user_task_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `weekly_reflections`
--
ALTER TABLE `weekly_reflections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `emergency_contacts`
--
ALTER TABLE `emergency_contacts`
  ADD CONSTRAINT `emergency_contacts_ibfk_1` FOREIGN KEY (`ID`) REFERENCES `userloginreg` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `journal`
--
ALTER TABLE `journal`
  ADD CONSTRAINT `journal_ibfk_1` FOREIGN KEY (`ID`) REFERENCES `userloginreg` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`ID`) REFERENCES `subscriptions` (`ID`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`therapist_id`) REFERENCES `therapists` (`therapist_id`);

--
-- Constraints for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD CONSTRAINT `subscriptions_ibfk_1` FOREIGN KEY (`ID`) REFERENCES `userloginreg` (`ID`);

--
-- Constraints for table `user_tasks`
--
ALTER TABLE `user_tasks`
  ADD CONSTRAINT `user_tasks_ibfk_1` FOREIGN KEY (`ID`) REFERENCES `userloginreg` (`ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_tasks_ibfk_2` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
