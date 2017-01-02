<?php

class Database {
    private static $connection;

    protected static function connection() {
        if(isset($connection)) {
            $conn = self::$connection;
        } else {
            $conn = new PDO(CONNECTION, USER_NAME , PASSWORD);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$connection = $conn;
        }

        return $conn;
    }

    public static function queryResult($sql, $get_last_id = false) {
        try{
            $conn = self::connection();
            $conn ->setAttribute(PDO::ATTR_EMULATE_PREPARES,TRUE);
            $stmt = $conn->prepare($sql);
            $stmt->execute();

            return $get_last_id ? $conn->lastInsertId() : $stmt->rowCount();
        }
        catch(PDOException $e)
        {
            echo "<p style='color:red'>$sql" . $e->getMessage() . "</p>";
            return false;
        }
    }

    public static function insert($table, $records = array()) {

        foreach($records as $value) {
            $values[] = self::connection()->quote($value);
        }

        $values = implode(',', $values);

        $sql = "INSERT INTO `$table`
                VALUES (default, $values)";


        return self::queryResult($sql, true);
    }

    public static function select($table, $fields = "*", $conditions = "", $join = array(), $limit = array(), $orderby = '') {
        $where = "1";

        if(is_array($fields)) {
            $fields = implode(',', $fields);
        }

        if(!empty($conditions)) {
            if(is_array($conditions)) {
                foreach($conditions as $key => $value) {
                    $where .= " AND $key = '$value'";
                }
            } else {
                $where .= " AND $conditions";
            }
        }

        $sql = "SELECT $fields FROM `$table`";

        if(!empty($join)) {
            for($i = 0; $i < count($join); $i++) {
                $key1 =  array_keys($join)[$i];
                $val1 = array_values($join)[$i++];

                $key2 =  array_keys($join)[$i];
                $val2 = array_values($join)[$i];

                $sql .= " JOIN $key1 ON $key1.$val1 = $key2.$val2";
            }
        }

        $sql .= " WHERE $where";

        if($orderby) {
            $sql .= " ORDER BY $orderby";
        }

        if(!empty($limit)) {
            $sql .= " LIMIT {$limit['limit']} OFFSET {$limit['offset']}";
        }

        $stmt = self::connection()->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function update($table, $fields = array(), $wheres = array(), $sanitize = true) {
        $records = is_array($fields)? self::buildFieldList($fields, ',', $sanitize) : $fields;
        $conditions = is_array($wheres)? self::buildFieldList($wheres, " AND ") : $wheres;

        return self::queryResult("UPDATE {$table} SET {$records} WHERE 1 AND {$conditions}");
    }

    public static function delete($table, $fields = array()){
        $where = "";

        foreach($fields as $field){
            foreach($field as $key => $value) {
                $where .= "$key = $value AND ";
            }
        }

        $where = trim($where, ' AND ');
        $sql = "DELETE FROM $table WHERE $where";

        self::queryResult($sql);
    }

    public static function rawQuery($sql, $fetch) {
        $stmt = self::connection()->prepare($sql);
        $stmt->execute();

        if($fetch) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return self::queryResult($sql);
        }
    }

    private static function buildFieldList($fields = array(), $glue = ",", $sanitize = true) {
        foreach($fields as $key => $value){
            if(is_array($value)) {
                self::buildFieldList($value, $glue);
            } else {
                $value = $sanitize? self::connection()->quote($value) : $value;
                $records[] = "$key = $value";
            }
        }

        return implode($glue, $records);
    }
}