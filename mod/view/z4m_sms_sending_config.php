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
 * ZnetDK 4 Mobile SMS sending module view
 *
 * File version: 1.2
 * Last update: 12/18/2025
 */

// Setting the $color $variable
require 'fragment/color_scheme.php';
?>
<style>
    #z4m-sms-sending-config-container {
        max-width: 640px;
    }
    #z4m-sms-sending-config-form h3 {
        text-transform: uppercase;
    }
</style>
<div id="z4m-sms-sending-config-container" class="w3-auto w3-section w3-card">
    <header class="<?php echo $color['modal_header']; ?> w3-container">
        <h2 class="w3-large"><i class="fa fa-cogs"></i> <?php echo MOD_Z4M_SMSSENDING_CONFIG_TITLE; ?></h2>
    </header>
    <form id="z4m-sms-sending-config-form" class="<?php echo $color['modal_content']; ?>" data-zdk-load="Z4MSMSSendingCtrl:config"
            data-zdk-submit="Z4MSMSSendingCtrl:storeConfig"
            data-notsaved="<?php echo MOD_Z4M_SMSSENDING_CONFIG_FORM_NOT_SAVED_MESSAGE; ?>">
        <section class="w3-container w3-margin-top">
            <label><b><?php echo MOD_Z4M_SMSSENDING_CONFIG_SENDER_NAME_LABEL; ?></b>
                <input class="w3-input w3-border w3-margin-bottom" type="text" name="sender_name" autocomplete="off" placeholder="<?php echo MOD_Z4M_SMSSENDING_CONFIG_SENDER_NAME_PLACEHOLDER; ?>" minlength="3" maxlength="11" required>
            </label>
            <label><b><?php echo MOD_Z4M_SMSSENDING_CONFIG_AUTH_TOKEN_LABEL; ?></b>
                <input class="w3-input w3-border w3-margin-bottom" type="text" name="auth_token" autocomplete="off" placeholder="<?php echo MOD_Z4M_SMSSENDING_CONFIG_AUTH_TOKEN_PLACEHOLDER; ?>" required>
            </label>
            <label><b><?php echo MOD_Z4M_SMSSENDING_CONFIG_ALERT_THRESHOLD_LABEL; ?></b>
                <input class="w3-input w3-border w3-margin-bottom" type="number" name="alert_threshold" min="1" placeholder="<?php echo MOD_Z4M_SMSSENDING_CONFIG_ALERT_THRESHOLD_PLACEHOLDER; ?>">
            </label>
            <label class="w3-show-block w3-margin-bottom">
                <input class="w3-check" type="checkbox" name="is_sending_enabled" value="1">
                <span><?php echo MOD_Z4M_SMSSENDING_CONFIG_OPTIONS_SENDING_LABEL; ?></span>
            </label>
            <label class="w3-show-block w3-margin-bottom">
                <input class="w3-check" type="checkbox" name="is_history_enabled" value="1">
                <span><?php echo MOD_Z4M_SMSSENDING_CONFIG_OPTIONS_HISTORY_LABEL; ?></span>
            </label>
            <div class="w3-padding"></div>
            <label><b><?php echo MOD_Z4M_SMSSENDING_CONFIG_CREDIT_BALANCE_LABEL; ?></b>
                <input class="w3-input w3-border w3-margin-bottom" type="number" name="credit_balance" disabled>
            </label>
            <div class="w3-padding"></div>
            <button class="w3-button w3-block <?php echo $color['btn_submit']; ?> w3-section" type="submit"><i class="fa fa-save fa-lg"></i> <?php echo LC_BTN_SAVE; ?></button>
        </section>
    </form>
    <footer class="w3-container w3-border-top <?php echo $color['modal_footer_border_top']; ?> w3-padding-16 <?php echo $color['modal_footer']; ?>">
        <div class="w3-bar">
            <button class="simulate w3-button w3-right <?php echo $color['btn_action']; ?>" type="button"><i class="fa fa-paper-plane-o fa-lg"></i> <?php echo MOD_Z4M_SMSSENDING_CONFIG_SIMULATION_BUTTON_LABEL; ?></button>
        </div>
    </footer>
</div>
<div id="z4m-sms-sending-config-simulate" class="w3-modal">
    <div class="w3-modal-content w3-card-4">
        <header class="w3-container <?php echo $color['modal_header']; ?>">
            <a class="close w3-button w3-xlarge <?php echo $color['btn_hover']; ?> w3-display-topright" href="javascript:void(0)" aria-label="<?php echo LC_BTN_CLOSE; ?>"><i class="fa fa-times-circle fa-lg" aria-hidden="true" title="<?php echo LC_BTN_CLOSE; ?>"></i></a>
            <h4>
                <i class="fa fa-paper-plane-o fa-lg"></i>
                <span class="title"><?php echo MOD_Z4M_SMSSENDING_CONFIG_SIMULATION_BUTTON_LABEL; ?></span>
            </h4>
        </header>
<?php require 'fragment/send_sms_form.php'; ?>
        <footer class="w3-container w3-border-top <?php echo $color['modal_footer_border_top']; ?> w3-padding-16 <?php echo $color['modal_footer']; ?>">
            <button type="button" class="cancel w3-button <?php echo $color['btn_cancel']; ?>">
                <i class="fa fa-close fa-lg"></i>&nbsp;
                <?php echo LC_BTN_CLOSE; ?>
            </button>
        </footer>
    </div>
</div>
<script>
    <?php if (CFG_DEV_JS_ENABLED) : ?>
    console.log("'z4m_sms_sending_config' ** For debug purpose **");
    <?php endif; ?>
    $(function(){
        const formObj = z4m.form.make('#z4m-sms-sending-config-form', submitCallback);
        formObj.load(0);
        const simulateButton = $('#z4m-sms-sending-config-container button.simulate');
        simulateButton.on('click.z4m_sms_sending_config', function() {
            showSimulationModal();
        });
        function submitCallback(response) {
            if (response.success) {
                formObj.setDataModifiedState(false);
            }
        }
        function showSimulationModal() {
            if (formObj.isModified()) {
                formObj.showError(formObj.element.data('notsaved'));
                return;
            }
            const modal = z4m.modal.make('#z4m-sms-sending-config-simulate'),
                    innerForm = modal.getInnerForm();
            innerForm.init({
                is_simulation: '1',
                message: "<?php echo MOD_Z4M_SMSSENDING_CONFIG_SIMULATION_MESSAGE; ?>"
            });
            modal.open(function(){
                modal.getInnerForm().setDataModifiedState(false);
            }, null, 'recipient_phone_nbr');
        }
    });
</script>
