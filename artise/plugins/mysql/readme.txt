The documentation of MySQL is not available yet. This is a quick reference on how to use it.




/*** CONFIGURING IT ***/


Use the config.xml file to set the connection information:

<connection>
	<id></id>
	<host>127.0.0.1</host>
	<user>root</user>
	<passwd></passwd>
	<dbase></dbase>
	<charset>utf8</charset>
</connection>


To set more than one connection, add more <connection> groups, changing the id for each. The connection with no id specified, will become the default instance's connection.

Another connection can be:

<connection>
	<id>myOtherConnection</id>
	<host>127.0.0.1</host>
	<user>root</user>
	<passwd></passwd>
	<dbase></dbase>
	<charset>utf8</charset>
</connection>




/*** USING IT ***/


// Fetch the default instance of MySQL:
$mysql = $this->devkit()->plugins()->mysql();

// Fetch the myOtherConnection instance of MySQL:
$mysql = $this->devkit()->plugins()->mysql('myOtherConnection');

// Execute a query.
$mysql->execute($query);

// Perform a query to retrieve data. A \Readers\Table is returned (check the online Reference).
$readers_table = $mysql->reader($query);

// Clean string variables to make them SQL-Injection-safe.
$mysql->clean($var1, $var2, ...);


The connection is stablished automatically, only if a query is performed during the page execution.