<?php
// ===========================================================================================
//
// Origin: http://github.com/mosbth/Utility
//
// Filename: check_php_config.php
//
// Description: Shows various details on the PHP environment. Whats installed
// and whats not.
//
// Author: Mikael Roos, mos@bth.se
//
// Change history:
// 
//

// -------------------------------------------------------------------------------------------
//
// Show output from phpinfo() if enabled
//
if(phpinfo()) {
	echo phpinfo();
}




?>