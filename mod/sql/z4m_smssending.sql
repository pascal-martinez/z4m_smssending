/**
 * ZnetDK, Starter Web Application for rapid & easy development
 * See official website https://mobile.znetdk.fr
 * Copyright (C) 2025 Pascal MARTINEZ (contact@znetdk.fr)
 * License GNU GPL https://www.gnu.org/licenses/gpl-3.0.html GNU GPL
 * --------------------------------------------------------------------
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 * --------------------------------------------------------------------
 * ZnetDK 4 Mobile SMS Sending module SQL script
 *
 * File version: 1.0
 * Last update: 05/15/2025
 */

CREATE TABLE IF NOT EXISTS `z4m_sms_sending_config` (
  `id` int(11) NOT NULL COMMENT 'Internal identifier',
  `is_sending_enabled` BOOLEAN NOT NULL DEFAULT 0 COMMENT 'Is sending enabled?',
  `is_history_enabled` BOOLEAN NOT NULL DEFAULT 0 COMMENT 'Is SMS history enabled?',
  `sender_name` VARCHAR (11) NULL COMMENT 'Sender name',
  `auth_token` VARCHAR (255) NULL COMMENT 'Token for authentication',
  `alert_threshold` int NULL COMMENT 'Alert threshold',
  `credit_balance` int NULL COMMENT 'Credit balance',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT = 'SMS sending configuration';

INSERT INTO `z4m_sms_sending_config` (`id`) VALUES (1);

CREATE TABLE IF NOT EXISTS `z4m_sms_sending_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Internal Identifier',
  `message_id` varchar(200) DEFAULT NULL COMMENT 'Message identifier',
  `business_reference` varchar(100) DEFAULT NULL COMMENT 'Business reference',
  `sending_datetime` datetime NOT NULL COMMENT 'Sending date and time',
  `recipient_phone_nbr` VARCHAR(20) NOT NULL COMMENT 'Recipient phone number',
  `recipient_name` VARCHAR(50) NULL COMMENT 'Recipient name',
  `sender_name` VARCHAR(20) NOT NULL COMMENT 'Sender name',
  `message` TEXT NOT NULL COMMENT 'Text message',
  `status` tinyint(1) NOT NULL COMMENT 'Status',
  `error_message` TEXT NULL COMMENT 'Error message',
  PRIMARY KEY (`id`),
  KEY `business_reference` (`business_reference`),
  KEY `sending_datetime` (`sending_datetime`),
  KEY `message_id` (`message_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Sent SMS history' AUTO_INCREMENT=1 ;