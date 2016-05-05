<?php
function isEmailValid($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    	return false;
    }
    return true;
}
?>