<?php

namespace Database;

interface ITable {
    public static function TableName();
}

abstract class Base implements \Serializable, ITable, \JsonSerializable {
    protected static $_CACHE = array();
    protected static $_LOG = false;

    private static function _fixWhere($sql, array $where) {
        if ($where) {
            $sql .= " WHERE \n";
            foreach ($where as $col => $val) { $sql .= static::_fixWhereBit($col, $val); }
            $sql = preg_replace('/ AND $/', '', $sql);
        }
        return $sql;
    }
    private static function _fixWhereBit($col, $val) {
        if (is_array($val)) {
            if(is_array($val[1])) { return "\t`$col` {$val[0]} (".implode(', ', $val[1]).")\n\t AND "; }
            return "\t`$col` {$val[0]} :$col\n\t AND ";
            //static::$_LOG = true;
        } else {
            return "\t`$col` = :$col\n\t AND ";
        }
    }

    private static function _fixOrderBy($sql, array $orderBy) {
        if ($orderBy) {
            $sql .= " ORDER BY ";
            foreach ($orderBy as $oName => $oVal) { $sql .= "$oName $oVal, "; }
            $sql = preg_replace('/, $/', '', $sql);
        }
        return $sql;
    }

    private static function _fixLimit($sql, $limit) {
        if ($limit) { $sql .= " LIMIT " . (is_array($limit) ? "{$limit[0]}, {$limit[1]}" : "$limit"); }
        return $sql;
    }
    public static function _LoadBySQL($sql, $params = array()) {
        $conn = \DBConnectionFactory::getFactory()->getConnection();
        //print_r($sql);
        $q = $conn->prepare($sql);
        foreach ($params as $col => &$val) {
            if (is_array($val)) { $val = &$val[1]; }
            if (is_array($val)) { continue; }
            $q->bindParam(':' . $col, $val, (is_int($val) ? \PDO::PARAM_INT : (is_bool($val) ? \PDO::PARAM_BOOL : (is_null($val) ? \PDO::PARAM_NULL :\PDO::PARAM_STR))));
        }
        $ret = array();
        if ($q->execute()) {
            if ($q->rowCount() < 1) {
                if(static::$_LOG) { \Command\Log::Log($sql . "\n" . print_r($q->errorInfo(), true)."\n".print_r($params, true), __FILE__, __LINE__); }
                $t = new \Exceptions\ItemNotFound("Item not found in " . static::TableName() . ".");
                $t->AddSQL($sql);
                throw $t;
            }
            if(static::$_LOG) { \Command\Log::Log($sql . "\n" . print_r($q->errorInfo(), true), __FILE__, __LINE__); }
            while ($row = $q->fetch(\PDO::FETCH_ASSOC)) { $ret[] = new static($row); }
        } else {
            //\Command\Log::Log(print_r([[$sql,$q]], true), __FILE__, __LINE__);
            \Command\Log::Log(print_r($q->errorInfo(), true), __FILE__, __LINE__);
        }
        return $ret;
    }
    public static function LoadBy(array $whereArray = array(), array $orderBy = array(), $limit = false) {
        //$conn = \DBConnectionFactory::getFactory()->getConnection();
        $sql = static::_fixLimit(static::_fixOrderBy(static::_fixWhere("SELECT * FROM `" . static::TableName()."`", $whereArray), $orderBy), $limit);
        error_log($sql."\n\n\n\n");
        return static::_LoadBySQL($sql, $whereArray);
    }

    protected $_data = array();
    protected $_nData = array();

    public function serialize ( ) { return serialize($this->ToArray()); }
    public function unserialize ( $serialized ) { $this->_data = unserialize($serialized); }
    public function jsonSerialize() { return $this->ToArray(); }
    
    public function __construct($arrId = null) {
        if ($arrId === null) {
            return;
        }
        if (is_array($arrId)) {
            $this->_data = $arrId;
        } else {
            $this->_loadById($arrId);
        }
    }

    public function __get($name) {
        if (isset($this->_nData[$name])) {
            return $this->_nData[$name];
        }
        if (isset($this->_data[$name])) {
            return $this->_data[$name];
        }
        return null;
    }

    public function __set($name, $value) {
        $this->_nData[$name] = $value;
    }

    public function Insert() {
        return $this->_sqlSave("INSERT INTO", $this->_getInsertEnd());
    }
    public function Delete() {
        if(!isset($this->id)) {
            throw new \Exceptions\Argument("Can't currently delete rows without an ID.");
        }
        $sql = "DELETE FROM ".static::TableName()." WHERE id = '{$this->id}'";
        $conn = \DBConnectionFactory::getFactory()->getConnection();
        $q = $conn->prepare($sql);
        #$q->debugDumpParams();
        if ($q->execute()) {
            if ($q->rowCount() < 1) {
                //error_log(" ZERO ROWS AFFECTED! ");
            }
            if(static::$_LOG) { error_log(print_r($q->errorInfo(), true)); }
        } else {
            error_log(print_r($q->errorInfo(), true));
            error_log(print_r($q, true));
        }
        return $this;
    }
    public function Save() {
        if ($this->IsNew()) {
            return $this->Insert();
        }
        return $this->_sqlSave("UPDATE", "WHERE " . $this->_where());
    }
    protected function _getInsertEnd() { return ''; }
    protected function _sqlSave($sqlStart, $sqlEnd = '') {
        if (!$this->Dirty()) {
            return $this;
        }
        $sql = $sqlStart . " " . static::TableName() . " SET";
        foreach (array_keys($this->_nData) as $col) {
            $sql .= " `$col` = :$col,";
        }
        $sql = preg_replace('/,$/', '', $sql) . " " . $sqlEnd;
        #error_log($sql);
        $conn = \DBConnectionFactory::getFactory()->getConnection();
        $q = $conn->prepare($sql);
        foreach ($this->_nData as $col => $val) {
            #error_log("binding :$col => $val");
            $q->bindValue(":$col", $val, (is_numeric($val) ? \PDO::PARAM_INT : \PDO::PARAM_STR));
        }
        #$q->debugDumpParams();
        if ($q->execute()) {
            if ($q->rowCount() < 1) {
                //error_log(" ZERO ROWS AFFECTED! ");
            }
            if (!$this->id) {
                $lastId = $conn->lastInsertId();
                if ($lastId) { $this->id = $lastId; }
            }
            if(static::$_LOG) { 
                \Command\Log::Log($sql, __FILE__, __LINE__);
                \Command\Log::Log("errorInfo " . print_r($q->errorInfo(), true), __FILE__, __LINE__);
                \Command\Log::Log("data ".print_r($this->_data, true), __FILE__, __LINE__);
                \Command\Log::Log("ndata ".print_r($this->_nData, true), __FILE__, __LINE__); 
            }
            $this->_data = array_merge($this->_data, $this->_nData);
            $this->_nData = array();
        } else {
            if(static::$_LOG) {
                error_log(print_r($q->errorInfo(), true));
                error_log(print_r($q, true));
            }
            $errs = $q->errorInfo();
            throw new Exception($errs[2]." in $sql");
        }
        return $this;
    }

    public function IsNew() {
        return empty($this->_data);
    }

    public function Dirty() {
        foreach ($this->_nData as $col => $val) {
            if(isset($this->_data[$col]) && $this->_data[$col] === $val) { unset($this->_nData[$col]); }
        }
        return !empty($this->_nData);
    }

    public function __isset($name) {
        return isset($this->_data[$name]);
    }

    public function __unset($name) {
        unset($this->_data[$name]);
    }

    public function ToArray() {
        return array_merge($this->_data, $this->_nData);
    }

    protected function _where() {
        $conn = \DBConnectionFactory::getFactory()->getConnection();
        $where = '';
        $count = 0;
        foreach ($this->_data as $col => $val) {
            if ($val) {
                $where .= "`$col` = " . (is_numeric($val) ? $val : $conn->quote($val)) . " AND ";
                $count++;
            }
            if ($count >= 10) {
                break;
            }
        }
        return rtrim($where, ' AND ');
    }

    protected function _loadById($id) {
        if(!isset(static::$_CACHE[static::TableName()])) { static::$_CACHE[static::TableName()] = array(); }
        if(isset(static::$_CACHE[static::TableName()][$id])) {
            $this->_data = &static::$_CACHE[static::TableName()][$id]->_DATA();
            $this->_nData = &static::$_CACHE[static::TableName()][$id]->_NDATA();
            return $this;
        }
        $conn = \DBConnectionFactory::getFactory()->getConnection();
        $q = $conn->prepare("SELECT * FROM " . static::TableName() . " WHERE id = :id");
        $q->bindParam(':id', $id);
        if ($q->execute()) {
            if ($q->rowCount() < 1) {
                $t = new \Exceptions\ItemNotFound("$id not found in " . static::TableName() . ".");
                $t->AddSQL("SELECT * FROM " . static::TableName() . " WHERE id = :id");
                throw $t;
            }
            $this->_data = $q->fetch(\PDO::FETCH_ASSOC);
        }
        static::$_CACHE[static::TableName()][$id] = $this;
        return $this;
    }
    public function &_DATA() { return $this->_data; }
    public function &_NDATA() { return $this->_nData; }
}
