<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$tables = DB::select('SHOW TABLES');
$sql = '';

foreach ($tables as $tableObj) {
    $tableArray = (array) $tableObj;
    $tableName = reset($tableArray);

    $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`")[0];
    $createSql = ((array) $createTable)['Create Table'];

    $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
    $sql .= $createSql.";\n\n";
}

file_put_contents('schema_dump.sql', $sql);
echo 'Schema dumped to schema_dump.sql';
