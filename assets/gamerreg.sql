-- SQL File for 'gamer' table (for gamereg.php)

-- --------------------------------------------------------
-- Table structure for `gamer`
--
-- This table stores information about gamers who register for the league.
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `gamer` (
  `id` INT(11) NOT NULL AUTO_INCREMENT, -- Unique identifier for each gamer, auto-increments
  `gaming_name` VARCHAR(255) NOT NULL,    -- The gamer's chosen gaming name/tag
  `game_registered` VARCHAR(100) NOT NULL, -- The specific game the gamer is registering for (e.g., NBA2K, FIFA)
  `email` VARCHAR(255) NOT NULL UNIQUE,   -- Gamer's email address, set as UNIQUE to prevent duplicate emails
  `phone_number` VARCHAR(50) NOT NULL,    -- Gamer's phone number
  `location_county` VARCHAR(100) NOT NULL, -- The county where the gamer is located
  `physical_location` VARCHAR(255) NOT NULL, -- More specific physical location/address
  `id_number` VARCHAR(50) NOT NULL,       -- Gamer's ID number (consider encrypting or hashing if sensitive)
  `mpesa_confirmation_message` TEXT NOT NULL, -- The full M-Pesa confirmation message
  `mpesa_transaction_id` VARCHAR(10) NOT NULL UNIQUE, -- The 10-character M-Pesa transaction ID, set as UNIQUE to prevent re-use
  `registration_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Automatically records when the registration occurred

  PRIMARY KEY (`id`), -- 'id' is the primary key
  INDEX (`mpesa_transaction_id`) -- Add an index for faster lookups on transaction ID
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Optional: Add comments to the table and columns for better documentation
ALTER TABLE `gamer` COMMENT = 'Stores registrations for gamers in the Khali League.';

ALTER TABLE `gamer` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Unique ID for the gamer';
ALTER TABLE `gamer` CHANGE `gaming_name` `gaming_name` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Gamer''s chosen gaming name';
ALTER TABLE `gamer` CHANGE `game_registered` `game_registered` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Game title registered for (e.g., NBA2K, FIFA)';
ALTER TABLE `gamer` CHANGE `email` `email` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Gamer''s email address';
ALTER TABLE `gamer` CHANGE `phone_number` `phone_number` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Gamer''s phone number';
ALTER TABLE `gamer` CHANGE `location_county` `location_county` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'County of residence';
ALTER TABLE `gamer` CHANGE `physical_location` `physical_location` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Specific physical location or address';
ALTER TABLE `gamer` CHANGE `id_number` `id_number` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'National ID number of the gamer';
ALTER TABLE `gamer` CHANGE `mpesa_confirmation_message` `mpesa_confirmation_message` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Full M-Pesa transaction confirmation message';
ALTER TABLE `gamer` CHANGE `mpesa_transaction_id` `mpesa_transaction_id` VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '10-character M-Pesa transaction ID (unique)';
ALTER TABLE `gamer` CHANGE `registration_date` `registration_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Timestamp of registration';