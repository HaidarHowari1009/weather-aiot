CREATE DATABASE IF NOT EXISTS weather_aiot;
USE weather_aiot;

CREATE TABLE IF NOT EXISTS `weather_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `observation_time` datetime NOT NULL,
  `temperature` float NOT NULL,
  `humidity` float NOT NULL,
  `wind_speed` float NOT NULL,
  `cloud_cover` float NOT NULL,
  `weather_desc` varchar(50) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `prediction_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `temperature` float NOT NULL,
  `humidity` float NOT NULL,
  `wind_speed` float NOT NULL,
  `cloud_cover` float NOT NULL,
  `prediction_result` varchar(50) NOT NULL,
  `confidence` float NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `model_evaluation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accuracy` float NOT NULL,
  `precision_score` float NOT NULL,
  `recall_score` float NOT NULL,
  `f1_score` float NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
