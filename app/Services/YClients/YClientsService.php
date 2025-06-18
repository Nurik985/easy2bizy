<?php

namespace App\Services\YClients;

use App\Exceptions\YClientsException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http; // Используем фасад Http для удобства
use Throwable;

/**
 * Class YClientsService
 * @package App\Services\YClients
 */
class YClientsService
{
	/**
	 * @var string
	 */
	protected $yClientsBaseUrl;


	/**
	 * @var string
	 */
	protected $tokenPartner;

	protected $apiHeaders;

	/**
	 * YClientsService constructor.
	 */
	public function __construct()
	{

		$this->apiHeaders = [
			'Accept'       => 'application/vnd.yclients.v2+json',
			'Content-Type' => 'application/json',
			'Authorization' => 'Bearer ' . config('services.yclients.partner_token'), // Ваш партнерский токен
		];

		// Получаем конечную точку API из конфигурации Laravel
		// Убедитесь, что у вас есть 'yclients.endpoint' в config/services.php или подобном файле
		$this->yClientsBaseUrl = config('services.yclients.base_url');
		if (empty($this->yClientsBaseUrl)) {
			throw new YClientsException("YClients API Endpoint not configured. Please set 'services.yclients.endpoint'.", 500);
		}

		// Получаем партнерский токен из конфигурации
		// Убедитесь, что у вас есть 'yclients.partner_token' в config/services.php или подобном файле
		$this->tokenPartner = config('services.yclients.partner_token');
		if (empty($this->tokenPartner)) {
			\Log::error("YClients Partner Token is missing or empty."); // Добавьте логирование
			throw new YClientsException("YClients Partner Token not configured. Please set 'services.yclients.partner_token'.", 500);
		}
		\Log::info("YClients Partner Token loaded: " . ($this->tokenPartner ? '******' : 'MISSING')); // Логирование для отладки
	}

	/**
	 * Получаем партнерский токен.
	 *
	 * @return string
	 */
	public function getTokenPartner(): string
	{
		return $this->tokenPartner;
	}

	/**
	 * Устанавливаем партнерский токен.
	 *
	 * @param string $tokenPartner
	 */
	public function setTokenPartner(string $tokenPartner): void
	{
		$this->tokenPartner = $tokenPartner;
	}

	/**
	 * Подготовка и выполнение запроса к API YClients.
	 *
	 * @param string $urlPath - Относительный путь к ресурсу API (например, 'auth')
	 * @param array $parameters - Параметры запроса
	 * @param string $method - Метод HTTP (GET, POST, PUT, DELETE)
	 * @param bool|string $auth - Если true, то авторизация партнерская. Если string, то пользовательская авторизация (токен пользователя).
	 * @return \Illuminate\Http\Client\Response
	 * @throws YClientsException
	 */
	protected function request(string $urlPath, array $parameters = [], string $method = 'GET', $auth = true)
	{
		$fullUrl = $this->yClientsBaseUrl . '/' . ltrim($urlPath, '/'); // Убедимся, что нет двойных слэшей

		/** @var PendingRequest $httpClient */
		$httpClient = Http::withHeaders([
			'Content-Type' => 'application/json',
			'Accept' => 'application/json', // Обычно полезно явно указать, что ожидаем JSON
		]);

		if ($auth) {
			if (!$this->tokenPartner) {
				throw new YClientsException('Partner token not set in configuration.', 500);
			}
			$authHeader = 'Bearer ' . $this->tokenPartner;
			if (is_string($auth)) {
				$authHeader .= ', User ' . $auth;
			}
			$httpClient->withHeaders(['Authorization' => $authHeader]);
		}

		// Выбираем метод запроса
		$method = strtolower($method);
		switch ($method) {
			case 'get':
				$response = $httpClient->get($fullUrl, $parameters);
				break;
			case 'post':
				$response = $httpClient->post($fullUrl, $parameters);
				break;
			case 'put':
				$response = $httpClient->put($fullUrl, $parameters);
				break;
			case 'delete':
				$response = $httpClient->delete($fullUrl, $parameters);
				break;
			default:
				throw new YClientsException("Unsupported HTTP method: {$method}", 500);
		}

		// Проверяем, вернул ли API ошибку по HTTP-статусу
		if ($response->failed()) {

			$errorBody = $response->body();
			$statusCode = $response->status();
			$parsedErrorMessage = "Unknown YClients API error."; // Сообщение по умолчанию

			try {
				$jsonResponse = $response->json(); // Используем $response->json() напрямую, оно уже парсит JSON

				// *** НОВАЯ ЛОГИКА ПАРСИНГА ***
				if (isset($jsonResponse['errors']['message'])) {
					$parsedErrorMessage = $jsonResponse['errors']['message'];
				} elseif (isset($jsonResponse['meta']['message'])) {
					$parsedErrorMessage = $jsonResponse['meta']['message'];
				} elseif (isset($jsonResponse['message'])) {
					// На случай, если есть общий ключ 'message'
					$parsedErrorMessage = $jsonResponse['message'];
				} else {
					// Если ни один из ожидаемых ключей не найден, используем весь JSON-ответ как строку
					$parsedErrorMessage = json_encode($jsonResponse);
				}
			} catch (Throwable $e) {
				// Если $response->json() вызвало ошибку (т.е. это невалидный JSON)
				$parsedErrorMessage = "API returned invalid JSON error or unparsable response: " . $errorBody;
			}

			// Выбрасываем YClientsException с уже чистым сообщением
			throw new YClientsException($parsedErrorMessage, $statusCode);
		}

		return $response;
	}
}
