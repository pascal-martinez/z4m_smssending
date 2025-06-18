<?php
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
 * Parameters of the ZnetDK 4 Mobile SMS Sending module
 *
 * File version: 1.1
 * Last update: 06/18/2025
 */


/**
 * HTTP GET URL to send a SMS
 * @var string HTTP GET URL
 */
define('MOD_Z4M_SMSSENDING_HTTP_REQUEST_SEND_URL', 'https://api.smsfactor.com/send');

/**
 * HTTP GET URL to simulate a SMS sending
 * @var string HTTP GET URL
 */
define('MOD_Z4M_SMSSENDING_HTTP_REQUEST_SIMULATE_URL', 'https://api.smsfactor.com/send/simulate');

/**
 * Specifies whether sending errors are traced in the engine/log/errors.log file.
 * @var Boolean Value FALSE (errors are not traced) by default.
 */
define('MOD_Z4M_SMSSENDING_DEBUG_ENABLED', FALSE);


/**
 * Path of the SQL script to update the database schema
 * @var string Path of the SQL script
 */
define('MOD_Z4M_SMSSENDING_SQL_SCRIPT_PATH', ZNETDK_MOD_ROOT
        . DIRECTORY_SEPARATOR . 'z4m_smssending' . DIRECTORY_SEPARATOR
        . 'mod' . DIRECTORY_SEPARATOR . 'sql' . DIRECTORY_SEPARATOR
        . 'z4m_smssending.sql');

/**
 * Color scheme of the SMS sending UI.
 * @var array|NULL Colors used to display the SMS sending views. The expected
 * array keys are 'content', 'modal_header', 'modal_content' ,'modal_footer',
 * 'modal_footer_border_top', 'btn_action', 'btn_hover', 'btn_submit', 
 * 'btn_cancel', 'msg_error', 'filter_bar', 'list_border_bottom', 'icon', 'tag',
 * 'form_title' and 'form_title_border_bottom';
 * If NULL, default color CSS classes are applied.
 */
define('MOD_Z4M_SMSSENDING_COLOR_SCHEME', NULL);

/**
 * Module version number
 * @var string Version
 */
define('MOD_Z4M_SMSSENDING_VERSION_NUMBER','1.1');
/**
 * Module version date
 * @var string Date in W3C format
 */
define('MOD_Z4M_SMSSENDING_VERSION_DATE','2025-06-18');
