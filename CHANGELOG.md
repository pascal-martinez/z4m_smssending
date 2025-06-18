# CHANGE LOG: SMS Sending (z4m_smssending)

## Version 1.1, 2025-06-18
- CHANGE : display of the SMS ID in the SMS Sent list.
- CHANGE : display of the SMS credit balance in the SMS Config form.
- BUG FIXING: the `SMSToSend::send()` method returned `NULL` instead of triggering an exception when SMS sending was disabled.

## Version 1.0, 2025-05-25
First version.