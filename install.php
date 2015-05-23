<?php
namespace frameworks\adapt;
/*
 * Prevent direct access
 */
defined('ADAPT_STARTED') or die;


$adapt = $GLOBALS['adapt'];
$sql = $adapt->data_source->sql;

$adapt->data_source->on('adapt.error', function($e){
    print "<h3>Data source error</h3>";
    print "<pre>" . $e['event_data']['error'] . "</pre>";
});

$sql->on('adapt.error', function($e){
    print "<h3>SQL error</h3>";
    print "<pre>" . $e['event_data']['error'] . "</pre>";
});

//print new \frameworks\adapt\html_pre(print_r($adapt->data_source, true));

if ($sql && $sql instanceof \frameworks\adapt\sql){
    
    if ($adapt->data_source instanceof data_source_mysql){
        /*
         * This is a hack to set the MySQL engine type
         * because MySQL 5.6 and PHP 5.4 (mysqli)
         * do not work correctly and setting the engine
         * on the CREATE TABLE statement causes the
         * statement to fail with varying errors
         */
        $engine = $adapt->setting('mysql.default_engine');
        if ($engine){
            $engine_fix = new \frameworks\adapt\sql("SET stoarge_engine={$engine};", $adapt->data_source);
            $engine_fix->execute();
        }
    }
    
    /* Lets install the base classes */
    $sql->create_table('data_type')
        ->add('data_type_id', 'bigint')
        ->add('bundle_name', 'varchar(128)')
        ->add('name', 'varchar(32)', false)
        ->add('based_on_data_type', 'text')
        ->add('validator', 'varchar(64)')
        ->add('formatter', 'varchar(64)')
        ->add('unformatter', 'varchar(64)')
        ->add('datetime_format', 'varchar(64)')
        ->add('max_length', 'int')
        ->add('date_created', 'datetime')
        ->add('date_modified', 'timestamp')
        ->add('date_deleted', 'datetime')
        ->primary_key('data_type_id')
        ->execute();
    
    
    $sql->create_table('field')
        ->add('field_id', 'bigint')
        ->add('bundle_name', 'varchar(128)')
        ->add('table_name', 'varchar(64)')
        ->add('field_name', 'varchar(64)')
        ->add('referenced_table_name', 'varchar(64)')
        ->add('referenced_field_name', 'varchar(64)')
        ->add('label', 'varchar(128)')
        ->add('placeholder_label', 'varchar(128)')
        ->add('description', 'text')
        ->add('data_type_id', 'bigint')
        ->add('primary_key', "enum('Yes', 'No')", false, 'Yes')
        ->add('signed', "enum('Yes', 'No')", false, 'Yes')
        ->add('nullable', "enum('Yes', 'No')", false, 'Yes')
        ->add('auto_increment', "enum('Yes', 'No')", false, 'No')
        ->add('timestamp', "enum('Yes', 'No')", false, 'No')
        ->add('max_length', "int")
        ->add('default_value', "varchar(128)")
        ->add('allowed_values', 'text')
        ->add('lookup_table', 'varchar(64)')
        ->add('depends_on_table_name', 'varchar(64)')
        ->add('depends_on_field_name', 'varchar(64)')
        ->add('depends_on_value', 'varchar(64)')
        ->add('date_created', 'datetime')
        ->add('date_modified', 'timestamp')
        ->add('date_deleted', 'datetime')
        ->primary_key('field_id')
        ->foreign_key('data_type_id', 'data_type', 'data_type_id')
        ->execute();
    
    /* We need to populate the schema manually in $adapt->data_source */
    $adapt->data_source->data_types = array(
        array(
            'data_type_id' => 1,
            'bundle_name' => 'adapt',
            'name' => 'tinyint',
            'validator' => 'tinyint',
            'formatter' => null,
            'unformatter' => null,
            'datetime_format' => null,
            'max_length' => null,
            'date_created' => null
        ),
        array(
            'data_type_id' => 2,
            'bundle_name' => 'adapt',
            'name' => 'smallint',
            'validator' => 'smallint',
            'formatter' => null,
            'unformatter' => null,
            'datetime_format' => null,
            'max_length' => null,
            'date_created' => null
        ),
        array(
            'data_type_id' => 3,
            'bundle_name' => 'adapt',
            'name' => 'mediumint',
            'validator' => 'mediumint',
            'formatter' => null,
            'unformatter' => null,
            'datetime_format' => null,
            'max_length' => null,
            'date_created' => null
        ),
        array(
            'data_type_id' => 4,
            'bundle_name' => 'adapt',
            'name' => 'int',
            'validator' => 'int',
            'formatter' => null,
            'unformatter' => null,
            'datetime_format' => null,
            'max_length' => null,
            'date_created' => null
        ),
        array(
            'data_type_id' => 5,
            'bundle_name' => 'adapt',
            'name' => 'integer',
            'validator' => 'int',
            'formatter' => null,
            'unformatter' => null,
            'datetime_format' => null,
            'max_length' => null,
            'date_created' => null
        ),
        array(
            'data_type_id' => 6,
            'bundle_name' => 'adapt',
            'name' => 'bigint',
            'validator' => 'bigint',
            'formatter' => null,
            'unformatter' => null,
            'datetime_format' => null,
            'max_length' => null,
            'date_created' => null
        ),
        array(
            'data_type_id' => 7,
            'bundle_name' => 'adapt',
            'name' => 'serial',
            'validator' => 'serial',
            'formatter' => null,
            'unformatter' => null,
            'datetime_format' => null,
            'max_length' => null,
            'date_created' => null
        ),
        array(
            'data_type_id' => 8,
            'bundle_name' => 'adapt',
            'name' => 'bit',
            'validator' => 'bit',
            'formatter' => null,
            'unformatter' => null,
            'datetime_format' => null,
            'max_length' => null,
            'date_created' => null
        ),
        array(
            'data_type_id' => 9,
            'bundle_name' => 'adapt',
            'name' => 'boolean',
            'validator' => 'boolean',
            'formatter' => null,
            'unformatter' => null,
            'datetime_format' => null,
            'max_length' => null,
            'date_created' => null
        ),
        array(
            'data_type_id' => 10,
            'bundle_name' => 'adapt',
            'name' => 'bool',
            'validator' => 'boolean',
            'formatter' => null,
            'unformatter' => null,
            'datetime_format' => null,
            'max_length' => null,
            'date_created' => null
        ),
        array(
            'data_type_id' => 11,
            'bundle_name' => 'adapt',
            'name' => 'float',
            'validator' => 'float',
            'formatter' => null,
            'unformatter' => null,
            'datetime_format' => null,
            'max_length' => null,
            'date_created' => null
        ),
        array(
            'data_type_id' => 12,
            'bundle_name' => 'adapt',
            'name' => 'double',
            'validator' => 'double',
            'formatter' => null,
            'unformatter' => null,
            'datetime_format' => null,
            'max_length' => null,
            'date_created' => null
        ),
        array(
            'data_type_id' => 13,
            'bundle_name' => 'adapt',
            'name' => 'decimal',
            'validator' => 'decimal',
            'formatter' => null,
            'unformatter' => null,
            'datetime_format' => null,
            'max_length' => null,
            'date_created' => null
        ),
        array(
            'data_type_id' => 14,
            'bundle_name' => 'adapt',
            'name' => 'char',
            'validator' => 'char',
            'formatter' => null,
            'unformatter' => null,
            'datetime_format' => null,
            'max_length' => null,
            'date_created' => null
        ),
        array(
            'data_type_id' => 15,
            'bundle_name' => 'adapt',
            'name' => 'binary',
            'validator' => 'binary',
            'formatter' => null,
            'unformatter' => null,
            'datetime_format' => null,
            'max_length' => null,
            'date_created' => null
        ),
        array(
            'data_type_id' => 16,
            'bundle_name' => 'adapt',
            'name' => 'varchar',
            'validator' => 'varchar',
            'formatter' => null,
            'unformatter' => null,
            'datetime_format' => null,
            'max_length' => null,
            'date_created' => null
        ),
        array(
            'data_type_id' => 17,
            'bundle_name' => 'adapt',
            'name' => 'varbinary',
            'validator' => 'varbinary',
            'formatter' => null,
            'unformatter' => null,
            'datetime_format' => null,
            'max_length' => null,
            'date_created' => null
        ),
        array(
            'data_type_id' => 18,
            'bundle_name' => 'adapt',
            'name' => 'tinyblob',
            'validator' => 'tinyblob',
            'formatter' => null,
            'unformatter' => null,
            'datetime_format' => null,
            'max_length' => null,
            'date_created' => null
        ),
        array(
            'data_type_id' => 19,
            'bundle_name' => 'adapt',
            'name' => 'blob',
            'validator' => 'blob',
            'formatter' => null,
            'unformatter' => null,
            'datetime_format' => null,
            'max_length' => null,
            'date_created' => null
        ),
        array(
            'data_type_id' => 20,
            'bundle_name' => 'adapt',
            'name' => 'mediumblob',
            'validator' => 'mediumblob',
            'formatter' => null,
            'unformatter' => null,
            'datetime_format' => null,
            'max_length' => null,
            'date_created' => null
        ),
        array(
            'data_type_id' => 21,
            'bundle_name' => 'adapt',
            'name' => 'longblob',
            'validator' => 'longblob',
            'formatter' => null,
            'unformatter' => null,
            'datetime_format' => null,
            'max_length' => null,
            'date_created' => null
        ),
        array(
            'data_type_id' => 22,
            'bundle_name' => 'adapt',
            'name' => 'tinytext',
            'validator' => 'tinytext',
            'formatter' => null,
            'unformatter' => null,
            'datetime_format' => null,
            'max_length' => null,
            'date_created' => null
        ),
        array(
            'data_type_id' => 23,
            'bundle_name' => 'adapt',
            'name' => 'text',
            'validator' => 'text',
            'formatter' => null,
            'unformatter' => null,
            'datetime_format' => null,
            'max_length' => null,
            'date_created' => null
        ),
        array(
            'data_type_id' => 24,
            'bundle_name' => 'adapt',
            'name' => 'mediumtext',
            'validator' => 'mediumtext',
            'formatter' => null,
            'unformatter' => null,
            'datetime_format' => null,
            'max_length' => null,
            'date_created' => null
        ),
        array(
            'data_type_id' => 25,
            'bundle_name' => 'adapt',
            'name' => 'longtext',
            'validator' => 'longtext',
            'formatter' => null,
            'unformatter' => null,
            'datetime_format' => null,
            'max_length' => null,
            'date_created' => null
        ),
        array(
            'data_type_id' => 26,
            'bundle_name' => 'adapt',
            'name' => 'enum',
            'validator' => null,
            'formatter' => null,
            'unformatter' => null,
            'datetime_format' => null,
            'max_length' => null,
            'date_created' => null
        ),
        array(
            'data_type_id' => 27,
            'bundle_name' => 'adapt',
            'name' => 'set',
            'validator' => null,
            'formatter' => null,
            'unformatter' => null,
            'datetime_format' => null,
            'max_length' => null,
            'date_created' => null
        ),
        array(
            'data_type_id' => 28,
            'bundle_name' => 'adapt',
            'name' => 'year',
            'validator' => 'year',
            'formatter' => null,
            'unformatter' => null,
            'datetime_format' => 'Y',
            'max_length' => 4,
            'date_created' => null
        ),
        array(
            'data_type_id' => 29,
            'bundle_name' => 'adapt',
            'name' => 'date',
            'validator' => 'date',
            'formatter' => 'date',
            'unformatter' => 'date',
            'datetime_format' => 'Y-m-d',
            'max_length' => null,
            'date_created' => null
        ),
        array(
            'data_type_id' => 30,
            'bundle_name' => 'adapt',
            'name' => 'time',
            'validator' => 'time',
            'formatter' => 'time',
            'unformatter' => 'time',
            'datetime_format' => 'H:i:s',
            'max_length' => null,
            'date_created' => null
        ),
        array(
            'data_type_id' => 31,
            'bundle_name' => 'adapt',
            'name' => 'datetime',
            'validator' => 'datetime',
            'formatter' => 'datetime',
            'unformatter' => 'datetime',
            'datetime_format' => 'Y-m-d H:i:s',
            'max_length' => null,
            'date_created' => null
        ),
        array(
            'data_type_id' => 32,
            'bundle_name' => 'adapt',
            'name' => 'timestamp',
            'validator' => 'datetime',
            'formatter' => 'datetime',
            'unformatter' => 'datetime',
            'datetime_format' => 'Y-m-d H:i:s',
            'max_length' => null,
            'date_created' => null
        )
    );
    
    $adapt->data_source->schema = array(
        array(
            'field_id' => 1,
            'bundle_name' => 'adapt',
            'table_name' => 'data_type',
            'field_name' => 'data_type_id',
            'referenced_table_name' => null,
            'referenced_field_name' => null,
            'label' => 'Data type #',
            'placeholder_label' => null,
            'description' => null,
            'data_type_id' => 6,
            'primary_key' => 'Yes',
            'signed' => 'Yes',
            'nullable' => 'Yes',
            'auto_increment' => 'Yes',
            'timestamp' => 'No',
            'max_length' => null,
            'default_value' => null,
            'allowed_values' => null,
            'lookup_table' => null,
            'depends_on_table_name' => null,
            'depends_on_field_name' => null,
            'depends_on_value' => null,
            'date_created' => null
        ),
        array(
            'field_id' => 2,
            'bundle_name' => 'adapt',
            'table_name' => 'data_type',
            'field_name' => 'bundle_name',
            'referenced_table_name' => null,
            'referenced_field_name' => null,
            'label' => 'Bundle',
            'placeholder_label' => null,
            'description' => null,
            'data_type_id' => 16,
            'primary_key' => 'No',
            'signed' => 'No',
            'nullable' => 'No',
            'auto_increment' => 'No',
            'timestamp' => 'No',
            'max_length' => 128,
            'default_value' => null,
            'allowed_values' => null,
            'lookup_table' => null,
            'depends_on_table_name' => null,
            'depends_on_field_name' => null,
            'depends_on_value' => null,
            'date_created' => null
        ),
        array(
            'field_id' => 3,
            'bundle_name' => 'adapt',
            'table_name' => 'data_type',
            'field_name' => 'name',
            'referenced_table_name' => null,
            'referenced_field_name' => null,
            'label' => 'Name',
            'placeholder_label' => null,
            'description' => null,
            'data_type_id' => 16,
            'primary_key' => 'No',
            'signed' => 'No',
            'nullable' => 'No',
            'auto_increment' => 'No',
            'timestamp' => 'No',
            'max_length' => 32,
            'default_value' => null,
            'allowed_values' => null,
            'lookup_table' => null,
            'depends_on_table_name' => null,
            'depends_on_field_name' => null,
            'depends_on_value' => null,
            'date_created' => null
        ),
        array(
            'field_id' => 4,
            'bundle_name' => 'adapt',
            'table_name' => 'data_type',
            'field_name' => 'based_on_data_type',
            'referenced_table_name' => null,
            'referenced_field_name' => null,
            'label' => 'Based on',
            'placeholder_label' => null,
            'description' => null,
            'data_type_id' => 23,
            'primary_key' => 'No',
            'signed' => 'No',
            'nullable' => 'Yes',
            'auto_increment' => 'No',
            'timestamp' => 'No',
            'max_length' => 64,
            'default_value' => null,
            'allowed_values' => null,
            'lookup_table' => null,
            'depends_on_table_name' => null,
            'depends_on_field_name' => null,
            'depends_on_value' => null,
            'date_created' => null
        ),
        array(
            'field_id' => 5,
            'bundle_name' => 'adapt',
            'table_name' => 'data_type',
            'field_name' => 'validator',
            'referenced_table_name' => null,
            'referenced_field_name' => null,
            'label' => 'Validator',
            'placeholder_label' => null,
            'description' => null,
            'data_type_id' => 16,
            'primary_key' => 'No',
            'signed' => 'No',
            'nullable' => 'Yes',
            'auto_increment' => 'No',
            'timestamp' => 'No',
            'max_length' => 64,
            'default_value' => null,
            'allowed_values' => null,
            'lookup_table' => null,
            'depends_on_table_name' => null,
            'depends_on_field_name' => null,
            'depends_on_value' => null,
            'date_created' => null
        ),
        array(
            'field_id' => 6,
            'bundle_name' => 'adapt',
            'table_name' => 'data_type',
            'field_name' => 'formatter',
            'referenced_table_name' => null,
            'referenced_field_name' => null,
            'label' => 'Formatter',
            'placeholder_label' => null,
            'description' => null,
            'data_type_id' => 16,
            'primary_key' => 'No',
            'signed' => 'No',
            'nullable' => 'Yes',
            'auto_increment' => 'No',
            'timestamp' => 'No',
            'max_length' => 64,
            'default_value' => null,
            'allowed_values' => null,
            'lookup_table' => null,
            'depends_on_table_name' => null,
            'depends_on_field_name' => null,
            'depends_on_value' => null,
            'date_created' => null
        ),
        array(
            'field_id' => 7,
            'bundle_name' => 'adapt',
            'table_name' => 'data_type',
            'field_name' => 'unformatter',
            'referenced_table_name' => null,
            'referenced_field_name' => null,
            'label' => 'Unformatter',
            'placeholder_label' => null,
            'description' => null,
            'data_type_id' => 16,
            'primary_key' => 'No',
            'signed' => 'No',
            'nullable' => 'Yes',
            'auto_increment' => 'No',
            'timestamp' => 'No',
            'max_length' => 64,
            'default_value' => null,
            'allowed_values' => null,
            'lookup_table' => null,
            'depends_on_table_name' => null,
            'depends_on_field_name' => null,
            'depends_on_value' => null,
            'date_created' => null
        ),
        array(
            'field_id' => 8,
            'bundle_name' => 'adapt',
            'table_name' => 'data_type',
            'field_name' => 'datetime_format',
            'referenced_table_name' => null,
            'referenced_field_name' => null,
            'label' => 'Datetime format',
            'placeholder_label' => null,
            'description' => null,
            'data_type_id' => 16,
            'primary_key' => 'No',
            'signed' => 'No',
            'nullable' => 'Yes',
            'auto_increment' => 'No',
            'timestamp' => 'No',
            'max_length' => 64,
            'default_value' => null,
            'allowed_values' => null,
            'lookup_table' => null,
            'depends_on_table_name' => null,
            'depends_on_field_name' => null,
            'depends_on_value' => null,
            'date_created' => null
        ),
        array(
            'field_id' => 9,
            'bundle_name' => 'adapt',
            'table_name' => 'data_type',
            'field_name' => 'max_length',
            'referenced_table_name' => null,
            'referenced_field_name' => null,
            'label' => 'Max length',
            'placeholder_label' => null,
            'description' => null,
            'data_type_id' => 4,
            'primary_key' => 'No',
            'signed' => 'No',
            'nullable' => 'Yes',
            'auto_increment' => 'No',
            'timestamp' => 'No',
            'max_length' => null,
            'default_value' => null,
            'allowed_values' => null,
            'lookup_table' => null,
            'depends_on_table_name' => null,
            'depends_on_field_name' => null,
            'depends_on_value' => null,
            'date_created' => null
        ),
        array(
            'field_id' => 10,
            'bundle_name' => 'adapt',
            'table_name' => 'data_type',
            'field_name' => 'date_created',
            'referenced_table_name' => null,
            'referenced_field_name' => null,
            'label' => 'Date created',
            'placeholder_label' => null,
            'description' => null,
            'data_type_id' => 31,
            'primary_key' => 'No',
            'signed' => 'No',
            'nullable' => 'Yes',
            'auto_increment' => 'No',
            'timestamp' => 'No',
            'max_length' => null,
            'default_value' => null,
            'allowed_values' => null,
            'lookup_table' => null,
            'depends_on_table_name' => null,
            'depends_on_field_name' => null,
            'depends_on_value' => null,
            'date_created' => null
        ),
        array(
            'field_id' => 11,
            'bundle_name' => 'adapt',
            'table_name' => 'data_type',
            'field_name' => 'date_modified',
            'referenced_table_name' => null,
            'referenced_field_name' => null,
            'label' => 'Last modified',
            'placeholder_label' => null,
            'description' => null,
            'data_type_id' => 32,
            'primary_key' => 'No',
            'signed' => 'No',
            'nullable' => 'Yes',
            'auto_increment' => 'No',
            'timestamp' => 'Yes',
            'max_length' => null,
            'default_value' => null,
            'allowed_values' => null,
            'lookup_table' => null,
            'depends_on_table_name' => null,
            'depends_on_field_name' => null,
            'depends_on_value' => null,
            'date_created' => null
        ),
        array(
            'field_id' => 12,
            'bundle_name' => 'adapt',
            'table_name' => 'data_type',
            'field_name' => 'date_deleted',
            'referenced_table_name' => null,
            'referenced_field_name' => null,
            'label' => 'Date deleted',
            'placeholder_label' => null,
            'description' => null,
            'data_type_id' => 31,
            'primary_key' => 'No',
            'signed' => 'No',
            'nullable' => 'Yes',
            'auto_increment' => 'No',
            'timestamp' => 'No',
            'max_length' => null,
            'default_value' => null,
            'allowed_values' => null,
            'lookup_table' => null,
            'depends_on_table_name' => null,
            'depends_on_field_name' => null,
            'depends_on_value' => null,
            'date_created' => null
        ),
        /* Field */
        array(
            'field_id' => 13,
            'bundle_name' => 'adapt',
            'table_name' => 'field',
            'field_name' => 'field_id',
            'referenced_table_name' => null,
            'referenced_field_name' => null,
            'label' => 'Field #',
            'placeholder_label' => null,
            'description' => null,
            'data_type_id' => 6,
            'primary_key' => 'Yes',
            'signed' => 'No',
            'nullable' => 'Yes',
            'auto_increment' => 'Yes',
            'timestamp' => 'No',
            'max_length' => null,
            'default_value' => null,
            'allowed_values' => null,
            'lookup_table' => null,
            'depends_on_table_name' => null,
            'depends_on_field_name' => null,
            'depends_on_value' => null,
            'date_created' => null
        ),
        array(
            'field_id' => 14,
            'bundle_name' => 'adapt',
            'table_name' => 'field',
            'field_name' => 'bundle_name',
            'referenced_table_name' => null,
            'referenced_field_name' => null,
            'label' => 'Bundle',
            'placeholder_label' => null,
            'description' => null,
            'data_type_id' => 16,
            'primary_key' => 'No',
            'signed' => 'No',
            'nullable' => 'No',
            'auto_increment' => 'No',
            'timestamp' => 'No',
            'max_length' => 128,
            'default_value' => null,
            'allowed_values' => null,
            'lookup_table' => null,
            'depends_on_table_name' => null,
            'depends_on_field_name' => null,
            'depends_on_value' => null,
            'date_created' => null
        ),
        array(
            'field_id' => 15,
            'bundle_name' => 'adapt',
            'table_name' => 'field',
            'field_name' => 'table_name',
            'referenced_table_name' => null,
            'referenced_field_name' => null,
            'label' => 'Table name',
            'placeholder_label' => null,
            'description' => null,
            'data_type_id' => 16,
            'primary_key' => 'No',
            'signed' => 'No',
            'nullable' => 'No',
            'auto_increment' => 'No',
            'timestamp' => 'No',
            'max_length' => 64,
            'default_value' => null,
            'allowed_values' => null,
            'lookup_table' => null,
            'depends_on_table_name' => null,
            'depends_on_field_name' => null,
            'depends_on_value' => null,
            'date_created' => null
        ),
        array(
            'field_id' => 16,
            'bundle_name' => 'adapt',
            'table_name' => 'field',
            'field_name' => 'field_name',
            'referenced_table_name' => null,
            'referenced_field_name' => null,
            'label' => 'Field name',
            'placeholder_label' => null,
            'description' => null,
            'data_type_id' => 16,
            'primary_key' => 'No',
            'signed' => 'No',
            'nullable' => 'No',
            'auto_increment' => 'No',
            'timestamp' => 'No',
            'max_length' => 64,
            'default_value' => null,
            'allowed_values' => null,
            'lookup_table' => null,
            'depends_on_table_name' => null,
            'depends_on_field_name' => null,
            'depends_on_value' => null,
            'date_created' => null
        ),
        array(
            'field_id' => 17,
            'bundle_name' => 'adapt',
            'table_name' => 'field',
            'field_name' => 'referenced_table_name',
            'referenced_table_name' => null,
            'referenced_field_name' => null,
            'label' => 'Referenced table name',
            'placeholder_label' => null,
            'description' => null,
            'data_type_id' => 16,
            'primary_key' => 'No',
            'signed' => 'No',
            'nullable' => 'Yes',
            'auto_increment' => 'No',
            'timestamp' => 'No',
            'max_length' => 64,
            'default_value' => null,
            'allowed_values' => null,
            'lookup_table' => null,
            'depends_on_table_name' => null,
            'depends_on_field_name' => null,
            'depends_on_value' => null,
            'date_created' => null
        ),
        array(
            'field_id' => 18,
            'bundle_name' => 'adapt',
            'table_name' => 'field',
            'field_name' => 'referenced_field_name',
            'referenced_table_name' => null,
            'referenced_field_name' => null,
            'label' => 'Referenced field name',
            'placeholder_label' => null,
            'description' => null,
            'data_type_id' => 16,
            'primary_key' => 'No',
            'signed' => 'No',
            'nullable' => 'Yes',
            'auto_increment' => 'No',
            'timestamp' => 'No',
            'max_length' => 64,
            'default_value' => null,
            'allowed_values' => null,
            'lookup_table' => null,
            'depends_on_table_name' => null,
            'depends_on_field_name' => null,
            'depends_on_value' => null,
            'date_created' => null
        ),
        array(
            'field_id' => 19,
            'bundle_name' => 'adapt',
            'table_name' => 'field',
            'field_name' => 'label',
            'referenced_table_name' => null,
            'referenced_field_name' => null,
            'label' => 'Label',
            'placeholder_label' => null,
            'description' => null,
            'data_type_id' => 16,
            'primary_key' => 'No',
            'signed' => 'No',
            'nullable' => 'Yes',
            'auto_increment' => 'No',
            'timestamp' => 'No',
            'max_length' => 128,
            'default_value' => null,
            'allowed_values' => null,
            'lookup_table' => null,
            'depends_on_table_name' => null,
            'depends_on_field_name' => null,
            'depends_on_value' => null,
            'date_created' => null
        ),
        array(
            'field_id' => 20,
            'bundle_name' => 'adapt',
            'table_name' => 'field',
            'field_name' => 'placeholder_label',
            'referenced_table_name' => null,
            'referenced_field_name' => null,
            'label' => 'Placeholder label',
            'placeholder_label' => null,
            'description' => null,
            'data_type_id' => 16,
            'primary_key' => 'No',
            'signed' => 'No',
            'nullable' => 'Yes',
            'auto_increment' => 'No',
            'timestamp' => 'No',
            'max_length' => 128,
            'default_value' => null,
            'allowed_values' => null,
            'lookup_table' => null,
            'depends_on_table_name' => null,
            'depends_on_field_name' => null,
            'depends_on_value' => null,
            'date_created' => null
        ),
        array(
            'field_id' => 21,
            'bundle_name' => 'adapt',
            'table_name' => 'field',
            'field_name' => 'description',
            'referenced_table_name' => null,
            'referenced_field_name' => null,
            'label' => 'Description',
            'placeholder_label' => null,
            'description' => null,
            'data_type_id' => 23,
            'primary_key' => 'No',
            'signed' => 'No',
            'nullable' => 'Yes',
            'auto_increment' => 'No',
            'timestamp' => 'No',
            'max_length' => null,
            'default_value' => null,
            'allowed_values' => null,
            'lookup_table' => null,
            'depends_on_table_name' => null,
            'depends_on_field_name' => null,
            'depends_on_value' => null,
            'date_created' => null
        ),
        array(
            'field_id' => 22,
            'bundle_name' => 'adapt',
            'table_name' => 'field',
            'field_name' => 'data_type_id',
            'referenced_table_name' => 'data_type',
            'referenced_field_name' => 'data_type_id',
            'label' => 'Data type',
            'placeholder_label' => null,
            'description' => null,
            'data_type_id' => 6,
            'primary_key' => 'No',
            'signed' => 'No',
            'nullable' => 'No',
            'auto_increment' => 'No',
            'timestamp' => 'No',
            'max_length' => null,
            'default_value' => null,
            'allowed_values' => null,
            'lookup_table' => 'data_type',
            'depends_on_table_name' => null,
            'depends_on_field_name' => null,
            'depends_on_value' => null,
            'date_created' => null
        ),
        array(
            'field_id' => 23,
            'bundle_name' => 'adapt',
            'table_name' => 'field',
            'field_name' => 'primary_key',
            'referenced_table_name' => null,
            'referenced_field_name' => null,
            'label' => 'Primary key',
            'placeholder_label' => null,
            'description' => null,
            'data_type_id' => 26,
            'primary_key' => 'No',
            'signed' => 'No',
            'nullable' => 'Yes',
            'auto_increment' => 'No',
            'timestamp' => 'No',
            'max_length' => null,
            'default_value' => 'Yes',
            'allowed_values' => "['Yes', 'No']",
            'lookup_table' => null,
            'depends_on_table_name' => null,
            'depends_on_field_name' => null,
            'depends_on_value' => null,
            'date_created' => null
        ),
        array(
            'field_id' => 24,
            'bundle_name' => 'adapt',
            'table_name' => 'field',
            'field_name' => 'signed',
            'referenced_table_name' => null,
            'referenced_field_name' => null,
            'label' => 'Signed',
            'placeholder_label' => null,
            'description' => null,
            'data_type_id' => 26,
            'primary_key' => 'No',
            'signed' => 'No',
            'nullable' => 'Yes',
            'auto_increment' => 'No',
            'timestamp' => 'No',
            'max_length' => null,
            'default_value' => 'Yes',
            'allowed_values' => "['Yes', 'No']",
            'lookup_table' => null,
            'depends_on_table_name' => null,
            'depends_on_field_name' => null,
            'depends_on_value' => null,
            'date_created' => null
        ),
        array(
            'field_id' => 25,
            'bundle_name' => 'adapt',
            'table_name' => 'field',
            'field_name' => 'nullable',
            'referenced_table_name' => null,
            'referenced_field_name' => null,
            'label' => 'Nullable',
            'placeholder_label' => null,
            'description' => null,
            'data_type_id' => 26,
            'primary_key' => 'No',
            'signed' => 'No',
            'nullable' => 'Yes',
            'auto_increment' => 'No',
            'timestamp' => 'No',
            'max_length' => null,
            'default_value' => 'Yes',
            'allowed_values' => "['Yes', 'No']",
            'lookup_table' => null,
            'depends_on_table_name' => null,
            'depends_on_field_name' => null,
            'depends_on_value' => null,
            'date_created' => null
        ),
        array(
            'field_id' => 26,
            'bundle_name' => 'adapt',
            'table_name' => 'field',
            'field_name' => 'auto_increment',
            'referenced_table_name' => null,
            'referenced_field_name' => null,
            'label' => 'Auto increment',
            'placeholder_label' => null,
            'description' => null,
            'data_type_id' => 26,
            'primary_key' => 'No',
            'signed' => 'No',
            'nullable' => 'Yes',
            'auto_increment' => 'No',
            'timestamp' => 'No',
            'max_length' => null,
            'default_value' => 'No',
            'allowed_values' => "['Yes', 'No']",
            'lookup_table' => null,
            'depends_on_table_name' => null,
            'depends_on_field_name' => null,
            'depends_on_value' => null,
            'date_created' => null
        ),
        array(
            'field_id' => 27,
            'bundle_name' => 'adapt',
            'table_name' => 'field',
            'field_name' => 'timestamp',
            'referenced_table_name' => null,
            'referenced_field_name' => null,
            'label' => 'Timestamp?',
            'placeholder_label' => null,
            'description' => null,
            'data_type_id' => 26,
            'primary_key' => 'No',
            'signed' => 'No',
            'nullable' => 'Yes',
            'auto_increment' => 'No',
            'timestamp' => 'No',
            'max_length' => null,
            'default_value' => 'No',
            'allowed_values' => "['Yes', 'No']",
            'lookup_table' => null,
            'depends_on_table_name' => null,
            'depends_on_field_name' => null,
            'depends_on_value' => null,
            'date_created' => null
        ),
        array(
            'field_id' => 28,
            'bundle_name' => 'adapt',
            'table_name' => 'field',
            'field_name' => 'max_length',
            'referenced_table_name' => null,
            'referenced_field_name' => null,
            'label' => 'Max length',
            'placeholder_label' => null,
            'description' => null,
            'data_type_id' => 4,
            'primary_key' => 'No',
            'signed' => 'No',
            'nullable' => 'Yes',
            'auto_increment' => 'No',
            'timestamp' => 'No',
            'max_length' => null,
            'default_value' => null,
            'allowed_values' => null,
            'lookup_table' => null,
            'depends_on_table_name' => null,
            'depends_on_field_name' => null,
            'depends_on_value' => null,
            'date_created' => null
        ),
        array(
            'field_id' => 29,
            'bundle_name' => 'adapt',
            'table_name' => 'field',
            'field_name' => 'default_value',
            'referenced_table_name' => null,
            'referenced_field_name' => null,
            'label' => 'Default value',
            'placeholder_label' => null,
            'description' => null,
            'data_type_id' => 16,
            'primary_key' => 'No',
            'signed' => 'No',
            'nullable' => 'Yes',
            'auto_increment' => 'No',
            'timestamp' => 'No',
            'max_length' => 128,
            'default_value' => null,
            'allowed_values' => null,
            'lookup_table' => null,
            'depends_on_table_name' => null,
            'depends_on_field_name' => null,
            'depends_on_value' => null,
            'date_created' => null
        ),
        array(
            'field_id' => 30,
            'bundle_name' => 'adapt',
            'table_name' => 'field',
            'field_name' => 'allowed_values',
            'referenced_table_name' => null,
            'referenced_field_name' => null,
            'label' => 'Allowed values',
            'placeholder_label' => null,
            'description' => null,
            'data_type_id' => 23,
            'primary_key' => 'No',
            'signed' => 'No',
            'nullable' => 'Yes',
            'auto_increment' => 'No',
            'timestamp' => 'No',
            'max_length' => null,
            'default_value' => null,
            'allowed_values' => null,
            'lookup_table' => null,
            'depends_on_table_name' => null,
            'depends_on_field_name' => null,
            'depends_on_value' => null,
            'date_created' => null
        ),
        array(
            'field_id' => 31,
            'bundle_name' => 'adapt',
            'table_name' => 'field',
            'field_name' => 'lookup_table',
            'referenced_table_name' => null,
            'referenced_field_name' => null,
            'label' => 'Lookup table',
            'placeholder_label' => null,
            'description' => null,
            'data_type_id' => 16,
            'primary_key' => 'No',
            'signed' => 'No',
            'nullable' => 'Yes',
            'auto_increment' => 'No',
            'timestamp' => 'No',
            'max_length' => 64,
            'default_value' => null,
            'allowed_values' => null,
            'lookup_table' => null,
            'depends_on_table_name' => null,
            'depends_on_field_name' => null,
            'depends_on_value' => null,
            'date_created' => null
        ),
        array(
            'field_id' => 32,
            'bundle_name' => 'adapt',
            'table_name' => 'field',
            'field_name' => 'depends_on_table_name',
            'referenced_table_name' => null,
            'referenced_field_name' => null,
            'label' => 'Depends on table name',
            'placeholder_label' => null,
            'description' => null,
            'data_type_id' => 16,
            'primary_key' => 'No',
            'signed' => 'No',
            'nullable' => 'Yes',
            'auto_increment' => 'No',
            'timestamp' => 'No',
            'max_length' => 64,
            'default_value' => null,
            'allowed_values' => null,
            'lookup_table' => null,
            'depends_on_table_name' => null,
            'depends_on_field_name' => null,
            'depends_on_value' => null,
            'date_created' => null
        ),
        array(
            'field_id' => 33,
            'bundle_name' => 'adapt',
            'table_name' => 'field',
            'field_name' => 'depends_on_field_name',
            'referenced_table_name' => null,
            'referenced_field_name' => null,
            'label' => 'Depends on field name',
            'placeholder_label' => null,
            'description' => null,
            'data_type_id' => 16,
            'primary_key' => 'No',
            'signed' => 'No',
            'nullable' => 'Yes',
            'auto_increment' => 'No',
            'timestamp' => 'No',
            'max_length' => 64,
            'default_value' => null,
            'allowed_values' => null,
            'lookup_table' => null,
            'depends_on_table_name' => null,
            'depends_on_field_name' => null,
            'depends_on_value' => null,
            'date_created' => null
        ),
        array(
            'field_id' => 34,
            'bundle_name' => 'adapt',
            'table_name' => 'field',
            'field_name' => 'depends_on_value',
            'referenced_table_name' => null,
            'referenced_field_name' => null,
            'label' => 'Depends on value',
            'placeholder_label' => null,
            'description' => null,
            'data_type_id' => 16,
            'primary_key' => 'No',
            'signed' => 'No',
            'nullable' => 'Yes',
            'auto_increment' => 'No',
            'timestamp' => 'No',
            'max_length' => 64,
            'default_value' => null,
            'allowed_values' => null,
            'lookup_table' => null,
            'depends_on_table_name' => null,
            'depends_on_field_name' => null,
            'depends_on_value' => null,
            'date_created' => null
        ),
        array(
            'field_id' => 35,
            'bundle_name' => 'adapt',
            'table_name' => 'field',
            'field_name' => 'date_created',
            'referenced_table_name' => null,
            'referenced_field_name' => null,
            'label' => 'Date created',
            'placeholder_label' => null,
            'description' => null,
            'data_type_id' => 31,
            'primary_key' => 'No',
            'signed' => 'No',
            'nullable' => 'Yes',
            'auto_increment' => 'No',
            'timestamp' => 'No',
            'max_length' => null,
            'default_value' => null,
            'allowed_values' => null,
            'lookup_table' => null,
            'depends_on_table_name' => null,
            'depends_on_field_name' => null,
            'depends_on_value' => null,
            'date_created' => null
        ),
        array(
            'field_id' => 36,
            'bundle_name' => 'adapt',
            'table_name' => 'field',
            'field_name' => 'date_modified',
            'referenced_table_name' => null,
            'referenced_field_name' => null,
            'label' => 'Last modified',
            'placeholder_label' => null,
            'description' => null,
            'data_type_id' => 32,
            'primary_key' => 'No',
            'signed' => 'No',
            'nullable' => 'Yes',
            'auto_increment' => 'No',
            'timestamp' => 'Yes',
            'max_length' => null,
            'default_value' => null,
            'allowed_values' => null,
            'lookup_table' => null,
            'depends_on_table_name' => null,
            'depends_on_field_name' => null,
            'depends_on_value' => null,
            'date_created' => null
        ),
        array(
            'field_id' => 37,
            'bundle_name' => 'adapt',
            'table_name' => 'field',
            'field_name' => 'date_deleted',
            'referenced_table_name' => null,
            'referenced_field_name' => null,
            'label' => 'Date deleted',
            'placeholder_label' => null,
            'description' => null,
            'data_type_id' => 31,
            'primary_key' => 'No',
            'signed' => 'No',
            'nullable' => 'Yes',
            'auto_increment' => 'No',
            'timestamp' => 'No',
            'max_length' => null,
            'default_value' => null,
            'allowed_values' => null,
            'lookup_table' => null,
            'depends_on_table_name' => null,
            'depends_on_field_name' => null,
            'depends_on_value' => null,
            'date_created' => null
        )
    );
    
    $data_types = $adapt->data_source->data_types;
    $fields = $adapt->data_source->schema;
    
    
    foreach($data_types as &$data_type){
        $keys = array_keys($data_type);
        foreach($keys as $key){
            if ($key == 'date_created'){
                $data_type['date_created'] = new sql('now()');
            }elseif(is_null($data_type[$key])){
                $data_type[$key] = new sql('null');
            }
        }
    }
    
    foreach($fields as &$field){
        $keys = array_keys($field);
        foreach($keys as $key){
            if ($key == 'date_created'){
                $field['date_created'] = new sql('now()');
            }elseif(is_null($field[$key])){
                $field[$key] = new sql('null');
            }
        }
    }
    
    
    
    $sql->insert_into('data_type', array_keys($data_types[0]));
    foreach($data_types as $type) $sql->values(array_values($type));
    $sql->execute();
    
    $sql->insert_into('field', array_keys($fields[0]));
    for($i = 0; $i < count($fields); $i++){
        //print "{$fields[$i]['field_id']}<br>";
        $sql->values(array_values($fields[$i]));
    }
    /* The below statement should work exactly the same
     * as the one above, but it fails and duplicates the
     * last but one record and misses the last record.
     * PHP Bug?
    */
    //foreach($fields as $field){
        //print "<pre>" . print_r($field, true) . "</pre>";
    //    $sql->values(array_values($field));
    //}
    //print $sql;
    //exit(1);
    $sql->execute();
    
    //print "Boom";
    //exit(1);
    

}

?>