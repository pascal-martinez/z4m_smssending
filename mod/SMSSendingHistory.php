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
 * ZnetDK 4 Mobile SMS Sending module History class
 *
 * File version: 1.0
 * Last update: 05/15/2025
 */
namespace z4m_smssending\mod;

/**
 * Storage of the SMS sent in history SQL table.
 * Supported ZnetDK version: 2.9 or higher.
 */
class SMSSendingHistory {

    /**
     * Returns SMS sending history rows
     * @param int $first The first row number to return
     * @param int $count The number of rows to return
     * @param array $searchCriteria Filter criteria. Expected keys are 'status',
     * 'start_date' and 'end_date'.
     * @param string $sortCriteria Sort criteria
     * @param array $rowsFound Rows found returned by the method
     * @return int Total number of history rows found in database
     * @throws \Exception When query failed
     */
    static public function getAll($first, $count, $searchCriteria, $sortCriteria, &$rowsFound) {
        $dao = new model\SMSSendingHistoryDAO();
        SMSConfig::createModuleSqlTable($dao);
        $dao->setSortCriteria($sortCriteria);
        if (is_array($searchCriteria)) {
            $dao->applySearchCriteria($searchCriteria);
        }
        $total = $dao->getCount();
        if (!is_null($first) && !is_null($count)) {
            $dao->setLimit($first, $count);
        }
        while($row = $dao->getResult()) {
            $rowsFound[] = $row;
        }
        return $total;
    }

    /**
     * Returns history row for the specified identifier
     * @param int $id row ID
     * @return array Row data
     */
    static public function getById($id) {
        $dao = new model\SMSSendingHistoryDAO();
        return $dao->getById($id);
    }

    /**
     * Adds a new history row
     * @param array $smsInfos SMS data
     * @param string $errorMessage Error message if status is FALSE.
     * @return int The history row identifier in database
     */
    static public function add($smsInfos, $errorMessage) {
        $newRow = $smsInfos;
        $newRow['sending_datetime'] = \General::getCurrentW3CDate(TRUE);
        $newRow['status'] = is_null($errorMessage);
        $newRow['error_message'] = $errorMessage;
        $dao = new model\SMSSendingHistoryDAO();
        $autocommit = !\Database::inTransaction();
        return $dao->store($newRow, $autocommit);
    }

    /**
     * Purge history rows. If search criteria are set, only the matching rows
     * are removed
     * @param array $searchCriteria Filter criteria. Expected keys are 'status',
     * 'start_date' and 'end_date'.
     * @return int The number of rows removed
     */
    static public function purge($searchCriteria) {
        $dao = new model\SMSSendingHistoryDAO();
        if (is_array($searchCriteria)) {
            $dao->applySearchCriteria($searchCriteria);
        } else {
            $dao->applySearchCriteria(['start' => '2020-01-01']);
        }
        return $dao->remove(NULL);
    }

    /**
     * Returns SMS credit balance
     * @return string Number of remaining SMS credits or empty string if credit
     * balance is unknown.
     */
    static public function getCreditBalance() {
        $config = new SMSConfig();
        return $config->credit_balance;
    }

}
