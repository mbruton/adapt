<?php

namespace frameworks\adapt{
    
    /* Prevent direct access */
    defined('ADAPT_STARTED') or die;
    
    class data_source_mysql extends data_source_sql implements interfaces\data_source_sql{
        
        /*
         * SQL Execution
         */
        public function query($sql, $write = false){
            $host = $this->get_host($write);
            //print new html_pre(print_r($host, true));
            if (!is_null($host) && isset($host['handle'])){
                if (mysqli_real_query($host['handle'], $sql)){
                    $this->trigger(self::EVENT_QUERY, array('sql' => $sql, 'host' => $host));
                    if ($write){
                        return true;
                    }elseif ($result = mysqli_store_result($host['handle'])){
                        return $result;
                    }
                    
                    $this->error("Unable to retrieve result set");
                    return false;
                }else{
                    $this->error("Invalid SQL statement: {$sql}");
                    return false;
                }
            }
            
            $this->error("No hosts available to query");
            return false;
        }
        
        public function fetch($statement_handle, $fetch_type = self::FETCH_ASSOC){
            if (is_object($statement_handle)){
                switch($fetch_type){
                case self::FETCH_ASSOC:
                    return $statement_handle->fetch_assoc();
                case self::FETCH_ARRAY:
                    return $statement_handle->fetch_array();
                case self::FETCH_OBJECT:
                    return $statement_handle->fetch_object();
                case self::FETCH_ALL_ASSOC:
                    $results = array();
                    while($row = $statement_handle->fetch_assoc()){
                        $results[] = $row;
                    }
                    return $results;
                case self::FETCH_ALL_ARRAY:
                    $results = array();
                    while($row = $statement_handle->fetch_array()){
                        $results[] = $row;
                    }
                    return $results;
                case self::FETCH_ALL_OBJECT:
                    $results = array();
                    while($row = $statement_handle->fetch_object()){
                        $results[] = $row;
                    }
                    return $results;
                }
            }
            
            $this->error("Invalid statement handle");
            return false;
        }
        
        public function last_insert_id(){
            $host = $this->get_host(true);
            if (isset($host) && isset($host['handle'])){
                return mysqli_insert_id($host['handle']);
            }
        }
        
        /*
         * Connection management
         */
        public function connect($host){
            //$mysql = new mysqli($host['host'], $host['username'], $host['password'], $host['schema']);
            $mysql = mysqli_connect($host['host'], $host['username'], $host['password'], $host['schema']);
            if (mysqli_connect_error($mysql)){
                //TODO: Append mysql error
                $this->error("Unable to connect to {$host['host']}");
                return false;
            }
            
            $this->trigger(self::EVENT_HOST_CONNECT, array('host' => $host));
            return $mysql;
        }
        
        public function disconnect($host){
            if (isset($host['handle'])){
                mysqli_close($host['handle']);
                $this->trigger(self::EVENT_HOST_DISCONNECT, array('host' => $host));
            }
        }
        
        /*
         * Escaping
         */
        public function escape($string){
            $host = $this->get_host();
            if (isset($host['handle'])){
                return mysqli_real_escape_string($host['handle'], $string);
            }else{
                return parent::escape($string);
            }
        }
        
        /*
         * SQL Rendering
         */
        public function render_sql(\frameworks\adapt\sql $sql){
            $statement = "";
            
            if ($sql instanceof \frameworks\adapt\sql_function){
                
                $statement .= $sql;
                return $statement;
                
            }
            
            if ($sql instanceof \frameworks\adapt\sql_condition){
                
                if ($sql->value_1 instanceof sql){
                    $statement .= $this->render_sql($sql->value_1);
                }elseif(is_string($sql->value_1)){
                    $value = $this->escape($sql->value_1);
                    $statement .= "\"" . $value . "\"";
                }
                
                $statement .= " " . $sql->condition . " ";
                
                if ($sql->value_2 instanceof sql){
                    $statement .= $this->render_sql($sql->value_2);
                }else{
                    $value = $this->escape($sql->value_2);
                    $statement .= "\"" . $value . "\"";
                }
                
                return $statement;
            }
            
            if ($sql instanceof \frameworks\adapt\sql_or){
                $statement .= "(";
                for($i = 0; $i < count($sql->conditions); $i++){
                    if ($i > 0) $statement .= " OR ";
                    $condition = $sql->conditions[$i];
                    if ($condition instanceof \frameworks\adapt\sql){
                        $statement .= $this->render_sql($condition);
                    }elseif(is_string($condition)){
                        $statement .= $condition;
                    }
                }
                
                $statement .= ")";
                return $statement;
            }
            
            if ($sql instanceof \frameworks\adapt\sql_and){
                $statement .= "(";
                for($i = 0; $i < count($sql->conditions); $i++){
                    if ($i > 0) $statement .= " AND ";
                    $condition = $sql->conditions[$i];
                    if ($condition instanceof \frameworks\adapt\sql){
                        $statement .= $this->render_sql($condition);
                    }elseif(is_string($condition)){
                        $statement .= $condition;
                    }
                }
                
                $statement .= ")";
                return $statement;
            }
            
            if ($sql instanceof \frameworks\adapt\sql_if){
                $statement .= "IF (";
                if ($sql->condition instanceof \frameworks\adapt\sql){
                    $statement .= $this->render_sql($sql->condition);
                }elseif(is_string($sql->condition)){
                    $statement .= $sql->condition;
                }
                
                $statement .= ", ";
                
                if ($sql->if_true instanceof \frameworks\adapt\sql){
                    $statement .= $this->render_sql($sql->if_true);
                }elseif(is_string($sql->if_true)){
                    $statement .= "\"" . $sql->if_true . "\"";
                }
                
                $statement .= ", ";
                
                if ($sql->if_false instanceof \frameworks\adapt\sql){
                    $statement .= $this->render_sql($sql->if_false);
                }elseif(is_string($sql->if_false)){
                    $statement .= "\"" . $sql->if_false . "\"";
                }
                
                $statement .= ")";
                
                return $statement;
            }
            
            if ($sql instanceof \frameworks\adapt\sql){
                if (!is_null($sql->statement)){
                    if ($statement == ""){
                        $statement = $sql->statement;
                    }else{
                        $statement .= "(" . $sql->statement . ")";
                    }
                    
                    return $statement;
                }
                
                /* Insert statement */
                if(!is_null($sql->insert_into_table_name)){
                    /* Insert statement */
                    if (in_array($sql->insert_into_table_name, array_merge(array('data_type'), $this->get_dataset_list()))){
                        $statement = "INSERT INTO `{$sql->insert_into_table_name}`\n";
                        $insert_fields = $sql->insert_into_fields;
                        if (is_array($insert_fields)){
                            $statement .= "(";
                            $first = true;
                            foreach($insert_fields as $field){
                                if (!$first){
                                    $statement .= ", ";
                                }
                                
                                $field = $this->escape($field);
                                $statement .= "`{$field}`";
                                $first = false;
                            }
                            
                            $statement .= ")\n";
                        }
                        
                        /* Are we inserting values or a select? */
                        if (is_array($sql->insert_into_values) && count($sql->insert_into_values)){
                            /* Insert the values */
                            $keys = array();
                            
                            $statement .= "VALUES\n";
                            
                            if (is_array($insert_fields)){
                                $keys = $insert_fields;
                            }else{
                                //Get the fields for this table
                                $keys = array_keys($schema);
                            }
                            
                            $rows = $sql->insert_into_values;
                            $first_row = true;
                            
                            for($j = 0; $j < count($rows); $j++){
                                $row = $rows[$j];
                                
                                if (count($row) == count($keys)){
                                    if ($first_row){
                                        $statement .= "(";
                                        $first_row = false;
                                    }else{
                                        $statement .= ",\n(";
                                    }
                                    
                                    for($i = 0; $i < count($row); $i++){
                                        $value = $row[$i];
                                        $key = $keys[$i];
                                        
                                        if ($i > 0) $statement .= ", ";
                                        
                                        if ($value instanceof \frameworks\adapt\sql){
                                            $statement .= $this->render_sql($value);
                                        }elseif(is_string($value) || is_numeric($value)){
                                            /* Unformat the value */
                                            // This should be done at model level as we can not format from
                                            // the select statement
                                            //$value = $this->unformat($sql->insert_into_table_name, $keys[$i], $value);
                                            
                                            /* Escape the value */
                                            $value = $this->escape($value);
                                            
                                            /* Validate the value */
                                            if (!$this->validate($sql->insert_into_table_name, $keys[$i], $value)){
                                                //TODO: Faild the insert
                                                $this->error("The data for {$keys[$i]} on row " . ($j + 1) . " is not valid");
                                                return null;
                                            }
                                            
                                            
                                            $statement .= "\"{$value}\"";
                                        }else{
                                            $statement .= "null";
                                        }
                                    }
                                    
                                    $statement .= ")";
                                }else{
                                    //Fail the entire insert
                                    $this->error("Unable in insert data into '{$sql->insert_into_table_name}' row " . ($j + 1) . " column count is incorrect");
                                    return null;
                                }
                            }
                            
                            $statement .= ";\n";
                            return $statement;
                        }
                    }else{
                        //TODO: Error: Invalid table name
                        $this->errors("Unable to insert data into non-existant table '{$sql->insert_into_table_name}'");
                        return null;
                    }
                }
                
                /* Select Statement */
                if (is_array($sql->select_fields) && count($sql->select_fields)){
                    $select_fields = $sql->select_fields;
                    $statement = "SELECT\n";
                    if ($sql->is_distinct) $statement = "SELECT DISTINCT\n";
                    $first = true;
                    foreach($select_fields as $pair){
                        $alias = null;
                        $field = $pair['value'];
                        if (isset($pair['alias'])) $alias = $pair['alias'];
                        
                        if (!$first) $statement .= ",\n";
                        if ($field instanceof \frameworks\adapt\sql){
                            $statement .= $this->render_sql($field);
                        }else{
                            //$field = $this->escape($field);
                            //$statement .= "\"{$field}\"";
                            $statement .= $field;
                        }
                        
                        if (!is_null($alias) && (!is_string($field) || $field != $alias)){
                            $statement .= " AS " . q($alias);
                        }
                        
                        $first = false;
                    }
                    
                    $statement .= "\n";
                    
                    /* From */
                    $from = $sql->from_fields;
                    if (is_array($from) && count($from)){
                        if (is_assoc($from)){
                            $keys = array_keys($from);
                            $statement .= "FROM ";
                            $value = $from[$keys[0]];
                            if ($value instanceof \frameworks\adapt\sql){
                                $statement .= "(" . $this->render_sql($value) . ")";
                            }else{
                                $statement .= $value;
                            }
                            
                            $statement .= " AS {$keys[0]}\n";
                        }else{
                            $statement .= "FROM {$from[0]}\n";
                        }
                    }
                    
                    /* Joins */
                    $joins = $sql->join_conditions;
                    if (is_array($joins) && count($joins)){
                        foreach($joins as $join){
                            switch($join['type']){
                            case \frameworks\adapt\sql::LEFT_JOIN:
                                $statement .= "LEFT JOIN ";
                                break;
                            case \frameworks\adapt\sql::RIGHT_JOIN:
                                $statement .= "RIGHT JOIN ";
                                break;
                            case \frameworks\adapt\sql::INNER_JOIN:
                                $statement .= "INNER JOIN ";
                                break;
                            case \frameworks\adapt\sql::OUTER_JOIN:
                                $statement .= "OUTER JOIN ";
                                break;
                            case \frameworks\adapt\sql::JOIN:
                            default:
                                $statement .= "JOIN ";
                            }
                            
                            if ($join['table'] instanceof \frameworks\adapt\sql){
                                $statement .= "(" . $this->render_sql($join['table']) . ")";
                            }else{
                                $statement .= $join['table'];
                            }
                            
                            if (!is_null($join['alias']) && $join['alias'] != ""){
                                $statement .= " AS {$join['alias']}\n";
                            }
                            
                            if ($join['condition'] instanceof \frameworks\adapt\sql){
                                $statement .= "ON (" . $this->render_sql($join['condition']) . ")\n";
                            }elseif(is_string($join['condition']) && $join['condition'] != ""){
                                $statement .= "USING({$join['condition']})\n";
                            }
                        }
                    }
                    
                    /* Where */
                    $where = $sql->where_conditions;
                    if (isset($where) && count($where)){
                        $statement .= "WHERE ";
                        foreach($where as $item){
                            if ($item instanceof \frameworks\adapt\sql){
                                $statement .= $this->render_sql($item);
                            }else{
                                $statement .= $item;
                            }
                        }
                    }
                    $statement .= "\n";
                    
                    /* Grouping */
                    $grouping = $sql->grouping;
                    if (is_array($grouping) && count($grouping)){
                        $statement .= "GROUP BY ";
                        $first = true;
                        foreach($grouping as $group){
                            if (!$first) $statement .= ",\n";
                            if ($group['field'] instanceof \frameworks\adapt\sql){
                                $statement .= $this->render_sql($group['field']);
                            }else{
                                $statement .= $group['field'];
                            }
                            
                            if ($group['ascending']){
                                $statement .= " ASC";
                            }else{
                                $statement .= " DESC";
                            }
                            
                            if ($group['with_rollup']){
                                $statement .= " WITH ROLLUP";
                            }
                            
                            $first = false;
                        }
                        
                        $statement .= "\n";
                    }
                    
                    /* Having */
                    $having = $sql->having_conditions;
                    if (isset($having) && count($having)){
                        $statement .= "HAVING ";
                        foreach($having as $item){
                            if ($item instanceof \frameworks\adapt\sql){
                                $statement .= $this->render_sql($item);
                            }else{
                                $statement .= $item;
                            }
                        }
                    }
                    $statement .= "\n";
                    
                    /* Ordering */
                    $ordering = $sql->ordering;
                    if (is_array($ordering) && count($ordering)){
                        $statement .= "ORDER BY ";
                        $first = true;
                        foreach($ordering as $order){
                            if (!$first) $statement .= ",\n";
                            if ($order['field'] instanceof \frameworks\adapt\sql){
                                $statement .= $this->render_sql($order['field']);
                            }else{
                                $statement .= $order['field'];
                            }
                            
                            if ($order['ascending']){
                                $statement .= " ASC";
                            }else{
                                $statement .= " DESC";
                            }
                            
                            $first = false;
                        }
                        
                        $statement .= "\n";
                    }
                    
                    /* Limit */
                    $limit = $sql->limit_count;
                    $offset = $sql->limit_offset;
                    if (!is_null($limit)){
                        $statement .= "LIMIT ";
                        if (isset($offset)){
                            $statement .= $offset . ", " . $limit . "\n";
                        }else{
                            $statement .= $limit . "\n";
                        }
                    }
                    $statement .= "\n";
                    return $statement;
                }
                
                /* Update statement */
                if (count($sql->update_tables) > 0){
                    /* Update */
                    $statement = "UPDATE ";
                    $tables = $sql->update_tables;
                    $first = true;
                    foreach($tables as $key => $value){
                        if (!$first) $statement .= ", ";
                        
                        if (is_int($key)){
                            $value = $this->escape($value);
                            $statement .= "`{$value}`";
                        }else{
                            $value = $this->escape($value);
                            $key = $this->escape($key);
                            if ($key != $value){
                                $statement .= "`{$key}` AS '{$value}'";
                            }else{
                                $statement .= "`{$key}`";
                            }
                        }
                        
                        $first = false;
                    }
                    
                    $statement .= "\n";
                    
                    /* Set */
                    $set = $sql->set;
                    $statement .= "SET ";
                    $first = true;
                    
                    foreach($set as $field => $value){
                        //TODO: Validate  values
                        //TODO: Take into account alias
                        if (!$first) $statement .= ",\n";
                        $statement .= "`{$field}` = ";
                        
                        if ($value instanceof sql){
                            $statement .= $this->render_sql($value);
                        }else{
                            $value = $this->escape($value);
                            $statement .= "\"{$value}\"";
                        }
                    }
                    $statement .= "\n";
                    
                    /* Where */
                    $where = $sql->where_conditions;
                    if (isset($where) && count($where)){
                        $statement .= "WHERE ";
                        foreach($where as $item){
                            if ($item instanceof \frameworks\adapt\sql){
                                $statement .= $this->render_sql($item);
                            }else{
                                $statement .= $item;
                            }
                        }
                    }
                    $statement .= "\n";
                    
                    return $statement;
                }
                
                /* Delete statement */
                //TODO:
                
                /* Create database */
                if(!is_null($sql->create_database_name)){
                    /*
                     * Create database
                     */
                    $statement = "CREATE DATABASE " . $this->escape($sql->create_database_name) . ";\n";
                    return $statement;
                }
                
                /* Create table */
                if(!is_null($sql->create_table_name)){
                    
                    /*
                     * Create table
                     */
                    $statement = "CREATE TABLE {$sql->create_table_name} (\n";
                    $fields = $sql->create_table_fields;
                    $primary_keys = $sql->primary_keys;
                    $first = true;
                    
                    /* Add the fields */
                    foreach($fields as $field){
                        if (!$first) $statement .= ",\n";
                        $statement .= $field['field_name'] . " " . $this->convert_data_type($field['data_type'], $field['signed']);
                        
                        /* Should we auto increment? */
                        foreach($primary_keys as $key){
                            if ($key['field_name'] == $field['field_name'] && $key['auto_increment'] == true){
                                $statement .= " AUTO_INCREMENT";
                            }
                        }
                        
                        if ($field['nullable'] === false) $statement .= " NOT NULL";
                        if (!is_null($field['default_value'])) $statement .= " DEFAULT \"" . $this->escape($field['default_value']) . "\"";
                        $first = false;
                    }
                    
                    //$statement .= "\n";
                    
                    /* Add any primary keys */
                    $field_names = array();
                    foreach($primary_keys as $key) $field_names[] = $key['field_name'];
                    
                    if (count($field_names)){
                        $statement .= ",\nPRIMARY KEY (" . implode(", ", $field_names) . ")";
                    }
                    
                    /* Add foreign keys */
                    $foreign_keys = $sql->foreign_keys;
                    
                    foreach($foreign_keys as $key){
                        $statement .= ",\nFOREIGN KEY ({$key['field_name']}) REFERENCES {$key['reference_table_name']} ({$key['reference_field_name']}) ON DELETE {$key['on_delete']}";
                    }
                    
                    /* Add indexes */
                    $indexes = $sql->indexes;
                    
                    foreach($indexes as $index){
                        $statement .= ",\nINDEX ({$index['field_name']}";
                        if (!is_null($index['size']) && is_numeric($index['size'])){
                            $statement .= "({$index['size']})";
                        }
                        $statement .= ")";
                    }
                    
                    $statement .= "\n)";
                    $engine = $this->setting('mysql.default_engine');
                    $charset = $this->setting('mysql.default_character_set');
                    $collation = $this->setting('mysql.default_collation');
                    
                    if (isset($engine)) $statement .= " ENGINE = {$engine}";
                    if (isset($charset)) $statement .= " DEFAULT CHARSET = {$charset}";
                    //if (isset($charset)) $statement .= " CHARACTER SET={$charset}";
                    if (isset($collation)) $statement .= " COLLATE={$collation}";
                    
                    $statement .= ";\n";
                    return $statement;
                }
                
                /*
                 * Alter table
                 */
                if (!is_null($sql->alter_table_name)){
                    $statement = "ALTER TABLE {$sql->alter_table_name}\n";
                    $fields = $sql->alter_table_fields;
                    $first = true;
                    
                    foreach($fields as $field){
                        if (!$first) $statement .= ",\n";
                        
                        switch($field['_type']){
                        case "add":
                            $statement .= "ADD " . $field['field_name'] . " " . $this->convert_data_type($field['data_type'], $field['signed']);
                            if ($field['nullable'] === false) $statement .= " NOT NULL";
                            if (!is_null($field['default_value'])) $statement .= " DEFAULT \"" . $this->escape($field['default_value']) . "\"";
                            if (!is_null($field['_after'])) $statement .= " AFTER {$field['after']}";
                            break;
                        case "change":
                            $statement .= "CHANGE {$field['old_field_name']}" . $field['field_name'] . " " . $this->convert_data_type($field['data_type'], $field['signed']);
                            if ($field['nullable'] === false) $statement .= " NOT NULL";
                            if (!is_null($field['default_value'])) $statement .= " DEFAULT \"" . $this->escape($field['default_value']) . "\"";
                            if (!is_null($field['_after'])) $statement .= " AFTER {$field['after']}";
                            break;
                        case "drop":
                            $statement .= "DROP " . $field['field_name'];
                            break;
                        }
                        
                        $first = false;
                    }
                    
                    $statement .= ";\n";
                    return $statement;
                }
            }
            
            
            return $statement;
        }
        
        /*
         * Data validation
         */
        //public function validate($table_name, $field_name, $value){
        //    
        //}
        
        /*
         * Data types
         */
        public function convert_data_type($type, $signed = true, $zero_fill = false){
            $params = array();
            
            if (mb_stripos($type, "(") !== false){
                $type = preg_replace("/\)/", "", $type);
                list($type, $raw_params) = explode("(", $type);
                $params = explode(",", $raw_params);
                foreach($params as &$p) $p = mb_trim($p);
            }
            
            $type = mb_trim(mb_strtolower($type));
            
            switch($type){
            case "tinyint":
            case "smallint":
            case "mediumint":
            case "int":
            case "integer":
            case "bigint":
                $type = mb_strtoupper($type);
                if (!$signed) $type .= " UNSIGNED";
                if ($zero_fill) $type .= " ZEROFILL";
                return $type;
            
            case "serial":
                return "SERIAL";
            
            case "bit":
                $type = mb_strtoupper($type);
                if (count($params) == 1 && is_numeric($params[0]) && $params[0] >= 1 && $params[0] <= 64){
                    $type .= "({$params[0]})";
                }else{
                    //Throw error
                }
                break;
            
            case "boolean":
            case "bool":
                return "BOOL";
            
            case "decimal":
            case "double":
                $type = mb_strtoupper($type);
                if (count($params) == 1 && is_numeric($params[0])){
                    $type .= "({$params[0]})";
                }elseif (count($params) == 2 && is_numeric($params[0]) && is_numeric($params[1])){
                    $type .= "({$params[0]},{$params[1]})";
                }else{
                    //Throw error
                    return;
                }
                if (!$signed) $type .= " UNSIGNED";
                if ($zero_fill) $type .= " ZEROFILL";
                return $type;
                
            case "float":
                $type = mb_strtoupper($type);
                if (count($params) == 1 && is_numeric($params[0])){
                    $type .= "({$params[0]})";
                }else{
                    //Throw error
                    return;
                }
                if (!$signed) $type .= " UNSIGNED";
                if ($zero_fill) $type .= " ZEROFILL";
                return $type;
                
            case "char":
            case "binary":
                $type = mb_strtoupper($type);
                if (count($params) == 1 && is_numeric($params[0]) && $params[0] >= 0 && $params[0] <= 255){
                    $type .= "({$params[0]})";
                }else{
                    //Throw error
                    return;
                }
                return $type;
            
            case "varchar":
            case "varbinary":
                $type = mb_strtoupper($type);
                if (count($params) == 1 && is_numeric($params[0]) && $params[0] >= 0 && $params[0] <= 65535){
                    $type .= "({$params[0]})";
                }else{
                    //Throw error
                    return;
                }
                return $type;
            
            case "tinyblob":
                $type = mb_strtoupper($type);
                return $type;
            
            case "blob":
                $type = mb_strtoupper($type);
                if (count($params) == 1 && is_numeric($params[0]) && $params[0] >= 0 && $params[0] <= 65535){
                    $type .= "({$params[0]})";
                }
                return $type;
            
            case "mediumblob":
            case "longblob":
            case "tinytext":
            case "text":
            case "mediumtext":
            case "longtext":
                $type = mb_strtoupper($type);
                return $type;
            
            case "enum":
            case "set":
                $type = mb_strtoupper($type);
                
                $type .= "(";
                if (count($params) > 0){
                    for($i = 0; $i < count($params); $i++){
                        if ($i > 0) $type .= ", ";
                        $type .= "\"" . $this->escape(mb_trim($params[$i], '\s\'"')) . "\"";
                    }
                }
                $type .= ")";
                
                return $type;
                
            case "year":
            case "date":
            case "time":
            case "datetime":
            case "timestamp":
                $type = mb_strtoupper($type);
                return $type;
            default:
                /*
                 * We are going to seek the
                 * base type from the schema's data_types
                 */
                foreach($this->_data_types as $data_type){
                    if ($data_type['name'] == $type && isset($data_type['based_on_data_type'])){
                        return $this->convert_data_type($data_type['based_on_data_type']);
                    }
                }
            }
        }
        
        public function sync_schema(){
            //base::install();
            print_r($this->errors());
            $host = $this->get_host();
            $sql = $this->sql;
            $sql->select(new sql('*'))
                ->from('information_schema.columns')
                ->where(new sql_condition(new sql('table_schema'), '=', $host['schema']));
            //print $sql;
            $results = $sql->execute()->results();
            //print_r($results);
            //exit(1);
            foreach($results as $result){
                $struct = $this->get_field_structure($result['TABLE_NAME'], $result['COLUMN_NAME']);
                
                if (is_null($struct)){
                    print "Foo\n";
                    /* We need to add this field */
                    $model = new model_adapt_field();
                    $model->table_name = $result['TABLE_NAME'];
                    $model->field_name = $result['COLUMN_NAME'];
                    $model->primary_key = $result["COLUMN_KEY"] == "PRI" ? "Yes" : "No";
                    if ($result['COLUMN_KEY'] == "PRI" && $result['EXTRA'] == "auto_increment" && $result['COLUMN_TYPE'] == 'bigint(20)'){
                        $model->data_type = "serial";
                    }else{
                        if (preg_match("/^(bigint|int)/", $result['COLUMN_TYPE'])){
                            $model->data_type = preg_replace("/\([0-9]+\)/", "", $result['COLUMN_TYPE']);
                        }else{
                            $model->data_type = $result['COLUMN_TYPE'];
                        }
                    }
                    $model->signed = isset($result['NUMERIC_PRECISION']) ? "Yes" :  "No";
                    if ($result['IS_NULLABLE'] == "NO"){
                        $model->nullable = "No";
                    }else{
                        $model->nullable = "Yes";
                    }
                    $model->default_value = $result['COLUMN_DEFAULT'];
                    if ($result['EXTRA'] == "auto_increment"){
                        $model->auto_increment = 'Yes';
                    }else{
                        $model->auto_increment = 'No';
                    }
                    if ($result['data_type'] == 'timestamp'){
                        $model->timestamp = 'Yes';
                    }else{
                        $model->timestamp = 'No';
                    }
                    
                    if (preg_match("/\([0-9]+\)$/", $result['COLUMN_TYPE'])){
                        $model->min_size = '0';
                        $model->max_size = preg_replace("/[^0-9]/", "", $result['COLUMN_TYPE']);
                    }
                    
                    //$model->date_created = new sql('now()');
                    //$model->date_modified = new sql('now()');
                    $model->save();
                    
                }
                
                //TODO: References
            }
        }
        
    }

}

?>