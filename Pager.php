<?php
/**
* Pagination abstract class
* Breaks down large data sets into different pages
* and generates HTML links to navigate between them
*
* @package Pager
* @author Mark Rolich <mark.rolich@gmail.com>
*/
abstract class Pager
{
    /**
    * @var int - how many items will be shown on the page
    */
    protected $limit;

    /**
    * @var int - pages count
    */
    protected $pages;

    /**
    * @var int - current page number
    */
    protected $pageNum;

    /**
    * @var int - count of page navigation links
    */
    protected $range;

    /**
    * @var int - total items count
    */
    protected $total;

    /**
    * Constructor
    *
    * @param $pageNum int
    * @param $limit int - default 10
    * @param $range int - default 10
    */
    public function __construct($pageNum, $limit = 10, $range = 10)
    {
        $this->pageNum = ($pageNum < 1) ? 1 : (int)$pageNum;
        $this->limit   = $limit;
        $this->range   = $range;
    }

    /**
    * Gets current offset
    *
    * @return int
    */
    public function getOffset()
    {
        return ($this->pageNum - 1)*$this->limit;
    }

    /**
    * Definition of abstract method getData
    *
    * Gets records and total number of records from different sources
    *
    * @param $options mixed - array of options
    * (should be defined individually for current implementation)
    *
    * @return mixed - array of records
    */
    abstract public function getData($options);

    /**
    * Generates array of page navigation main links
    *
    * @return mixed
    */
    protected function getNavRange()
    {
        $this->pages = (int)ceil($this->total/$this->limit);

        $result = array();

        if ($this->total > $this->limit
            && $this->pageNum >= 1
            && $this->pageNum <= $this->pages
        ) {
            $side = (int)floor($this->range/2);
            $corr = abs($this->range%2 - 1);

            $low = $this->pageNum - $side;
            $high = $this->pageNum + $side - $corr;

            if ($this->pages > $this->range) {
                if ($low < 1) {
                    $low = 1;
                    $high = $this->range;
                }

                if ($high > $this->pages) {
                    $low = $this->pages - $this->range + 1;
                    $high = $this->pages;
                }
            } else {
                $low = 1;
                $high = $this->pages;
            }

            $result = range($low, $high);
        }

        return $result;
    }

    /**
    * Gets previous page number
    *
    * @return int
    */
    protected function getPrev()
    {
        return ($this->pageNum > 1) ? $this->pageNum - 1 : 1;
    }

    /**
    * Gets next page number
    *
    * @return int
    */
    protected function getNext()
    {
        return ($this->pageNum < $this->pages)
                ? $this->pageNum + 1
                : $this->pages;
    }

    /**
    * Forms pager navigation links array
    * depending on provided options
    * to render as HTML string.
    * Merges main navigation links with links to
    * first, previous, next and last pages
    * Sets css class, label, page number attributes to links.
    *
    * @param $data mixed - navigation main links got from getNavRange() method
    * @param $options - associative array of navigation links options
    * @return mixed - formatted array
    */
    protected function format($data, $options)
    {
        $result = array();

        $first = $prev = $next = $last = 2;

        $firstLabel = '&lt;&lt;';
        $prevLabel = '&lt;';
        $nextLabel = '&gt;';
        $lastLabel = '&gt;&gt;';

        extract($options);

        $firstData = array('num' => 1,                  'label' => $firstLabel);
        $prevData  = array('num' => $this->getPrev(),   'label' => $prevLabel);
        $nextData  = array('num' => $this->getNext(),   'label' => $nextLabel);
        $lastData  = array('num' => $this->pages,       'label' => $lastLabel);

        if ($first == 1 || $first == 2 && $this->pageNum != 1) {
            $result['first'] = $firstData;
        }

        if ($prev == 1 || $prev == 2 && $this->pageNum != 1) {
            $result['prev'] = $prevData;
        }

        foreach ($data as $link) {
            $result[] = array(
                'class' => ($link == $this->pageNum) ? 'selected' : 'regular',
                'num' => $link,
                'label' => $link
            );
        }

        if ($next == 1 || $next == 2 && $this->pageNum != $this->pages) {
            $result['next'] = $nextData;
        }

        if ($last == 1 || $last == 2 && $this->pageNum != $this->pages) {
            $result['last'] = $lastData;
        }

        return $result;
    }

    /**
    * Renders formatted page navigation links array to HTML string
    *
    * @param $options mixed - associative array of navigation links options
    *
    * can have the following keys and values:
    * 'url' - string or not set (with integer placeholder in sprintf style)
    * 'first' - int (0|1|2)
    * 'prev' - int (0|1|2)
    * 'next' - int (0|1|2)
    * 'last' - int (0|1|2)
    * 'firstLabel' - string or not set
    * 'prevLabel' - string or not set
    * 'nextLabel' - string or not set
    * 'lastLabel' - string or not set
    *
    * @return string
    */
    public function render($options = array())
    {
        $result = '';
        $navData = $this->getNavRange();

        if (!empty($navData)) {
            $navData = $this->format($navData, $options);

            $result = '<ul class="pager">';
            $url = isset($options['url']) ? $options['url'] : '?p=%d';

            foreach ($navData as $k => $page) {
                $class = $k;
                extract($page);

                $result .= '<li>';

                if ($class == 'selected') {
                    $result .= '<span>' . $label . '</span>';
                } else {

                    $result .= '<a href="' . sprintf($url, $num) . '" class="'
                            . $class . '">'
                            . $label . '</a>';
                }

                $result .= '</li>';
            }

            $result .= '</ul>';
        }

        return $result;
    }

    /**
    * Renders informational message about
    * current offset and records total number into string
    *
    * @param $string string - string with three integer (decimal) placeholders (printf style)
    * @return $stats string - formatted string with replaced placholders
    */
    public function renderStats($string)
    {
        $stats = '';

        if ($this->total > $this->limit
            && $this->pageNum >= 1
            && $this->pageNum <= $this->pages)
        {
            $offset = $this->getOffset();
            $total = (int)$this->total;
            $limit = ($this->pageNum == $this->pages) ? $total : $offset + $this->limit;

            $stats = sprintf($string, $offset + 1, $limit, $total);
        }

        return $stats;
    }
}
?>