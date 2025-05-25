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
 * ZnetDK 4 Mobile SMS Sending module view fragment
 *
 * File version: 1.0
 * Last update: 05/24/2025
 */
?>
        <form class="w3-container <?php echo $color['modal_content']; ?>" data-zdk-submit="Z4MSMSSendingCtrl:send">
            <div class="w3-section">
                <label>
                    <input class="w3-check w3-margin-bottom" type="checkbox" name="is_simulation" value="1">
                    <?php echo MOD_Z4M_SMSSENDING_IS_SIMULATION_LABEL; ?>
                </label>
                <br>
                <label><b><?php echo MOD_Z4M_SMSSENDING_MESSAGE_LABEL; ?></b>
                    <input class="w3-input w3-border w3-margin-bottom" type="text" name="message" autocomplete="off" required>
                </label>
                <label><b><?php echo MOD_Z4M_SMSSENDING_RECIPIENT_PHONE_NUMBER_LABEL; ?></b>
                    <input class="w3-input w3-border w3-margin-bottom" type="phone" name="recipient_phone_nbr" minlength="8" maxlength="16" autocomplete="off" required>
                </label>
                <label><b><?php echo MOD_Z4M_SMSSENDING_RECIPIENT_NAME_LABEL; ?></b>
                    <input class="w3-input w3-border w3-margin-bottom" type="text" name="recipient_name" maxlength="50" autocomplete="off">
                </label>
                <label><b><?php echo MOD_Z4M_SMSSENDING_BUSINESS_REFERENCE_LABEL; ?></b>
                    <input class="w3-input w3-border w3-margin-bottom" type="text" name="business_reference" maxlength="100" autocomplete="off">
                </label>
            </div>
            <!-- Submit button -->
            <p class="w3-padding"></p>
            <button class="w3-button w3-block w3-green w3-section w3-padding" type="submit">
                <i class="fa fa-check-circle fa-lg"></i>&nbsp;
                <?php echo MOD_Z4M_SMSSENDING_SEND_BUTTON_LABEL; ?>
            </button>
        </form>