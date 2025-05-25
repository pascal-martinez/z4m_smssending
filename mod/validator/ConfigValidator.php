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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * --------------------------------------------------------------------
 * ZnetDK 4 Mobile SMS Sending module configuration validator
 *
 * File version: 1.0
 * Last update: 05/06/2025
 */

namespace z4m_smssending\mod\validator;

use z4m_smssending\mod\SMSToSend;

/**
 * SMS sending configuration validator
 */
class ConfigValidator extends \Validator {

    protected function initVariables() {
        return array('sender_name', 'auth_token', 'alert_threshold',
            'is_sending_enabled', 'is_history_enabled');
    }
    /**
     * Sender name is mandatory.
     * @param string $value Sender name
     * @return boolean TRUE on validation success, FALSE otherwise.
     */
    protected function check_sender_name($value) {
        if (is_null($value)) {
            $this->setErrorMessage(\General::getFilledMessage(
                LC_MSG_ERR_MISSING_VALUE_FOR,
                MOD_Z4M_SMSSENDING_CONFIG_SENDER_NAME_LABEL));
            return FALSE;
        }
        try {
            SMSToSend::isSenderValid($value);
        } catch (\Exception $ex) {
            $this->setErrorMessage(LC_MSG_ERR_VALUE_INVALID . ' ' . $ex->getMessage());
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Authentication token is mandatory.
     * @param string $value Authentication token
     * @return boolean TRUE on validation success, FALSE otherwise.
     */
    protected function check_auth_token($value) {
        if (is_null($value)) {
            $this->setErrorMessage(\General::getFilledMessage(
                LC_MSG_ERR_MISSING_VALUE_FOR,
                MOD_Z4M_SMSSENDING_CONFIG_AUTH_TOKEN_LABEL));
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Alert threshold, if entered, must be an integer > 0.
     * @param string $value Alert threshold
     * @return boolean TRUE on validation success, FALSE otherwise.
     */
    protected function check_alert_threshold($value) {
        if (is_null($value)) {
            return TRUE;
        }
        if (!is_numeric($value) || $value < 1) {
            $this->setErrorMessage(LC_MSG_ERR_VALUE_INVALID);
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Option "SMS sending enabled" must be equal to 0 or 1.
     * @param boolean $value Option value 0 or 1 expected.
     * @return boolean TRUE on validation success, FALSE otherwise.
     */
    protected function check_is_sending_enabled($value) {
        if ($value < 0 || $value > 1) {
            $this->setErrorMessage(LC_MSG_ERR_VALUE_INVALID);
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Option "History of sent SMS enabled" must be equal to 0 or 1.
     * @param boolean $value Option value 0 or 1 expected.
     * @return boolean TRUE on validation success, FALSE otherwise.
     */
    protected function check_is_history_enabled($value) {
        if ($value < 0 || $value > 1) {
            $this->setErrorMessage(LC_MSG_ERR_VALUE_INVALID);
            return FALSE;
        }
        return TRUE;
    }
}
