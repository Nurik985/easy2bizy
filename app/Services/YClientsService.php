<?php

namespace App\Services;

use App\Exceptions\YClientsException;
use DateTimeInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;
use Throwable;
use DateTime;

class YClientsService
{
    protected string $yClientsBaseUrl;
    protected ?string $tokenPartner;

    /**
     * @throws YClientsException
     */
    public function __construct()
    {
        $this->yClientsBaseUrl = config('services.yclients.base_url');
        $this->tokenPartner = config('services.yclients.partner_token');

        if (empty($this->yClientsBaseUrl)) {
            throw new YClientsException("YClients Base URL not found in configuration.", 500);
        }
        if (empty($this->tokenPartner)) {
            throw new YClientsException("YClients Partner Token not found in configuration.", 500);
        }
    }

    /**
     * Возвращает партнерский токен.
     *
     * @return string
     */
    public function getTokenPartner(): string
    {
        return $this->tokenPartner;
    }

    /**
     * Устанавливает партнерский токен.
     *
     * @param string $tokenPartner
     * @return void
     */
    public function setTokenPartner(string $tokenPartner): void
    {
        $this->tokenPartner = $tokenPartner;
    }

    /**
     * Выполняет запрос к YClients API.
     *
     * @param string $urlPath Путь к эндпоинту API.
     * @param array $parameters Параметры запроса.
     * @param string $method Метод HTTP.
     * @param bool|string $auth Если true, используется партнерская авторизация. Если string,
     * то это пользовательский токен, который добавляется как 'User {token}'.
     * Если false, авторизация не используется.
     * @return \Illuminate\Http\Client\Response
     * @throws YClientsException
     * @throws ConnectionException
     */
    protected function request(
        string $urlPath,
        array $parameters = [],
        string $method = 'GET',
        bool|string $auth = true
    ): \Illuminate\Http\Client\Response {
        $fullUrl = $this->yClientsBaseUrl . '/' . ltrim($urlPath, '/');

        /** @var PendingRequest $httpClient */
        $httpClient = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])
            ->timeout(30);

        if ($auth !== false) {
            $authHeaderValue = '';
            // Если $auth - это строка, используем ее как пользовательский токен.
            // YClients API требует 'Bearer {partner_token}, User {user_token}' для многих операций.
            // Но для 'auth' эндпоинта, авторизация не требуется в заголовках,
            // данные отправляются в теле запроса.
            if (is_string($auth) && $urlPath !== 'auth') { // Добавляем условие для 'auth'
                $authHeaderValue = 'Bearer ' . $this->tokenPartner . ', User ' . $auth;
            } elseif ($auth === true) {
                $authHeaderValue = 'Bearer ' . $this->tokenPartner;
            }

            if (!empty($authHeaderValue)) { // Добавляем заголовок только если он не пуст
                $httpClient->withHeaders(['Authorization' => $authHeaderValue]);
            }
        }

        $method = strtolower($method);
        if ($method == 'get') {
            $response = $httpClient->get($fullUrl, $parameters);
        } elseif ($method == 'post') {
            $response = $httpClient->post($fullUrl, $parameters);
        } elseif ($method == 'put') {
            $response = $httpClient->put($fullUrl, $parameters);
        } elseif ($method == 'delete') {
            $response = $httpClient->delete($fullUrl, $parameters);
        } else {
            throw new YClientsException("Unsupported HTTP method: {$method}", 500);
        }

        if ($response->failed()) {
            $errorBody = $response->body();
            $statusCode = $response->status();
            $parsedErrorMessage = "Unknown YClients API error.";

            try {
                $jsonResponse = $response->json();

                if ($jsonResponse === null) {
                    $parsedErrorMessage = "API returned invalid JSON error or unparsable response: " . $errorBody;
                } elseif (isset($jsonResponse['errors']['message'])) {
                    $parsedErrorMessage = $jsonResponse['errors']['message'];
                } elseif (isset($jsonResponse['meta']['message'])) {
                    $parsedErrorMessage = $jsonResponse['meta']['message'];
                } elseif (isset($jsonResponse['message'])) {
                    $parsedErrorMessage = $jsonResponse['message'];
                } else {
                    $parsedErrorMessage = json_encode($jsonResponse);
                }
            } catch (Throwable $e) {
                $parsedErrorMessage = "API returned non-JSON error or unparsable response: " . $errorBody;
            }

            throw new YClientsException($parsedErrorMessage, $statusCode);
        }

        return $response;
    }

    // --- Методы для работы с компаниями ---

    /**
     * @throws YClientsException
     * @throws ConnectionException
     */
    public function getCompanies(
        ?int $groupId = null,
        ?bool $active = null,
        ?bool $moderated = null,
        ?bool $forBooking = null,
        ?bool $my = null,
        ?string $userToken = null
    ): array {
        if ($my && !$userToken) {
            throw new YClientsException("User token is required when 'my' parameter is set to true.", 400);
        }

        $parameters = [];
        if ($groupId !== null) {
            $parameters['group_id'] = $groupId;
        }
        if ($active !== null) {
            $parameters['active'] = $active;
        }
        if ($moderated !== null) {
            $parameters['moderated'] = $moderated;
        }
        if ($forBooking !== null) {
            $parameters['forBooking'] = $forBooking;
        }
        if ($my !== null) {
            $parameters['my'] = $my;
        }

        $authOption = ($userToken !== null) ? $userToken : true;

        $response = $this->request('companies', $parameters, 'GET', $authOption);
        return $response->json();
    }

    /**
     * @throws YClientsException
     * @throws ConnectionException
     */
    public function createCompany(array $fields, string $userToken): array
    {
        if (!isset($fields['title']) || empty($fields['title'])) {
            throw new YClientsException("Company title is required to create a company.", 400);
        }

        $response = $this->request('companies', $fields, 'POST', $userToken);
        return $response->json();
    }

    /**
     * @throws YClientsException
     * @throws ConnectionException
     */
    public function getCompany(int $companyId, ?string $userToken = null): array
    {
        $urlPath = "company/{$companyId}";
        $authOption = ($userToken !== null) ? $userToken : true;
        $response = $this->request($urlPath, [], 'GET', $authOption);
        return $response->json();
    }

    /**
     * @throws YClientsException
     * @throws ConnectionException
     */
    public function updateCompany(int $companyId, array $fields, string $userToken): array
    {
        $urlPath = "company/{$companyId}";
        $response = $this->request($urlPath, $fields, 'PUT', $userToken);
        return $response->json();
    }

    /**
     * @throws YClientsException
     * @throws ConnectionException
     */
    public function deleteCompany(int $companyId, string $userToken): array
    {
        $urlPath = "company/{$companyId}";
        $response = $this->request($urlPath, [], 'DELETE', $userToken);
        return $response->json();
    }

    // --- Методы для работы с сотрудниками ---

    /**
     * @throws YClientsException
     * @throws ConnectionException
     */
    public function getStaff(int $companyId, string $userToken, ?int $staffId = null, array $params = []): array
    {
        $urlPath = "staff/{$companyId}";
        if ($staffId !== null) {
            $urlPath .= "/{$staffId}";
        }
        $response = $this->request($urlPath, $params, 'GET', $userToken);
        return $response->json();
    }

    // --- Методы для работы с категориями услуг ---

    /**
     * @throws YClientsException
     * @throws ConnectionException
     */
    public function getServiceCategories(int $companyId, ?string $userToken = null, ?int $staffId = null): array
    {
        $urlPath = "service_categories/{$companyId}";
        $parameters = [];
        $authOption = true;

        if ($staffId !== null) {
            $urlPath .= "/{$staffId}";
        }

        if ($userToken !== null) {
            $authOption = $userToken;
        }

        $response = $this->request($urlPath, $parameters, 'GET', $authOption);
        return $response->json();
    }

    /**
     * @throws YClientsException
     * @throws ConnectionException
     */
    public function createServiceCategory(int $companyId, array $fields, string $userToken): array
    {
        $urlPath = "service_categories/{$companyId}";
        $response = $this->request($urlPath, $fields, 'POST', $userToken);
        return $response->json();
    }

    /**
     * @throws YClientsException
     * @throws ConnectionException
     */
    public function getServiceCategory(int $companyId, int $categoryId, ?string $userToken = null): array
    {
        $urlPath = "service_categories/{$companyId}/{$categoryId}";
        $authOption = true;
        if ($userToken !== null) {
            $authOption = $userToken;
        }
        $response = $this->request($urlPath, [], 'GET', $authOption);
        return $response->json();
    }

    /**
     * @throws YClientsException
     * @throws ConnectionException
     */
    public function updateServiceCategory(int $companyId, int $categoryId, array $fields, string $userToken): array
    {
        $urlPath = "service_categories/{$companyId}/{$categoryId}";
        $response = $this->request($urlPath, $fields, 'PUT', $userToken);
        return $response->json();
    }

    /**
     * @throws YClientsException
     * @throws ConnectionException
     */
    public function deleteServiceCategory(int $companyId, int $categoryId, string $userToken): array
    {
        $urlPath = "service_categories/{$companyId}/{$categoryId}";
        $response = $this->request($urlPath, [], 'DELETE', $userToken);
        return $response->json();
    }

    // --- Методы для работы с услугами ---

    /**
     * @throws YClientsException
     * @throws ConnectionException
     */
    public function getServices(
        int $companyId,
        ?string $userToken = null,
        ?int $serviceId = null,
        ?int $staffId = null,
        ?int $categoryId = null
    ): array {
        $urlPath = "services/{$companyId}";
        $parameters = [];
        $authOption = true;

        if ($serviceId !== null) {
            $urlPath .= "/{$serviceId}";
        }

        if ($staffId !== null) {
            $parameters['staff_id'] = $staffId;
        }
        if ($categoryId !== null) {
            $parameters['category_id'] = $categoryId;
        }

        if ($userToken !== null) {
            $authOption = $userToken;
        }

        $response = $this->request($urlPath, $parameters, 'GET', $authOption);
        return $response->json();
    }

    /**
     * @throws YClientsException
     * @throws ConnectionException
     */
    public function createService(int $companyId, array $fields, string $userToken): array
    {
        $urlPath = "services/{$companyId}";
        if (!isset($fields['title']) || !isset($fields['category_id'])) {
            throw new YClientsException("Missing required fields 'title' or 'category_id' for service creation.", 400);
        }
        $response = $this->request($urlPath, $fields, 'POST', $userToken);
        return $response->json();
    }

    /**
     * @throws YClientsException
     * @throws ConnectionException
     */
    public function updateService(int $companyId, int $serviceId, array $fields, string $userToken): array
    {
        $urlPath = "services/{$companyId}/{$serviceId}";
        $response = $this->request($urlPath, $fields, 'PUT', $userToken);
        return $response->json();
    }

    /**
     * @throws YClientsException
     * @throws ConnectionException
     */
    public function deleteService(int $companyId, int $serviceId, string $userToken): array
    {
        $urlPath = "services/{$companyId}/{$serviceId}";
        $response = $this->request($urlPath, [], 'DELETE', $userToken);
        return $response->json();
    }

    // --- Методы для работы с расписанием (Schedule) ---

    /**
     * @throws YClientsException
     * @throws ConnectionException
     * @throws \Exception
     */
    public function getSchedule(
        int $companyId,
        string $userToken,
        ?int $staffId = null,
        ?string $dateStart = null,
        ?string $dateEnd = null
    ): array {
        $formattedDateStart = (new DateTime($dateStart ?? '-30 day'))->format('Y-m-d');
        $formattedDateEnd = (new DateTime($dateEnd ?? '+5 day'))->format('Y-m-d');

        if ($staffId === null) {
            throw new YClientsException("Staff ID is required for getSchedule method in current API URL structure.", 400);
        }

        $urlPath = "schedule/{$companyId}/{$staffId}/{$formattedDateStart}/{$formattedDateEnd}";

        $response = $this->request($urlPath, [], 'GET', $userToken);
        return $response->json();
    }

    // --- Методы для работы с записями (Records) ---

    /**
     * @throws YClientsException
     * @throws ConnectionException
     * @throws \Exception
     */
    public function getRecords(int $companyId, string $userToken, array $filters = []): array
    {
        $urlPath = "records/{$companyId}";
        $parameters = [];

        foreach ($filters as $key => $value) {
            if ($value === null) {
                continue;
            }

            switch ($key) {
                case 'start_date':
                case 'end_date':
                case 'c_start_date':
                case 'c_end_date':
                    if ($value instanceof DateTime) {
                        $parameters[$key] = $value->format('Y-m-d');
                    } elseif (is_string($value)) {
                        $parameters[$key] = (new DateTime($value))->format('Y-m-d');
                    }
                    break;
                case 'changed_after':
                case 'changed_before':
                    if ($value instanceof DateTime) {
                        $parameters[$key] = $value->format(DateTime::ISO8601);
                    } elseif (is_string($value)) {
                        $parameters[$key] = (new DateTime($value))->format(DateTime::ISO8601);
                    }
                    break;
                default:
                    $parameters[$key] = $value;
                    break;
            }
        }

        $response = $this->request($urlPath, $parameters, 'GET', $userToken);
        return $response->json();
    }

    /**
     * @throws YClientsException
     * @throws ConnectionException
     */
    public function createRecord(int $companyId, string $userToken, array $recordData): array
    {
        $urlPath = "records/{$companyId}";

        if (isset($recordData['datetime']) && $recordData['datetime'] instanceof DateTime) {
            $recordData['datetime'] = $recordData['datetime']->format(DateTime::ISO8601);
        } elseif (isset($recordData['datetime']) && is_string($recordData['datetime'])) {
            try {
                $recordData['datetime'] = (new DateTime($recordData['datetime']))->format(DateTime::ISO8601);
            } catch (Throwable $e) {
                throw new YClientsException("Invalid 'datetime' format for record creation.", 400, $e);
            }
        }

        $requiredFields = ['staff_id', 'services', 'client', 'datetime', 'seance_length', 'save_if_busy', 'send_sms'];
        foreach ($requiredFields as $field) {
            if (!isset($recordData[$field])) {
                throw new YClientsException("Missing required field '{$field}' for record creation.", 400);
            }
        }

        $response = $this->request($urlPath, $recordData, 'POST', $userToken);
        return $response->json();
    }

    /**
     * @throws YClientsException
     * @throws ConnectionException
     */
    public function getRecord(int $companyId, int $recordId, string $userToken): array
    {
        $urlPath = "records/{$companyId}/{$recordId}";
        $response = $this->request($urlPath, [], 'GET', $userToken);
        return $response->json();
    }

    /**
     * @throws YClientsException
     * @throws ConnectionException
     */
    public function updateRecord(int $companyId, int $recordId, string $userToken, array $recordData): array
    {
        if (isset($recordData['datetime']) && $recordData['datetime'] instanceof DateTime) {
            $recordData['datetime'] = $recordData['datetime']->format(DateTime::ISO8601);
        } elseif (isset($recordData['datetime']) && is_string($recordData['datetime'])) {
            try {
                $recordData['datetime'] = (new DateTime($recordData['datetime']))->format(DateTimeInterface::ISO8601);
            } catch (Throwable $e) {
                throw new YClientsException("Invalid 'datetime' format for record update.", 400, $e);
            }
        }

        $urlPath = "records/{$companyId}/{$recordId}";
        $response = $this->request($urlPath, $recordData, 'PUT', $userToken);
        return $response->json();
    }

    /**
     * @throws YClientsException
     * @throws ConnectionException
     */
    public function deleteRecord(int $companyId, int $recordId, string $userToken): array
    {
        $urlPath = "records/{$companyId}/{$recordId}";
        $response = $this->request($urlPath, [], 'DELETE', $userToken);
        return $response->json();
    }

    // --- Методы для работы с клиентами (Clients) ---

    /**
     * @throws YClientsException
     * @throws ConnectionException
     */
    public function getClients(int $companyId, string $userToken, array $filters = []): array
    {
        $urlPath = "clients/{$companyId}";
        $parameters = [];

        if (isset($filters['id']) && is_array($filters['id'])) {
            $parameters['id'] = $filters['id'];
        }
        if (isset($filters['fullname'])) {
            $parameters['fullname'] = (string) $filters['fullname'];
        }
        if (isset($filters['phone'])) {
            $parameters['phone'] = (string) $filters['phone'];
        }
        if (isset($filters['email'])) {
            $parameters['email'] = (string) $filters['email'];
        }
        if (isset($filters['page'])) {
            $parameters['page'] = (int) $filters['page'];
        }
        if (isset($filters['count'])) {
            $parameters['count'] = (int) $filters['count'];
        }

        $response = $this->request($urlPath, $parameters, 'GET', $userToken);
        return $response->json();
    }

    /**
     * @throws YClientsException
     * @throws ConnectionException
     */
    public function createClient(int $companyId, string $userToken, array $clientData): array
    {
        $urlPath = "clients/{$companyId}";

        if (!isset($clientData['name']) || empty($clientData['name'])) {
            throw new YClientsException("Client 'name' is required to create a client.", 400);
        }
        if (!isset($clientData['phone']) || empty($clientData['phone'])) {
            throw new YClientsException("Client 'phone' is required to create a client.", 400);
        }

        $response = $this->request($urlPath, $clientData, 'POST', $userToken);
        return $response->json();
    }

    /**
     * @throws YClientsException
     * @throws ConnectionException
     */
    public function getClient(int $companyId, int $clientId, string $userToken): array
    {
        $urlPath = "client/{$companyId}/{$clientId}";
        $response = $this->request($urlPath, [], 'GET', $userToken);
        return $response->json();
    }

    /**
     * @throws YClientsException
     * @throws ConnectionException
     */
    public function updateClient(int $companyId, int $clientId, string $userToken, array $clientData): array
    {
        $urlPath = "client/{$companyId}/{$clientId}";
        $response = $this->request($urlPath, $clientData, 'PUT', $userToken);
        return $response->json();
    }

    /**
     * @throws YClientsException
     * @throws ConnectionException
     */
    public function deleteClient(int $companyId, int $clientId, string $userToken): array
    {
        $urlPath = "client/{$companyId}/{$clientId}";
        $response = $this->request($urlPath, [], 'DELETE', $userToken);
        return $response->json();
    }

    // ДОБАВЛЯЕМ НОВЫЙ МЕТОД ДЛЯ АУТЕНТИФИКАЦИИ

    /**
     * Получает токен пользователя по логину и паролю.
     *
     * @param string $login Логин пользователя (обычно номер телефона).
     * @param string $password Пароль пользователя.
     * @return array Массив с токеном пользователя и связанными данными.
     * @throws YClientsException
     * @throws ConnectionException
     * @see http://docs.yclients.apiary.io/#reference/0/0/0
     */
    public function authenticate(string $login, string $password): array
    {
        $urlPath = 'auth';
        $parameters = [
            'login' => $login,
            'password' => $password,
        ];
        // Для эндпоинта 'auth' не нужно передавать партнерский токен в заголовке Authorization.
        // Он отправляется как часть тела запроса.
        // Поэтому передаем 'false' в качестве $auth параметра, чтобы отключить добавление заголовков авторизации
        // для этого конкретного запроса.
        $response = $this->request($urlPath, $parameters, 'POST');

        return $response->json();
    }
}
