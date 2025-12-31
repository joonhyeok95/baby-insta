-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- 생성 시간: 25-12-31 14:41
-- 서버 버전: 10.4.32-MariaDB
-- PHP 버전: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 데이터베이스: `yehan`
--

-- --------------------------------------------------------

--
-- 테이블 구조 `tb_file`
--

CREATE TABLE `tb_file` (
  `FILE_ID` varchar(100) NOT NULL,
  `FILE_NAME` varchar(100) NOT NULL,
  `FILE_NAME_ORG` varchar(100) NOT NULL,
  `FILE_PATH` varchar(100) NOT NULL,
  `TAKEN_AT` date NOT NULL,
  `FILE_SIZE` int(11) NOT NULL,
  `CHILD_ID` varchar(100) NOT NULL,
  `USER_ID` varchar(100) NOT NULL,
  `WRITE_DATE` datetime NOT NULL,
  `CREATE_DATE` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
