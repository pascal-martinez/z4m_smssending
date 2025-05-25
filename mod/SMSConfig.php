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
 * ZnetDK 4 Mobile SMS module Configuration class
 *
 * File version: 1.0
 * Last update: 05/17/2025
 */

namespace z4m_smssending\mod;

/**
 * Configuration of SMS sending
 */
class SMSConfig {

    protected $configuration = NULL;

    /**
     * Instantiates a new SMS configuration object.
     * @param boolean $initFromHttpRequest Should we initialize the parameters
     * from the HTTP request POST values? If FALSE, configuration is read from
     * the database, otherwise is set from the current HTTP request POST
     * parameters.
     */
    public function __construct($initFromHttpRequest = FALSE) {
        if ($initFromHttpRequest) {
            $this->initFromHttpRequest();
        } else {
            $this->fetchData();
        }
    }

    /**
     * Initializes the configuration from the current HTTP request POST values.
     */
    protected function initFromHttpRequest() {
        $request = new \Request();
        $this->configuration = $request->getValuesAsMap('sender_name',
            'auth_token', 'alert_threshold');
        $this->configuration['id'] = 1;
        $this->configuration['is_sending_enabled'] = $request->is_sending_enabled === '1' ? 1 : 0;
        $this->configuration['is_history_enabled'] = $request->is_history_enabled === '1' ? 1 : 0;
    }

    /**
     * Validates configuration data.
     * @param boolean $throwException If TRUE, an exception is triggered if
     * validation failed.
     * @return TRUE|array TRUE if validation has succeeded otherwise
     * informations about failed validation (keys are 'message' and 'property').
     * @throws \ZDKException Validation error if $throwException is TRUE.
     */
    public function validate($throwException = FALSE) {
        $validator = new validator\ConfigValidator();
        $validator->setValues($this->configuration);
        $validator->setCheckingMissingValues();
        if (!$validator->validate()) {
            $property = $validator->getErrorVariable();
            $error = $validator->getErrorMessage();
            if ($throwException) {
                throw new \ZDKException("SCG-001: [{$property}] {$error}");
            }
            return ['message' => $error, 'property' => $property];
        }
        return TRUE;
    }

    /**
     * Fetches SMS configuration
     */
    protected function fetchData() {
        $dao = new model\ConfigurationDAO();
        $this->createModuleSqlTable($dao);
        $row = $dao->getById(1);
        if (is_array($row)) {
            $this->configuration = $row;
        } else { // SQL table is empty...
            throw new \ZDKException('SCG-002: no configuration found in database.');
        }
    }

    /**
     * Returns the property value.
     * @param type $property
     * @return string The value matching the specified property
     * @throws \ZDKException Unknown property
     */
    public function __get($property) {
        if (key_exists($property, $this->configuration)) {
            return $this->configuration[$property];
        }
        throw new \ZDKException("SCG-003: unknown property '{$property}'.");
    }

    /**
     * Returns configuration as a database row.
     * @return array Configuration as database row.
     */
    public function getRow() {
        return $this->configuration;
    }

    /**
     * Indicates whether sent SMS are historicized or not
     * @return boolean TRUE if SMS sending history is enabled.
     */
    public function isHistoryEnabled() {
        return $this->configuration['is_history_enabled'] === '1';
    }

    /**
     * Indicates whether SMS sending is enabled or not
     * @return boolean TRUE if SMS sending is enabled.
     */
    public function isSendingEnabled() {
        return $this->configuration['is_sending_enabled'] === '1';
    }

    /**
     * Indicates whether SMS credit alert threshold has been reached or not.
     * @return boolean TRUE if the alert threshold has been reached, FALSE
     * otherwise.
     */
    public function hasCreditAlertThresholdBeenReached() {
        return is_numeric($this->configuration['credit_balance'])
            && is_numeric($this->configuration['alert_threshold'])
            && $this->configuration['alert_threshold'] > 0
            ? $this->configuration['alert_threshold'] > $this->configuration['credit_balance']
            : FALSE;
    }

    /**
     * Stores the configuration
     * @param boolean $autocommit If FALSE, a SQL transaction must be started
     * before calling this method.
     */
    public function store($autocommit = TRUE) {
        $dao = new model\ConfigurationDAO();
        $dao->store($this->configuration, $autocommit);
    }

    /**
     * Updates credit balance
     * @param int $newCreditBalance New number of remaining SMS credits
     * @param boolean $autocommit If FALSE, a SQL transaction must be started
     * before calling this method.
     */
    static public function updateCreditBalance($newCreditBalance, $autocommit = TRUE) {
        $dao = new model\ConfigurationDAO();
        $dao->store(['id' => 1, 'credit_balance' => $newCreditBalance], $autocommit);
    }

    /**
     * Create the SQL table required for the module.
     * The table is created from the SQL script defined via the
     * MOD_Z4M_SMSSENDING_SQL_SCRIPT_PATH constant.
     * @param DAO $dao DAO for which existence is checked
     * @throws \Exception SQL script is missing and SQL table creation failed.
     */
    static public function createModuleSqlTable($dao) {
        if ($dao->doesTableExist()) {
            return;
        }
        if (!file_exists(MOD_Z4M_SMSSENDING_SQL_SCRIPT_PATH)) {
            $error = "SQL script '" . MOD_Z4M_SMSSENDING_SQL_SCRIPT_PATH . "' is missing.";
            throw new \Exception($error);
        }
        $sqlScript = file_get_contents(MOD_Z4M_SMSSENDING_SQL_SCRIPT_PATH);
        $db = \Database::getApplDbConnection();
        try {
            $db->exec($sqlScript);
        } catch (\Exception $ex) {
            \General::writeErrorLog(__METHOD__, $ex->getMessage());
            throw new \Exception("Error executing 'z4m_smssending' module SQL script.");
        }

    }
}
