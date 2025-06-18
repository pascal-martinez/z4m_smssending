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
 * ZnetDK 4 Mobile SMS sending module class
 *
 * File version: 1.1
 * Last update: 06/17/2025
 */

namespace z4m_smssending\mod;

/**
 * Class to send a SMS via the SMSFactor operator.
 * See API description at https://dev.smsfactor.com/en/api/sms/getting-started
 */
class SMSToSend {
    protected $lastErrorMessage = NULL; // Last error message
    protected $lastHistoryRowId = NULL;
    protected $creditBalance = NULL; // SMS credit balance after sending
    protected $isSendindEnabled = FALSE;
    protected $isHistoryEnabled = FALSE;
    protected $token = NULL;
    protected $sender = NULL;
    protected $isSimulation;
    protected $pushType;

    /**
     * Instantiates a new SMS to send
     * @param string $pushType SMS push type: 'alert' or 'marketing'
     * @param boolean $isSimulation If TRUE, this is a simulation
     * @throws \Exception Bad SMS push type
     */
    public function __construct($pushType, $isSimulation = FALSE) {
        if ($pushType !== 'alert' && $pushType !== 'marketing') {
            throw new \Exception('Unknown SMS push type.');
        }
        $this->isSimulation = $isSimulation;
        $this->pushType = $pushType;
        $this->initConfig();
    }

    /**
     * Initializes the configuration required to send SMS.
     */
    protected function initConfig() {
        $config = new SMSConfig();
        $this->token = $config->auth_token;
        $this->setSender($config->sender_name);
        $this->isHistoryEnabled = $config->isHistoryEnabled();
        $this->isSendindEnabled = $config->isSendingEnabled();
    }

    /**
     * Returns the GET URL required to send the SMS.
     * @param string $message Text of the SMS
     * @param string $recipientPhoneNumber Recipient phone number
     * @param string|NULL $businessReference Optional, a business reference.
     * @return string The encoded GET URL
     */
    protected function getSendingURL($message, $recipientPhoneNumber, $businessReference) {
        $url = $this->isSimulation ? MOD_Z4M_SMSSENDING_HTTP_REQUEST_SIMULATE_URL
                : MOD_Z4M_SMSSENDING_HTTP_REQUEST_SEND_URL;
        $parameters = [
            'text' => $message,
            'sender' => $this->sender,
            'to' => $recipientPhoneNumber,
            'pushtype' => $this->pushType
        ];
        if (!empty($businessReference)) {
            $parameters['gsmsmsid'] = $businessReference;
        }
        return $url . '?' . http_build_query($parameters);
    }

    /**
     * Checks configured API Token and Sender
     * @throws \Exception Token is not set or sender is not set
     */
    protected function checkConfigBeforeSending() {
        if (empty($this->token)) {
            throw new \Exception('Token is not set (L0).');
        }
        if (empty($this->sender)) {
            throw new \Exception('Sender is not set (L0).');
        }
    }

    /**
     * Checks SMS text
     * @param string $message Text of the SMS
     * @throws \Exception Message is empty
     */
    static protected function checkMessageBeforeSending($message) {
        if (empty($message)) {
            throw new \Exception('Message is empty (L0).');
        }
    }

    /**
     * Checks the recipient's phone number.
     * To be valid, only digits are accepted. Leading + sign is also accepted.
     * @param string $phoneNumber The recipient's phone number
     * @throws \Exception Phone number is invalid.
     */
    static public function checkRecipientPhoneNumber($phoneNumber) {
        if (substr($phoneNumber, 0, 1) === '+') {
            $phoneNumber = substr($phoneNumber, 1);
        }
        if (strlen($phoneNumber) < 8 || strlen($phoneNumber) > 15) {
            throw new \Exception('Bad phone number length (L0).');
        }
        if (!ctype_digit($phoneNumber)) {
            throw new \Exception('Phone number is invalid: only digits are expected (L0).');
        }
    }

    /**
     * Executes the HTTP GET Send request
     * @param string $message Text of the message
     * @param string $recipientPhoneNumber Recipient's phone number
     * @param string $businessReference In option, a business reference
     * @return integer|FALSE The message ID or FALSE in case of error.
     */
    protected function execHTTPSendRequest($message, $recipientPhoneNumber, $businessReference) {
        try {
            $this->checkConfigBeforeSending();
            self::checkMessageBeforeSending($message);
            self::checkRecipientPhoneNumber($recipientPhoneNumber);
            $context = stream_context_create([
                'http'=> [
                    'method' => 'GET',
                    'header' => "Accept: application/json\r\n" . "Authorization: Bearer {$this->token}\r\n"
                ]
            ]);
            $response = file_get_contents($this->getSendingURL($message, $recipientPhoneNumber, $businessReference), false, $context);
            $responseHeader = is_array($http_response_header) ? implode(';', $http_response_header) : '???';
            return $this->processHTTPSendRequestResponse($response, $responseHeader);
        } catch (\Exception $ex) {
            $this->setLastErrorMessage($ex->getMessage());
            return FALSE;
        }
    }

    /**
     * Processes the response returned by the SMS sending HTTP GET request.
     * @param string|FALSE $response Response returned by file_get_contents().
     * @param string $responseHeader HTTP response header
     * @return int the SMS id when SMS sending has succeeded.
     * @throws \Exception SMS sending failed
     */
    protected function processHTTPSendRequestResponse($response, $responseHeader) {
        if ($response === FALSE) {
            throw new \Exception("SMS sending failed (L1): {$responseHeader}");
        }
        $infos = json_decode($response, TRUE);
        if (!is_array($infos)) {
            throw new \Exception("SMS sending failed (L2): {$responseHeader}");
        }
        if (key_exists('status', $infos) && key_exists('message', $infos)) {
            if (key_exists('ticket', $infos) && key_exists('credits', $infos)
                    && $infos['status'] === 1 || $infos['status'] === -8 /* Temporary moderation */) {
                $this->lastErrorMessage = NULL;
                $this->creditBalance = $infos['credits'];
                return $infos['ticket']; // Message ID
            }
            $errorMessage = $infos['message'] . (key_exists('details', $infos) ? " {$infos['details']}" : '');
            throw new \Exception("SMS sending failed (L4): status = {$infos['status']}, message = {$errorMessage}");
        }
        $infosAsText = implode(';', $infos);
        throw new \Exception("SMS sending failed (L3): {$responseHeader};{$infosAsText}");
    }

    /**
     * Sets the last error message.
     * @param string $message Error message
     */
    protected function setLastErrorMessage($message) {
        $this->lastErrorMessage = $message;
        if (MOD_Z4M_SMSSENDING_DEBUG_ENABLED === TRUE) {
            \General::writeErrorLog(__METHOD__, $message);
        }
    }

    /**
     * checks whether the sender is valid or not.
     * A sender is valid if it contains alphanumeric characters (letters and
     * digits, no spaces) and if its length is between 3 and 11 characters.
     * @param string $sender The string set for the sender.
     * @throws \Exception Bad length or unexpected characters.
     */
    static public function isSenderValid($sender) {
        if (strlen($sender) < 3 || strlen($sender) > 11) {
            throw new \Exception('Bad sender name length: expected length between 3 and 11 characters.');
        }
        if (!ctype_alnum($sender)) {
            throw new \Exception('Bad sender name characters: only letters and digits are expected.');
        }
    }

    /**
     * Specifies a sender name different than the one defined in the SMS
     * Configuration form.
     * @param string $sender Sender name
     * @throws \Exception Bad number of characters
     */
    public function setSender($sender) {
        self::isSenderValid($sender);
        $this->sender = $sender;
    }

    /**
     * Sends a SMS
     * @param string $message Text of the SMS message
     * @param string $recipientPhoneNumber Recipient's phone number
     * @param string $recipientName Optional, recipient's name
     * @param string $businessReference Optional, business reference
     * @return int Identifier of the sent SMS.
     * @throws \ZDKException SMS sending has failed
     */
    public function send($message, $recipientPhoneNumber, $recipientName = NULL, $businessReference = NULL) {
        $this->creditBalance = NULL; $this->lastErrorMessage = NULL; $messageId = FALSE;
        if ($this->isSendindEnabled) {
            $messageId = $this->execHTTPSendRequest($message, $recipientPhoneNumber, $businessReference);
        } else {
            $this->setLastErrorMessage(MOD_Z4M_SMSSENDING_CONFIG_OPTIONS_SENDING_DISABLED_ERROR . ' (L0)');
        }
        if ($this->isHistoryEnabled) {
            $this->lastHistoryRowId = SMSSendingHistory::add(['message_id' => $messageId,
                'business_reference' => $businessReference, 'message' => $message,
                'recipient_phone_nbr' => $recipientPhoneNumber,
                'recipient_name' => $recipientName, 'sender_name' => $this->sender],
                $this->lastErrorMessage);
        }
        if ($messageId === FALSE) {
            $errorNumber = $this->isSendindEnabled ? '001' : '002';
            throw new \ZDKException("STS-{$errorNumber}: {$this->lastErrorMessage}");
        } elseif ($this->isSimulation === FALSE && !is_null($this->creditBalance)) {
            SMSConfig::updateCreditBalance($this->creditBalance);
        }
        return $messageId;
    }

    /**
     * Returns the last SMS sent history identifier in database
     * @return int Row identifier
     */
    public function getLastHistoryRowId() {
        return $this->lastHistoryRowId;
    }

    /**
     * Get the last error message on SMS sending.
     * @return int|NULL Error level or NULL if no error
     */
    public function getLastErrorMessage() {
        return $this->lastErrorMessage;
    }

    /**
     * Get the last error level on SMS sending.
     * This error level helps you to know if the same SMS can be sent again when
     * a network error occurred.
     * Retry to send the SMS only if error level is 1 or 2.
     * The 5 error levels are:
     * 0: error detected before sending SMS: token, sender or message are not
     *    properly set (SMS has not been sent)
     * 1: error executing GET HTTP request (SMS has not been sent)
     * 2: The response returned by the SMS operator is not in JSON format (SMS
     *    sending request has not been processed).
     * 3: The response returned by the SMS operator is in JSON format but
     *    expected properties are missing (SMS sending request has not been
     *    processed).
     * 4: error returned by the SMS operator if API token or phone number is
     *    invalid (SMS has been received by the SMS operator but has not been
     *    sent because API token or recipient's phone number is invalid or
     *    because SMS credits are insufficient.).
     * @return int|NULL Error level or NULL if no error. If the error level is
     * unkown, -1 is returned.
     */
    public function getLastErrorLevel() {
        if (is_null($this->lastErrorMessage)) {
            return NULL;
        }
        for ($index = 0; $index <= 4; $index++) {
            if (str_contains($this->lastErrorMessage, "(L{$index}")) {
                return $index;
            }
        }
        return -1;
    }

    /**
     * Get the credit balance returned by SMS Factor after sending the SMS.
     * @return int|NULL The credit balance or NULL if no SMS has been sent yet.
     */
    public function getNewCreditBalance() {
        return $this->creditBalance;
    }

}
