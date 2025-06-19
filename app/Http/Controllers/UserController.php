<?php

namespace App\Http\Controllers;

use App\Exceptions\YClientsException;
use App\Services\YClientsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

	public function destroy(Request $request) {
		Auth::guard('web')->logout();

		$request->session()->invalidate();
		$request->session()->regenerateToken();

		return redirect('/');
	}
}
