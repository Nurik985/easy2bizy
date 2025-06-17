<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{

	public function login()
	{
		return view('auth.login');
	}

	public function loginAuth(Request $request)
	{
		$method = 'GET';
		$method = strtolower($method);

		$url     = env('YCLIENTSENDPOINT', 'https://api.yclients.com/api/v1') . '/' . $url;

		return $this->request->$method($url, $parameters, ['header' => $headers]);

		dd($request->all());
	}
}
