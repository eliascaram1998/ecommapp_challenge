<?php

namespace App\Http\Controllers;

use Illuminate\Pagination\LengthAwarePaginator;

class BaseController extends Controller
{

	/**
	 * Returns view with list of objects, used in listAjax methods
	 * @param Object $class Object that it´s going to be listed
	 * @param array $filters Key => Value
	 * @param string $view
	 * @param string $url
	 * @param int $itemsPerPage
	 * @param array $isAuthenticated Optional - If the user is authenticated
	 * @return array
	 */
	public function listAction(
		object $class,
		array $filters,
		string $view,
		string $url = '',
		int $itemsPerPage = 10,
		bool $isAuthenticated = false,
	): array {
        
		$start = (($filters['page'] ?? 1) - 1) * $itemsPerPage;

		$arrayObjects = $class::index($filters, $start, $itemsPerPage);
		$count = $class::count($filters, $start);

		$pagination = new LengthAwarePaginator(
			$arrayObjects,
			$count,
			$itemsPerPage,
			$filters['page'] ?? 1,
			['path' => $url]
		);
      
		$htmlData = [
			'arrayObjects' => $arrayObjects,
			'isAuthenticated' => $isAuthenticated,
			'pagination' => $pagination,
        ];

		return [
			'result' => true,
			'html' => view($view, $htmlData)->render(),
			'data' => [
				'items' => $arrayObjects,
				'count' => $count,
				'start' => $start,
				'limit' => $itemsPerPage,
			],
		];
	}
}
