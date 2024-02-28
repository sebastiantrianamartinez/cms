-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 22-02-2024 a las 14:25:57
-- Versión del servidor: 10.4.27-MariaDB
-- Versión de PHP: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `engine`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `activation`
--

CREATE TABLE `activation` (
  `activation_id` int(11) NOT NULL,
  `activation_user` int(11) NOT NULL,
  `activation_token` tinytext NOT NULL,
  `activation_code` int(8) NOT NULL,
  `activation_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `activation`
--

INSERT INTO `activation` (`activation_id`, `activation_user`, `activation_token`, `activation_code`, `activation_time`) VALUES
(1, 1, '3495629265d553988ac7c2.32699273', 481013, '2024-02-21 01:37:04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `groups`
--

CREATE TABLE `groups` (
  `group_id` int(11) NOT NULL,
  `group_name` varchar(255) NOT NULL,
  `group_description` tinytext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `groups`
--

INSERT INTO `groups` (`group_id`, `group_name`, `group_description`) VALUES
(1, 'Webmasters', 'A webmaster is a person responsible for managing and maintaining a website. Their role involves various tasks related to the technical and operational aspects of a website.'),
(2, 'Admins', 'their role typically involves higher-level responsibilities related to managing and overseeing the overall administration and operation of a website or a web-based system. '),
(3, 'Editor', 'An editor is responsible for creating and editing content for the website.'),
(4, 'Manager', 'The role of a manager in a website typically involves overseeing the overall operation, strategy, and performance of the website.'),
(5, 'Analyst', 'Analysts are responsible for setting up and configuring data tracking tools, such as web analytics software (e.g., Google Analytics).'),
(6, 'Partner', 'Websites may form partnerships with sponsors or advertisers to generate revenue or support specific initiatives.'),
(7, 'Member', 'In the context of a website, a \"member\" refers to an individual or entity that has registered or subscribed to become part of a specific website community, program, or membership.'),
(8, 'Subscriptor', 'In the context of a website, a \"subscriber\" or \"subscriber\" refers to an individual or entity that has voluntarily provided their contact information to receive updates, newsletters, or other forms of communication from the website.'),
(9, 'Anonymous', 'No logged users');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logs`
--

CREATE TABLE `logs` (
  `log_id` int(11) NOT NULL,
  `log_class` int(11) NOT NULL,
  `log_user` int(11) DEFAULT NULL,
  `log_ip` varchar(16) NOT NULL,
  `log_agent` tinytext NOT NULL,
  `log_iat` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `log_ping` timestamp NULL DEFAULT NULL,
  `log_service` int(11) DEFAULT NULL,
  `log_message` tinytext DEFAULT NULL,
  `log_url` tinytext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permissions`
--

CREATE TABLE `permissions` (
  `permission_id` int(11) NOT NULL,
  `permission_service` int(11) NOT NULL,
  `permission_group` int(11) DEFAULT NULL,
  `permission_user` int(11) DEFAULT NULL,
  `permission_ip` varchar(64) DEFAULT NULL,
  `permission_crud` int(11) DEFAULT 15
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `permissions`
--

INSERT INTO `permissions` (`permission_id`, `permission_service`, `permission_group`, `permission_user`, `permission_ip`, `permission_crud`) VALUES
(1, 1, -1, NULL, NULL, 9),
(2, 2, -1, NULL, NULL, 15),
(3, 3, -1, NULL, NULL, 15),
(4, 4, 1, NULL, NULL, 15);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rules`
--

CREATE TABLE `rules` (
  `rule_id` int(11) NOT NULL,
  `rule_code` int(11) NOT NULL,
  `rule_cause` int(11) DEFAULT NULL,
  `rule_message` text DEFAULT NULL,
  `rule_user` int(11) DEFAULT NULL,
  `rule_ip` varchar(54) DEFAULT NULL,
  `rule_agent` tinytext DEFAULT NULL,
  `rule_iat` timestamp NOT NULL DEFAULT current_timestamp(),
  `rule_exp` timestamp NULL DEFAULT NULL,
  `rule_service` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `services`
--

CREATE TABLE `services` (
  `service_id` int(11) NOT NULL,
  `service_name` varchar(255) NOT NULL,
  `service_description` tinytext NOT NULL,
  `service_status` int(11) NOT NULL,
  `service_exp` int(11) NOT NULL DEFAULT 86700,
  `service_timeout` int(11) DEFAULT NULL,
  `service_level` int(11) NOT NULL DEFAULT 3,
  `service_key` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `services`
--

INSERT INTO `services` (`service_id`, `service_name`, `service_description`, `service_status`, `service_exp`, `service_timeout`, `service_level`, `service_key`) VALUES
(1, 'Authenticate', 'Manage user sessions', 2, 86700, NULL, 1, 'aa9e1eba0d002d85bdd7130222854ace'),
(2, 'Join', 'Create user account in the system', 2, 86700, NULL, 1, '1e9ae29440c74c4b995920e0f53437ef'),
(3, 'Activation', 'Activate user', 2, 86700, 120, 1, '713b3bc3a5c6a2e55b4b7644bbcdf751'),
(4, 'admin', 'admin', 2, 86700, NULL, 3, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tokens`
--

CREATE TABLE `tokens` (
  `token_id` int(11) NOT NULL,
  `token_value` text NOT NULL,
  `token_iat` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `token_exp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `token_user` int(11) NOT NULL,
  `token_ip` varchar(64) NOT NULL,
  `token_agent` tinytext NOT NULL,
  `token_ping` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(30) NOT NULL,
  `user_mail` varchar(255) NOT NULL,
  `user_password` tinytext NOT NULL,
  `user_group` int(11) NOT NULL,
  `user_alias` varchar(500) NOT NULL,
  `user_status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`user_id`, `user_name`, `user_mail`, `user_password`, `user_group`, `user_alias`, `user_status`) VALUES
(1, 'admin', 'sebastian.triana@utp.edu.co', '$2y$10$XH8.Iu1rnPBegDQx1kI.Ae/w2Jzk6mLiwrPKc1tL6Y7PMmXU5VGVy', 1, 'Sebastian Triana Martinez', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `wafcategories`
--

CREATE TABLE `wafcategories` (
  `wafcategory_id` int(11) NOT NULL,
  `wafcategory_name` varchar(255) NOT NULL,
  `wafcategory_description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `wafcategories`
--

INSERT INTO `wafcategories` (`wafcategory_id`, `wafcategory_name`, `wafcategory_description`) VALUES
(1, 'Authentication', 'Activities related to user authentication and access control'),
(2, 'Service Consumption', 'Activities related to consuming web services'),
(3, 'Request Handling', 'Activities related to handling incoming requests'),
(4, 'Data Protection', 'Activities related to protecting sensitive data'),
(5, 'Brute Force', 'Attempts to gain unauthorized access through brute force attacks'),
(6, 'Session Management', 'Events related to user session management');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `wafcauses`
--

CREATE TABLE `wafcauses` (
  `wafcause_id` int(11) NOT NULL,
  `wafcause_name` varchar(255) NOT NULL,
  `wafcause_description` text NOT NULL,
  `wafcause_severity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `wafcauses`
--

INSERT INTO `wafcauses` (`wafcause_id`, `wafcause_name`, `wafcause_description`, `wafcause_severity`) VALUES
(1, 'Incorrect username', 'Username does not match database records.', 1),
(2, 'Incorrect password', 'The password does not match the one registered by the user.', 1),
(3, 'Inactive user', 'Login attempt for a user who has not been activated', 1),
(4, 'Locked user', 'Login attempt for a user who has been blocked', 2),
(5, 'Deleted user', 'Login attempt for a user who has been deleted', 2),
(6, 'Previously used token', 'The user attempted to consume the service with a token that has already been used', 3),
(7, 'Wrong token client', 'The user agent of the token does not match the sender of the request', 3),
(8, 'Wrong token service id', 'Token service id does not match current request', 3),
(9, 'Wrong token session id', 'Token session ID does not match current session data', 3),
(10, 'Disobedience firewall rule', 'The user tries to access a service while blocked by the firewall', 3),
(11, 'Disobedience IpAccess rule', 'The user tries to access a service while being blocked by the IP access service', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `wafevents`
--

CREATE TABLE `wafevents` (
  `wafevent_id` int(11) NOT NULL,
  `wafevent_name` varchar(255) NOT NULL,
  `wafevent_description` text NOT NULL,
  `wafevent_category` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `wafevents`
--

INSERT INTO `wafevents` (`wafevent_id`, `wafevent_name`, `wafevent_description`, `wafevent_category`) VALUES
(1, 'Service consumed', 'The user successfully consumed the service', 2),
(2, 'Authentication failed', 'The user attempted authentication, but it was incorrect', 1),
(3, 'Success authentication ', 'The user successfully logged into the system', 1),
(4, 'Success authentication unlink', 'The user successfully logged out from the system', 1),
(5, 'Service Inactive', 'The user is trying to consume an inactive service', 2),
(6, 'Insufficient permissions', 'The user does not have permission to consume the requested service', 2),
(7, 'Timeout limit', 'The user is trying to re-consume a service that has mandatory downtime.', 2),
(8, 'Method not allowed', 'The user has permission to access the service, but the desired action is not allowed', 2),
(9, 'Invalid service token', 'The authorization token to consume the service has failed.', 2),
(10, 'Access attempt while suspended', 'The user has been blocked from consuming the service, but repeats consuming it', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `waflogs`
--

CREATE TABLE `waflogs` (
  `waflog_id` int(11) NOT NULL,
  `waflog_ip` varchar(45) NOT NULL,
  `waflog_agent` tinytext NOT NULL,
  `waflog_user` int(11) DEFAULT NULL,
  `waflog_event` int(11) NOT NULL,
  `waflog_cause` int(11) DEFAULT NULL,
  `waflog_description` text DEFAULT NULL,
  `waflog_data` longtext DEFAULT NULL,
  `waflog_service` int(11) NOT NULL,
  `waflog_iat` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `waflogs_view`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `waflogs_view` (
`waflog_id` int(11)
,`waflog_ip` varchar(45)
,`waflog_agent` tinytext
,`waflog_user` varchar(30)
,`waflog_event` varchar(255)
,`waflog_cause` varchar(255)
,`waflog_description` text
,`waflog_data` longtext
,`waflog_service` varchar(255)
,`waflog_iat` timestamp
);

-- --------------------------------------------------------

--
-- Estructura para la vista `waflogs_view`
--
DROP TABLE IF EXISTS `waflogs_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `waflogs_view`  AS SELECT `waflogs`.`waflog_id` AS `waflog_id`, `waflogs`.`waflog_ip` AS `waflog_ip`, `waflogs`.`waflog_agent` AS `waflog_agent`, `users`.`user_name` AS `waflog_user`, `wafevents`.`wafevent_name` AS `waflog_event`, `wafcauses`.`wafcause_name` AS `waflog_cause`, `waflogs`.`waflog_description` AS `waflog_description`, `waflogs`.`waflog_data` AS `waflog_data`, `services`.`service_name` AS `waflog_service`, `waflogs`.`waflog_iat` AS `waflog_iat` FROM ((((`waflogs` left join `users` on(`waflogs`.`waflog_user` = `users`.`user_id`)) left join `wafevents` on(`waflogs`.`waflog_event` = `wafevents`.`wafevent_id`)) left join `wafcauses` on(`waflogs`.`waflog_cause` = `wafcauses`.`wafcause_id`)) left join `services` on(`waflogs`.`waflog_service` = `services`.`service_id`))  ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `activation`
--
ALTER TABLE `activation`
  ADD PRIMARY KEY (`activation_id`),
  ADD UNIQUE KEY `activation_user` (`activation_user`);

--
-- Indices de la tabla `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`group_id`);

--
-- Indices de la tabla `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`log_id`);

--
-- Indices de la tabla `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`permission_id`);

--
-- Indices de la tabla `rules`
--
ALTER TABLE `rules`
  ADD PRIMARY KEY (`rule_id`);

--
-- Indices de la tabla `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`service_id`);

--
-- Indices de la tabla `tokens`
--
ALTER TABLE `tokens`
  ADD PRIMARY KEY (`token_id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_name` (`user_name`),
  ADD UNIQUE KEY `user_mail` (`user_mail`);

--
-- Indices de la tabla `wafcategories`
--
ALTER TABLE `wafcategories`
  ADD PRIMARY KEY (`wafcategory_id`);

--
-- Indices de la tabla `wafcauses`
--
ALTER TABLE `wafcauses`
  ADD PRIMARY KEY (`wafcause_id`);

--
-- Indices de la tabla `wafevents`
--
ALTER TABLE `wafevents`
  ADD PRIMARY KEY (`wafevent_id`);

--
-- Indices de la tabla `waflogs`
--
ALTER TABLE `waflogs`
  ADD PRIMARY KEY (`waflog_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `activation`
--
ALTER TABLE `activation`
  MODIFY `activation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `groups`
--
ALTER TABLE `groups`
  MODIFY `group_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `logs`
--
ALTER TABLE `logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `permissions`
--
ALTER TABLE `permissions`
  MODIFY `permission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `rules`
--
ALTER TABLE `rules`
  MODIFY `rule_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `services`
--
ALTER TABLE `services`
  MODIFY `service_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tokens`
--
ALTER TABLE `tokens`
  MODIFY `token_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `wafcategories`
--
ALTER TABLE `wafcategories`
  MODIFY `wafcategory_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `wafcauses`
--
ALTER TABLE `wafcauses`
  MODIFY `wafcause_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `wafevents`
--
ALTER TABLE `wafevents`
  MODIFY `wafevent_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `waflogs`
--
ALTER TABLE `waflogs`
  MODIFY `waflog_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
