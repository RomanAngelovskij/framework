<?php
namespace app\models;

use FM\BaseModel;

class Pages extends BaseModel{
	private $__totalResults;

	private $__resultsOnPage;

	private $__current = 1;

	private $__total;

	public function __construct($totalResults, $resultsOnPage){
		$this->__totalResults = $totalResults;

		$this->__resultsOnPage = $resultsOnPage;

		$this->__total = ceil($this->__totalResults/$this->__resultsOnPage);
	}

	public function setCurrent($page){
		$this->__current = $page;
	}

	public function current(){
		return $this->__current;
	}

	public function total(){
		return $this->__total;
	}

	public function offset(){
		return ($this->__current-1)*$this->__resultsOnPage;
	}

	public function resultsOnPage(){
		return $this->__resultsOnPage;
	}

	public function build($visiblePages = 10){
		if ($this->total() <= $visiblePages){
			return range(1, $this->total());
		}

		$start = $this->current() < 10 ? floor($this->current()/10)*10+1 : floor($this->current()/10)*10;

		if ($start == $this->current() && $start > 1){
			$start -= 1;
		}

		$end = $start+$visiblePages;

		return range($start, $end);
	}

}