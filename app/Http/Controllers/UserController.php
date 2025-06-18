<?php

namespace App\Http\Controllers;

use App\Exceptions\YClientsException;
use App\Services\YClients\AuthenticationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
	protected $authService;

	public function __construct(AuthenticationService $authService)
	{
		$this->authService = $authService;
	}

	public function login()
	{
		return view('auth.login');
	}

	public function loginAuth(Request $request)
	{

		$validator = $request->validate([
			'login' => 'required',
			'password' => 'required'
		], [
			'login.required' => 'Логин не должен быть пустым',
			'password.required' => 'Пароль не должен быть пустым'
		]);

		if ($validator->fails()) {
			// Если валидация не пройдена, возвращаем ошибки с кодом 422
			return response()->json([
				'message' => 'Ошибка валидации.',
				'errors' => $validator->errors()
			], 422); // Unprocessable Entity
		}

		try {
			$response = $this->authService->getAuthToken($request->login, $request->password);
			$responseData = $response->json();

			// Этот блок выполняется только если $response->successful() и не было выброшено YClientsException
			$userToken = $responseData['user_token'] ?? null;

			if ($userToken) {
				session(['yclients_user_token' => $userToken]);
				// Дополнительная логика, например, перенаправление на домашнюю страницу
				//return redirect()->intended('/dashboard')->with('success', 'Вы успешно вошли в систему!');
			} else {
				// Это маловероятно, так как YClientsException должен был быть выброшен
				Log::warning('YClients successful response without user_token:', $responseData);
				return back()->withInput()->withErrors(['api_error' => 'Произошла непредвиденная ошибка при получении токена.']);
			}

		} catch (YClientsException $e) {
			// Если вы попали сюда, значит YClientsException был правильно пойман.
			// Сообщение $e->getMessage() должно быть чистым.
			Log::error('YClients Authentication Error (specific):', ['message' => $e->getMessage(), 'code' => $e->getCode()]);
			return back()->withInput()->withErrors(['api_error' => $e->getMessage()]);

		} catch (\Exception $e) {
			// Этот блок должен ловить только ОБЩИЕ, неожиданные ошибки,
			// которые НЕ являются YClientsException.
			Log::error('Unexpected General Error during Authentication:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
			return back()->withInput()->withErrors(['api_error' => 'Произошла общая ошибка: ' . $e->getMessage()]);
		}
	}
}
