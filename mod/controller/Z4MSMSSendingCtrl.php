<?php

/*
 * ZnetDK, Starter Web Application for rapid & easy development
 * See official website https://www.znetdk.fr
 * Copyright (C) 2025 Pascal MARTINEZ (contact@znetdk.fr)
 * License GNU GPL http://www.gnu.org/licenses/gpl-3.0.html GNU GPL
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
 * ZnetDK 4 Mobile SMS sending module Controller
 *
 * File version: 1.0
 * Last update: 05/06/2025
 */

namespace z4m_smssending\mod\controller;

use \z4m_smssending\mod\SMSSendingHistory;
use \z4m_smssending\mod\SMSConfig;
use \z4m_smssending\mod\SMSToSend;

/**
 * App controller dedicated to HTTP requests sent by the SMS sending
 * configuration view (see 'z4m_sms_sending_config.php') and by the SMS sending
 * history view (see 'z4m_sms_sending_history.php').
 * Supported ZnetDK version: 2.9 or higher.
 */
class Z4MSMSSendingCtrl extends \AppController {

    /**
     * Evaluates whether action is allowed or not.
     * When authentication is required, action is allowed if connected user has
     * full menu access or if has a profile allowing access to the following
     * views: 'z4m_sms_sending_history' or 'z4m_sms_sending_config'.
     * If no authentication is required, action is allowed if the expected view
     * menu item is declared in the 'menu.php' script of the application.
     * @param string $action Action name
     * @return Boolean TRUE if action is allowed, FALSE otherwise
     */
    static public function isActionAllowed($action) {
        $status = parent::isActionAllowed($action);
        if ($status === FALSE) {
            return FALSE;
        }
        $actionView = [
            'history' => 'z4m_sms_sending_history',
            'purge' => 'z4m_sms_sending_history',
            'config' => 'z4m_sms_sending_config',
            'storeConfig' => 'z4m_sms_sending_config',
            'send' => 'z4m_sms_sending_config'
        ];
        $menuItem = key_exists($action, $actionView) ? $actionView[$action] : NULL;
        return CFG_AUTHENT_REQUIRED === TRUE
            ? \controller\Users::hasMenuItem($menuItem) // User has right on menu item
            : \MenuManager::getMenuItem($menuItem) !== NULL; // Menu item declared in 'menu.php'
    }

    // Controller's actions

    /**
     * Returns SMS sending history
     * @return \Response SMS sending history in JSON
     */
    static protected function action_history() {
        $request = new \Request();
        $first = $request->first; $count = $request->count;
        $searchCriteria = is_string($request->search_criteria) ? json_decode($request->search_criteria, TRUE) : NULL;
        $response = new \Response();
        $rowsFound = array();
        try {
            $response->total = SMSSendingHistory::getAll($first, $count, $searchCriteria, 'id DESC', $rowsFound);
            $response->rows = $rowsFound;
            $response->success = TRUE;
        } catch (\Exception $ex) {
            \General::writeErrorLog(__METHOD__, $ex->getMessage());
            $response->setFailedMessage(LC_MSG_CRI_ERR_SUMMARY, LC_MSG_CRI_ERR_GENERIC);
        }
        return $response;
    }

    static protected function action_purge() {
        $request = new \Request();
        $searchCriteria = is_string($request->search_criteria) ? json_decode($request->search_criteria, TRUE) : NULL;
        $response = new \Response();
        try {
            SMSSendingHistory::purge($searchCriteria);
            $response->setSuccessMessage(NULL, MOD_Z4M_SMSSENDING_HISTORY_PURGE_SUCCESS);
        } catch (Exception $ex) {
            \General::writeErrorLog(__METHOD__, $ex->getMessage());
            $response->setFailedMessage(LC_MSG_CRI_ERR_SUMMARY, LC_MSG_CRI_ERR_GENERIC);
        }
        return $response;
    }

    /**
     * Returns SMS sending configuration
     * @return \Response Configuration in JSON format
     */
    static protected function action_config() {
        $response = new \Response();
        try {
            $configuration = new SMSConfig();
            $response->setResponse($configuration->getRow());
        } catch (\Exception $ex) {
            \General::writeErrorLog(__METHOD__, $ex->getMessage());
            $response->setCriticalMessage(LC_MSG_CRI_ERR_GENERIC, $ex);
        }
        return $response;
    }

    /**
     * Stores SMS Sending configuration
     * @return \Response Success message or error message in JSON
     */
    static protected function action_storeConfig() {
        $response = new \Response();
        try {
            $config = new SMSConfig(TRUE);
            $validation = $config->validate();
            if (is_array($validation)) {
                $response->setFailedMessage(NULL, $validation['message'],
                        $validation['property']);
            } else {
                $config->store();
                $response->setSuccessMessage(NULL, MOD_Z4M_SMSSENDING_CONFIG_STORAGE_SUCCESS);
            }
        } catch (\ZDKException $ex) {
            $response->setFailedMessage(NULL, MOD_Z4M_SMSSENDING_CONFIG_STORAGE_FAILED
                    . $ex->getMessageWithoutCode());
        }
        catch (\Exception $ex) {
            \General::writeErrorLog(__METHOD__, $ex->getMessage());
            $response->setCriticalMessage(LC_MSG_CRI_ERR_GENERIC, $ex);
        }
        return $response;
    }

    /**
     * Sends a SMS.
     * @return \Response Success message or failed message in JSON.
     */
    static protected function action_send() {
        $request = new \Request();
        $response = new \Response();        
        $sms = new SMSToSend('alert', $request->is_simulation === '1');
        try {
            $sms->send($request->message, $request->recipient_phone_nbr,
                    $request->recipient_name, $request->business_reference);
            $response->setSuccessMessage(NULL, MOD_Z4M_SMSSENDING_SUCCESS);
        } catch (\Exception $ex) {
            $response->setFailedMessage(NULL, MOD_Z4M_SMSSENDING_FAILED . $ex->getMessage());
        }
        return $response;
    }

}
