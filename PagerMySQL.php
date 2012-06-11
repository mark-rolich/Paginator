<?php
/**
* Extension of Pager abstract class
* Implements pagination for MySQL recordsets,
* can be used with three PHP MySQL modules (mysql, mysqli, pdo)
*
* @package Pager
* @author Mark Rolich <mark.rolich@gmail.com>
*/
class PagerMySQL extends Pager
{
    /**
    * @var string - MySQL query to find total rows count
    */
    private $countStmt = 'SELECT FOUND_ROWS()';

    /**
    * @var mixed - link to MySQL connection (resource - mysql or object - mysqli, pdo)
    */
    private $handle;

    /**
    * @var mixed - array of parameters for prepared query
    */
    private $params;

    /**
    * @var string - MySQL statement to find records with given offset and limit
    */
    private $stmt;

    /**
    * Determines the type of used PHP MySQL module from connection handle
    *
    * @return string - (mysql|mysqli|pdo)
    */
    private function getHandleType()
    {
        $type = '';

        if (is_resource($this->handle)) {
            $type = 'mysql';
        } elseif (is_object($this->handle)) {
            $type = strtolower(get_class($this->handle));
        }

        return $type;
    }

    /**
    * Implementation of abstract method getData of Pager class
    *
    * Gets records from MySQL table with given offset and limit
    * and total number of records for mysql, mysqli or pdo
    *
    * @param $options mixed - array of options
    *
    * contains the following keys and values
    * 'handle' - MySQL connection link (mandatory)
    * 'query' - generic MySQL query to get all of the records from the table - raw or prepared (mandatory)
    * 'params' - parameters for the prepared query (actual only when using mysqli or pdo)
    *
    * @return mixed - array of records
    */
    public function getData($options)
    {
        $data = array();

        extract($options);

        $stmt = str_replace('SELECT ', 'SELECT SQL_CALC_FOUND_ROWS ', $query);
        $stmt .= ' LIMIT ' . $this->getOffset() . ',' . $this->limit;

        $this->handle = $handle;
        $this->stmt = $stmt;

        if (isset($params)) {
            $this->params = $params;
        }

        switch ($this->getHandleType()) {
            case 'mysql':
                $data = $this->getDataMySQL();
                break;
            case 'mysqli':
                $data = $this->getDataMySQLI();
                break;
            case 'pdo':
                $data = $this->getDataPDO();
        }

        return $data;
    }

    /**
    * Gets records from MySQL table with given offset and limit for mysql PHP module
    * Calculates total numbers of record
    *
    * @return mixed - array of records
    */
    private function getDataMySQL()
    {
        $data = array();

        $sth = mysql_query($this->stmt, $this->handle);

        while ($row = mysql_fetch_assoc($sth)) {
            $data[] = $row;
        }

        mysql_free_result($sth);

        $sth = mysql_query($this->countStmt, $this->handle);
        list($this->total) = mysql_fetch_row($sth);

        mysql_free_result($sth);

        return $data;
    }

    /**
    * Gets records from MySQL table with given offset and limit for mysqli PHP module
    * Calculates total numbers of record
    *
    * @return mixed - array of records
    */
    private function getDataMySQLI()
    {
        $data = array();

        if (isset($this->params)) {
            $sth = $this->handle->prepare($this->stmt);

            call_user_func_array(array($sth, 'bind_param'), $this->params);

            $sth->execute();

            $sth = $sth->get_result();
        } else {
            $sth = $this->handle->query($this->stmt);
        }

        while ($row = $sth->fetch_assoc()) {
            $data[] = $row;
        }

        $sth->close();

        $sth = $this->handle->query($this->countStmt);
        list($this->total) = $sth->fetch_row();

        $sth->close();

        return $data;
    }

    /**
    * Gets records from MySQL table with given offset and limit for PDO PHP module
    * Calculates total numbers of record
    *
    * @return mixed - array of records
    */
    private function getDataPDO()
    {
        $data = array();

        if (isset($this->params)) {
            $sth = $this->handle->prepare($this->stmt);

            if (isset($this->params[0]) && is_array($this->params[0])) {
                foreach($this->params as $param) {
                    call_user_func_array(array($sth, 'bindValue'), $param);
                }

                $sth->execute();
            } else {
                $sth->execute($this->params);
            }
        } else {
            $sth = $this->handle->query($this->stmt);
        }

        $data = $sth->fetchAll(PDO::FETCH_ASSOC);

        $sth->closeCursor();

        $sth = $this->handle->query($this->countStmt);
        $this->total = $sth->fetchColumn();

        $sth->closeCursor();

        return $data;
    }
}
?>