<?php
// ===========================================================================================
//
// Origin: http://github.com/mosbth/Utility
//
// Filename: install_test_environment.php
//
// Description: To install and compile a testenvironment for Apache, PHP and MySQL.
//
// Author: Mikael Roos, mos@bth.se
//
//

// -------------------------------------------------------------------------------------------
//
// Download and build MySQL from source
//
// http://dev.mysql.com/doc/refman/5.1/en/quick-install.html
//
if(isset($_GET['mysql'])) {

	$version 	= $_GET['mysql'];
	$whereami = "/home/mos/test";
	$downloadfile = "mysql-{$version}";
	$downloadtarball = "{$downloadfile}.tar.gz";
	$download = "MySQL-5.1/{$downloadtarball}";
	$install	= "{$whereami}/local/mysql-{$version}";
	
	$cmd = <<<EOD
<pre>
cd {$whereami}/src;
rm -rf {$downloadtarball} {$downloadfile} {$install}
wget http://ftp.sunet.se/pub/unix/databases/relational/mysql/Downloads/{$download};
tar xfz {$downloadtarball};
cd {$downloadfile};
./configure --prefix={$install};
make;
make install;
cp support-files/my-medium.cnf {$whereami}/local/etc/my-{$version}.cnf
cd {$install};
chown -R mysql .;
chgrp -R mysql .;
bin/mysql_install_db --user=mysql;
chown -R root .;
chown -R mysql var;
cd {$whereami};

</pre>
EOD;

/*

shell> bin/mysqld_safe --user=mysql &

*/
	echo $cmd;
}

// -------------------------------------------------------------------------------------------
//
// Download and build Apache from source
//

//wget http://apache.dataphone.se/httpd/httpd-2.2.15.tar.gz

// -------------------------------------------------------------------------------------------
//
// Download and build PHP from source
//

//wget http://se2.php.net/get/php-5.3.2.tar.bz2/from/se.php.net/mirror





?>