<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
  |--------------------------------------------------------------------------
  | File and Directory Modes
  |--------------------------------------------------------------------------
  |
  | These prefs are used when checking and setting modes when working
  | with the file system.  The defaults are fine on servers with proper
  | security, but you may wish (or even need) to change the values in
  | certain environments (Apache running a separate process for each
  | user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
  | always be used to set the mode correctly.
  |
 */
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
  |--------------------------------------------------------------------------
  | File Stream Modes
  |--------------------------------------------------------------------------
  |
  | These modes are used when working with fopen()/popen()
  |
 */

define('FOPEN_READ', 'rb');
define('FOPEN_READ_WRITE', 'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE', 'ab');
define('FOPEN_READ_WRITE_CREATE', 'a+b');
define('FOPEN_WRITE_CREATE_STRICT', 'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');



/* START OF PROMIX SYSTEM CONSTANTS */
/*
  |--------------------------------------------------------------------------
  | Login Error Codes
  |--------------------------------------------------------------------------
  |
  | These codes are used for login returns
  |
 */

define('ERR_USERNAME', 011);
define('ERR_PASSWORD', 012);

/*
  |--------------------------------------------------------------------------
  | Navigation Tab Constants
  |--------------------------------------------------------------------------
  |
  |
 */

define('NAV_HOME', 021);
define('NAV_INVENTORY', 022);
define('NAV_SALES', 023);
define('NAV_PURCHASES', 024);
define('NAV_PRODUCTION', 025);
define('NAV_REPORTS', 026);
define('NAV_USERS', 027);
define('NAV_ACCOUNTING', 028);
define('NAV_WAREHOUSING', 030);
define('NAV_MAINTAINABLE', 031);
define('NAV_SPECIAL_REPORTS', 032);
define('NAV_TRUCKING', 033);

define('SUPERUSER', 'su');
define('STANDARD', 's');
define('ADMIN', 'a');

define('TOGGLE_SALES_AGENT', TRUE);
define('IGNORE_STOCK_PL_ACTION', FALSE);

/*END OF PROMIX SYSTEM CONSTANTS*/

/* End of file constants.php */
/* Location: ./application/config/constants.php */