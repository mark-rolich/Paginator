<?php
/**
* Extension of Pager abstract class
* Implements pagination for arrays
*
* @package Pager
* @author Mark Rolich <mark.rolich@gmail.com>
*/
class PagerArray extends Pager
{
    /**
    * Implementation of abstract method getData of Pager class
    *
    * Gets records from array with given offset and limit
    * and total number of records
    *
    * @param $data mixed - array of all the records
    * @return mixed - array of records
    */
    public function getData($data)
    {
        $this->total = count($data);
        $data = array_slice($data, $this->getOffset(), $this->limit);
        return $data;
    }
}
?>