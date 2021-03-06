<?php

namespace ElCoop\Datatable\Controllers;

use App\Http\Controllers\Controller;
//use App\Services\DatatableService;
use ElCoop\Datatable\Services\DatatableService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;

class DatatableController extends Controller {
	
	public function list(Request $request, DatatableService $datatableService) {
		$query = $datatableService->query();
		return $query->paginate($request->input('per_page', 25));
	}
	
	public function export(Excel $excel, Request $request, DatatableService $datatableService) {
		return $excel->download($datatableService, "{$request->input('name')}.xls");
	}
}
