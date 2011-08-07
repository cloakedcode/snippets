<?php

$db_host = 'localhost';
$db_user = 'username';
$db_pass = 'password';
$db_name = 'database';

$db = mysql_connect($db_host, $db_user, $db_pass);
mysql_select_db($db_name, $db);

$sql = '';
$tables = mysql_query('SHOW TABLES');

while ($t = mysql_fetch_row($tables))
{
    $t = $t[0];
    
    $create_table = mysql_fetch_array(mysql_query("SHOW CREATE TABLE `{$t}`"));
    $sql .= "\nDROP TABLE IF EXISTS `{$t}`;\n";
    $sql .= "{$create_table['Create Table']};\n\n";

    $rows = mysql_query("SELECT * FROM `{$t}`");

    while ($row = mysql_fetch_assoc($rows))
    {
        $sql .= "INSERT INTO `{$t}` SET";

        foreach ($row as $key => $value)
        {
            $sql .= " `{$key}` = '" . mysql_escape_string($value) . "',";
        }
        
        $sql = rtrim($sql, ',') . ";\n";
    }
}

mysql_close($db);

header("Content-disposition: attachment; filename={$db_name}.sql");
header('Content-type: text/sql');

echo $sql;
