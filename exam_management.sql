-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 02, 2025 at 05:24 PM
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
-- Database: `exam_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `answers`
--

CREATE TABLE `answers` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer` text NOT NULL,
  `is_correct` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `exam_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `course_name` varchar(100) NOT NULL,
  `faculty_id` int(11) NOT NULL,
  `section` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `department` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `course_name`, `faculty_id`, `section`, `created_at`, `department`) VALUES
(1, 'Mathematics', 1, 'A', '2025-02-22 21:50:47', ''),
(2, 'Computer Science 101', 2, 'A', '2025-02-22 20:30:54', ''),
(3, 'Mathematics 101', 3, 'B', '2025-02-22 20:30:54', ''),
(5, 'Java', 2, 'C', '2025-02-22 20:31:47', ''),
(6, 'Java', 3, 'A', '2025-02-23 00:13:01', ''),
(10, 'SQL', 29, 'A', '2025-02-23 06:09:12', 'CSE'),
(11, 'Computer Science', 29, 'B', '2025-02-23 06:14:12', 'CSE'),
(12, 'Java', 29, 'A', '2025-02-23 06:52:35', 'CSE'),
(14, 'Java', 29, 'S13', '2025-02-23 18:50:03', 'CSE'),
(16, 'python ', 29, 's-12', '2025-03-01 12:54:02', 'cse');

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `exam_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `date` date NOT NULL,
  `start_date` date DEFAULT NULL,
  `time` time NOT NULL,
  `time_limit` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exams`
--

INSERT INTO `exams` (`id`, `course_id`, `exam_name`, `description`, `date`, `start_date`, `time`, `time_limit`, `created_at`, `start_time`, `end_time`) VALUES
(36, 10, 'python', 'xx', '2025-02-28', NULL, '00:00:00', 2, '2025-02-28 18:24:41', '23:54:00', '23:59:00');

-- --------------------------------------------------------

--
-- Table structure for table `exam_attempts`
--

CREATE TABLE `exam_attempts` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `exam_id` int(11) NOT NULL,
  `attempt_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `question_type` enum('multiple_choice','single_choice','short_answer','long_answer') NOT NULL,
  `question` text NOT NULL,
  `options` text DEFAULT NULL,
  `answer` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `course_id` int(11) DEFAULT NULL,
  `correct_answer` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`correct_answer`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `exam_id`, `question_type`, `question`, `options`, `answer`, `created_at`, `course_id`, `correct_answer`) VALUES
(46, 1, 'single_choice', 'What is the capital of France?', '[\"Paris\", \"London\", \"Berlin\", \"Madrid\"]', NULL, '2025-02-23 07:34:01', 101, '[\"Paris\"]'),
(47, 1, 'multiple_choice', 'Which of these are programming languages?', '[\"Python\", \"HTML\", \"CSS\", \"JavaScript\"]', NULL, '2025-02-23 07:34:01', 101, '[\"Python\", \"JavaScript\"]'),
(48, 1, 'short_answer', 'What is 2 + 2?', NULL, '4', '2025-02-23 07:34:01', 101, NULL),
(49, 1, 'long_answer', 'Explain the theory of relativity.', NULL, 'The theory of relativity states...', '2025-02-23 07:34:01', 101, NULL),
(87, 36, 'single_choice', 'what is pickle ', '[\"convert python to byte code\",\"byte code to python\",\"none\"]', '[\"1\"]', '2025-02-28 18:25:34', NULL, NULL),
(88, 36, 'short_answer', 'hbajhba', NULL, NULL, '2025-03-01 14:15:15', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `results`
--

CREATE TABLE `results` (
  `id` int(11) NOT NULL,
  `total_score` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `score` int(11) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `user_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `results`
--

INSERT INTO `results` (`id`, `total_score`, `exam_id`, `created_at`, `score`, `status`, `user_id`, `student_id`) VALUES
(18, 3, 36, '2025-02-28 18:26:48', 0, 'pending', 31, NULL),
(19, 3, 36, '2025-02-28 18:37:14', 0, 'pending', 31, NULL),
(20, 3, 36, '2025-02-28 18:41:03', 0, 'pending', 31, NULL),
(21, 3, 36, '2025-02-28 18:43:17', 0, 'pending', 31, NULL),
(22, 3, 36, '2025-02-28 19:01:59', 0, 'pending', 31, NULL),
(23, 3, 36, '2025-02-28 19:14:20', 0, 'pending', 31, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `section` varchar(50) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `year` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `name`, `email`, `password`, `section`, `department`, `year`) VALUES
(11, 'Test Student', 'test@example.com', 'password123', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','teacher','student') NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `section` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `department`, `year`, `created_at`, `section`) VALUES
(1, 'Admin User', 'admin@example.com', '$2y$10$tO1asEJ5M.v2dcd5a0VD0OgZUmC7.FbA1SSgSOL5VD.bZg/pox8C2', 'admin', NULL, NULL, '2025-02-22 20:21:26', ''),
(2, 'Teacher One', 'teacher1@example.com', '$2y$10$82rMewp0C9wRcbG03efLwOf0EobbHrjoiInNM5Am.lHBYKroaSEeu', 'teacher', 'Computer Science', NULL, '2025-02-22 20:21:26', ''),
(3, 'Teacher Two', 'teacher2@example.com', '$2y$10$6DxfX7McK7NAtGlKn8SGwuw/7BdFu94RqFH6Jk7/w.feKle005342', 'teacher', 'Mathematics', NULL, '2025-02-22 20:21:26', ''),
(27, 'Alice Johnson', 'student1@example.com', '$2y$10$D1.jLpO7.wJWQ56lQ6iChul5wKhjMfW1/OeU22mQsDOAd3.tAOR56', 'student', 'CSE', 4, '2025-02-23 05:27:58', 'A'),
(29, 'mehereesh', 'mehereesh2@gmail.com', '$2y$10$Z.8kMxd.01lJkR7QLi.N4eHT0mjspFKElfnaiM4ffjnd694zLOCTK', 'teacher', 'CSE', 2021, '2025-02-23 05:51:04', ''),
(30, 'meher', 'student1@gmail.com', '$2y$10$nDf0E4s/u8MoO.WBODQLnOVn019DdVrrvCdI/YGXTM.w1Uh/5YLE2', 'student', 'CSE', 4, '2025-02-23 05:57:12', ''),
(31, 'student3', 'student3@gmail.com', '$2y$10$XAAlFs11tltWB3o/Drt98OX8t/7Vw10N0Yt3htnklLbFo7qmkgjiO', 'student', 'CSE', 4, '2025-02-23 07:02:26', ''),
(37, 'dhanush', 'iamdhanushsm28@gmail.com', '$2y$10$vLP2CDKoueoDa.CUnaEGlu6Do1MrpsnshyVX5iVXWU7tx3enbAzBy', 'student', 'cse', 4, '2025-03-01 13:10:29', ''),
(40, 'Dhanush S M', 'dhanush@gmail.com', '$2y$10$EsgrsR3c5S7QxBlDmPZQcuVLg9/CD.BLKcXiDIoCqD9xhB54A/D/m', 'student', 'cse', 4, '2025-03-01 13:14:38', '');

-- --------------------------------------------------------

--
-- Table structure for table `user_answers`
--

CREATE TABLE `user_answers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer` text NOT NULL,
  `status` enum('pending','correct','incorrect') DEFAULT 'pending',
  `user_answer` text NOT NULL,
  `score` int(11) DEFAULT NULL,
  `feedback` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_answers`
--

INSERT INTO `user_answers` (`id`, `user_id`, `exam_id`, `question_id`, `answer`, `status`, `user_answer`, `score`, `feedback`) VALUES
(98, 31, 36, 87, '', '', 'convert python to byte code', 1, NULL),
(99, 31, 36, 87, '', '', 'convert python to byte code', 1, NULL),
(100, 31, 36, 87, '', '', 'convert python to byte code', 1, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `answers`
--
ALTER TABLE `answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_course` (`course_name`,`faculty_id`,`section`),
  ADD KEY `faculty_id` (`faculty_id`);

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `exam_attempts`
--
ALTER TABLE `exam_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `exam_id` (`exam_id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_id` (`exam_id`);

--
-- Indexes for table `results`
--
ALTER TABLE `results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_results_exam` (`exam_id`),
  ADD KEY `fk_results_user` (`user_id`),
  ADD KEY `fk_results_student` (`student_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `unique_email` (`email`);

--
-- Indexes for table `user_answers`
--
ALTER TABLE `user_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `exam_id` (`exam_id`),
  ADD KEY `question_id` (`question_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `answers`
--
ALTER TABLE `answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `exam_attempts`
--
ALTER TABLE `exam_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `results`
--
ALTER TABLE `results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `user_answers`
--
ALTER TABLE `user_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `answers`
--
ALTER TABLE `answers`
  ADD CONSTRAINT `answers_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `answers_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exams`
--
ALTER TABLE `exams`
  ADD CONSTRAINT `exams_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_attempts`
--
ALTER TABLE `exam_attempts`
  ADD CONSTRAINT `exam_attempts_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_attempts_ibfk_2` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `results`
--
ALTER TABLE `results`
  ADD CONSTRAINT `fk_results_exam` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_results_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_results_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `results_ibfk_2` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`);

--
-- Constraints for table `user_answers`
--
ALTER TABLE `user_answers`
  ADD CONSTRAINT `user_answers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_answers_ibfk_2` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_answers_ibfk_3` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
