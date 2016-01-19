<?php
namespace app\controllers;

use FM;
use FM\Application;
use app\models\News;
use app\models\Pages;

class SiteController extends FM\BaseController{

	public function __construct(){

	}

	public function actionIndex($page = 1){
		$NewsModel = new News();

		$Query = $NewsModel->select();

		$totalNewsNumber = $Query->count();

		$Pagination = new Pages($totalNewsNumber, 20);

		$Pagination->setCurrent($page);

		$News = $Query->offset($Pagination->offset())->limit($Pagination->resultsOnPage())->get();

		$this->render('index', [
			'totalNewsNumber' => $totalNewsNumber,
			'News' => $News,
			'Pagination' => $Pagination,
			'newsOnPage' => 10,
		],
		!Application::$i->Input->isAjax());
	}
}