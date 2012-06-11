<?php
/**
* Extension of Pager abstract class
* Implements pagination for CSV files
*
* @package Pager
* @author Mark Rolich <mark.rolich@gmail.com>
*/
class PagerCSV extends Pager
{
    /**
    * Implementation of abstract method getData of Pager class
    *
    * Gets records from CSV files with given offset and limit
    * and total number of records
    *
    * @param $fh mixed - CSV file pointer resource
    * @return mixed - array of records
    */
    public function getData($fh)
    {
        while (($row = fgetcsv($fh, 1000, ",")) !== false) {
            $data[] = $row;
        }

        $this->total = count($data);
        $data = array_slice($data, $this->getOffset(), $this->limit);

        return $data;
    }
}
?>