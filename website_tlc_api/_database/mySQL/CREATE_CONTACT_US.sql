CREATE TABLE `contact_us` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `best_time_to_call` varchar(255) DEFAULT NULL,
  `message` text,
   PRIMARY KEY (id)
);