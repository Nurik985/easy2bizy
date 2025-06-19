<?php

namespace App\Livewire\User;

use App\Services\YClientsService;
use Illuminate\Support\Facades\Auth;
use JetBrains\PhpStorm\NoReturn;
use Livewire\Component;
use Masmerise\Toaster\Toastable;
use Illuminate\Support\Facades\Log;
use App\Exceptions\YClientsException;
use Illuminate\Validation\ValidationException;

use App\Models\User;
use App\Models\Company; // <-- Импортируем новую модель Company



class UserAuth extends Component
{
	use Toastable; // Используем трейт Toastable для вывода уведомлений

    public $login = '';
    public $password = '';

    // Свойства для обработки сценария выбора компании
    public array $availableCompanies = [];
    public ?int $selectedCompanyId = null;
    public ?int $userIdForCompanySelection = null;
    public ?string $userTokenForCompanySelection = null;

    protected YClientsService $yclientsService;

	protected $rules = [
		'login' => 'required',
		'password' => 'required',
	];

	protected $messages = [
		'login.required' => 'Заполните поле Логин',
		'password.required' => 'Заполните поле Пароль',
	];

    public function boot(YClientsService $yclientsService): void
    {
        // Внедрение зависимости через метод boot()
        $this->yclientsService = $yclientsService;
    }

    /**
     * Обрабатывает попытку входа пользователя.
     */
    public function auth()
    {
        try {
            $this->validate();

            $userLogin = $this->login;
            $userPassword = $this->password;

            // 1. Первичная аутентификация через YClients API
            $authData = $this->yclientsService->authenticate($userLogin, $userPassword);

            $userToken = $authData['user_token'] ?? null;

            if (empty($userToken)) {
                Log::warning("YClients API Auth: Missing user_token in response.", ['response' => $authData]);
                throw ValidationException::withMessages([
                    'login' => 'Не удалось получить токен пользователя. Проверьте данные или свяжитесь с поддержкой.'
                ]);
            }

            // 2. Создаем или обновляем локального пользователя YClients
            $user = User::updateOrCreate(
                ['login' => $userLogin], // <--- Используем очищенный логин
                [
                    'user_token' => $userToken,
                    'name' => $authData['name'] ?? null,
                    'phone' => $authData['phone'] ?? null,
                    'email' => $authData['email'] ?? null,
					'avatar' => $authData['avatar'] ?? null,
                    'password' => \Illuminate\Support\Str::random(10),
                ]
            );

            // Если токен обновился, сохраняем его
            if ($user->user_token !== $userToken) {
                $user->update(['user_token' => $userToken]);
            }

            // 3. Получаем список компаний, доступных для пользователя в YClients
            $yclientsCompaniesResponse = $this->yclientsService->getCompanies(my: true, userToken: $userToken);

            $companiesFromYclients = [];
            $companyIdsToAttach = [];

            foreach ($yclientsCompaniesResponse as $companyData) {
                if (isset($companyData['id']) && isset($companyData['title'])) {
                    $companiesFromYclients[$companyData['id']] = $companyData['title'];

                    // Сохраняем/обновляем каждую компанию в локальной БД
                    $company = Company::updateOrCreate(
                        ['yclients_id' => $companyData['id']],
                        ['name' => $companyData['title']]
                    );

                    // Собираем ID локальных компаний для привязки к пользователю
                    $companyIdsToAttach[] = $company->id; //
                }
            }

            if (empty($companiesFromYclients)) {
                throw new \App\Exceptions\YClientsException("У пользователя нет доступных компаний в YClients.", 404);
            }

            $user->companies()->sync($companyIdsToAttach);

            $currentCompanyId = array_key_first($companiesFromYclients);
            $currentCompanyName = $companiesFromYclients[$currentCompanyId];

            // Финализируем вход, используя ПЕРВУЮ найденную компанию
            $this->finalizeLogin($user, $userToken, $currentCompanyId, $currentCompanyName);

            session()->flash('success', 'Вы успешно вошли в систему: ' . $currentCompanyName);

            return redirect()->route('campaigns');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Ловим исключение валидации от Livewire
            // проходим по циклу все сообщения об ошибках и выводим их через Toaster
            foreach ($e->validator->errors()->all() as $message) {
                $this->error($message); // Используем error для ошибок валидации
            }
        } catch (YClientsException $e) {
            // Ловим специфические исключения от YClientsService
            Log::error('YClients Authentication Error (specific):', ['message' => $e->getMessage(), 'code' => $e->getCode()]);
            $this->error($e->getMessage()); // Выводим сообщение об ошибке YClients
        } catch (\Exception $e) {
            // Ловим любые другие неожиданные исключения
            Log::error('Unexpected General Error during Authentication:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            $this->error('Произошла общая ошибка: ' . $e->getMessage()); // Выводим общее сообщение об ошибке
        }
    }

    /**
     * Финализирует процесс входа, сохраняя данные и аутентифицируя пользователя.
     * Вынесено в отдельный метод, чтобы избежать дублирования.
     */
    public function finalizeLogin(User $user, string $userToken, int $companyId, string $companyName)
    {
        // Аутентификация пользователя в Laravel
        Auth::login($user); // Учитываем флаг "запомнить меня"

        // Сохраняем текущую компанию и токен в сессии
        session()->put('current_yclients_company_id', $companyId);
        session()->put('current_yclients_company_name', $companyName);
        session()->put('yclients_user_token', $userToken);

        // Удаляем временные данные выбора компании, если они были
        session()->forget('user_id_for_company_selection');
        session()->forget('user_token_for_company_selection');
    }

    public function render()
    {
        return view('livewire.user.user-auth');
    }
}
