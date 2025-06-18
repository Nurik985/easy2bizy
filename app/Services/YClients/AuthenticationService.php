<?php

namespace App\Services\YClients;

use App\Exceptions\YClientsException;
use Illuminate\Http\Client\Response; // Используем Response из Http-клиента Laravel
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


/**
 * Class AuthenticationService
 * @package App\Services\YClients
 */
class AuthenticationService extends YClientsService
{


	/**
	 * Получает токен аутентификации пользователя и информацию о компании.
	 *
	 * @param string $login
	 * @param string $password
	 * @return array Возвращает массив с user_token, company_id и company_name.
	 * @throws YClientsException В случае ошибок API YClients или отсутствия данных.
	 */

	public function authenticateAndGetCompany(string $login, string $password)
	{
		// 1. Запрос на аутентификацию пользователя (login)
		$authResponse = $this->getAuthToken($login, $password);

		$authResultBody = $authResponse->json();

		// Проверка на ошибки ответа API аутентификации
		if (!$authResponse->successful() || !$authResultBody) {
			Log::error('YClients Auth API error or empty body', ['status' => $authResponse->status(), 'response' => $authResultBody]);
			throw new YClientsException("Ошибка аутентификации на сервисе YClients. Код: {$authResponse->status()}", $authResponse->status());
		}

		if (isset($authResultBody['errors']) && $authResultBody['errors']) {
			$code    = $authResultBody['errors']['code'] ?? 500;
			$message = $authResultBody['errors']['message'] ?? 'Неизвестная ошибка аутентификации YClients';
			Log::error('YClients Auth API specific error', ['code' => $code, 'message' => $message]);
			throw new YClientsException($message, $code);
		}

		$userToken = $authResultBody['user_token'] ?? null;

		if (!$userToken) {
			Log::error('YClients Auth API: user_token not found in successful response', ['response' => $authResultBody]);
			throw new YClientsException("YClients не вернул токен пользователя.", 500);
		}

		// 2. Запрос на получение ID компании
		// Headers для запроса компании должны включать user_token, а не partner_token
		$companyHeaders = array_merge($this->apiHeaders, ['Authorization' => 'Bearer ' . $userToken]);

		// companyName теперь будет браться из Laravel config
		$companyName = config('app.yclients_company_name'); // Добавьте это в config/app.php или config/services.php
		if (!$companyName) {
			throw new YClientsException("Отсутствует наименование компании в конфигах приложения.", 500);
		}

		$companyResponse = Http::withHeaders($companyHeaders)
			->get("{$this->baseUrl}/companies");

		$companyResultBody = $companyResponse->json();

		if (!$companyResponse->successful() || !$companyResultBody) {
			Log::error('YClients Company API error or empty body', ['status' => $companyResponse->status(), 'response' => $companyResultBody]);
			throw new YClientsException("Ошибка получения информации о компании на сервисе YClients.", $companyResponse->status());
		}

		$companyId = null;
		foreach ($companyResultBody as $company) {
			if (isset($company['id']) && $company['id'] &&
				isset($company['title']) && $company['title'] &&
				$company['title'] == $companyName
			) {
				$companyId = $company['id'];
				break;
			}
		}

		if (!$companyId) {
			Log::warning('YClients Company API: company ID not found for configured name', ['companyName' => $companyName, 'response' => $companyResultBody]);
			throw new YClientsException("Сервис YClients не вернул ID компании для '{$companyName}'.", 500);
		}

		// Возвращаем данные, которые будут использоваться в компоненте
		return [
			'user_token' => $userToken,
			'user_name' => $authResultBody['name'] ?? null,
			'user_phone' => $authResultBody['phone'] ?? null,
			'user_email' => $authResultBody['email'] ?? null,
			'company_id' => $companyId,
			'company_name' => $companyName,
		];
	}

	/**
	 * Получаем токен пользователя по логину-паролю.
	 *
	 * @param string $login
	 * @param string $password
	 *
	 * @return Response
	 * @throws YClientsException
	 * @see http://docs.yclients.apiary.io/#reference/0/0/0
	 */
	public function getAuthToken(string $login, string $password): Response
	{
		// Используем метод request из родительского класса YClientsService
		// Обратите внимание: метод 'auth' в YClients API, согласно документации, обычно POST.
		return $this->request(
			'auth',
			[
				'login' => $login,
				'password' => $password,
			],
			'POST', // Явно указываем POST
			true // Для получения токена пользователя, скорее всего, не требуется партнерская авторизация
		// Или она требуется, но по-другому, в зависимости от API YClients.
		// Если API требует партнерский токен для запроса 'auth', измените на `true`.
		);
	}
}
