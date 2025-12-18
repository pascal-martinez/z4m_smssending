# CHANGE LOG: SMS Sending (z4m_smssending)

## Version 1.2, 2025-12-18
- BUG FIXING: error "Uncaught TypeError: formObj.element.date is not a function" displayed in browser console from the 
**SMS sending configuration** page when clicking the **Send a simulation SMS...** button while the form is modified and not saved.  

## Version 1.1, 2025-06-18
- CHANGE: display of the SMS ID in the SMS Sent list.
- CHANGE: display of the SMS credit balance in the SMS Config form.
- BUG FIXING: the `SMSToSend::send()` method returned `NULL` instead of triggering an exception when SMS sending was disabled.

## Version 1.0, 2025-05-25
First version.