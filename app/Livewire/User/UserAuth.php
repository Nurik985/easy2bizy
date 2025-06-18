<?php

namespace App\Livewire\User;

use Livewire\Component;
use Masmerise\Toaster\Toastable;
use Illuminate\Support\Facades\Log;
use App\Exceptions\YClientsException;
use App\Services\YClients\AuthenticationService;

use App\Models\User;
use App\Models\Company; // <-- Импортируем новую модель Company

class UserAuth extends Component
{
	use Toastable; // Используем трейт Toastable для вывода уведомлений

	public $login;
	public $password;

	protected $rules = [
		'login' => 'required',
		'password' => 'required',
	];

	protected $messages = [
		'login.required' => 'Заполните поле Логин',
		'password.required' => 'Заполните поле Пароль',
	];


	public function auth(AuthenticationService $authService)
	{
		try {
			// 1. Выполняем Livewire-валидацию по заданным правилам
			$this->validate();

			// 2. Вызываем сервис для получения токена YClients
			// Передаем данные из свойств компонента: $this->login, $this->password
			//$response = $authService->getAuthToken($this->login, $this->password);

			// 2. Вызываем сервис для аутентификации и получения данных о компании
			$authData = $authService->authenticateAndGetCompany($this->login, $this->password);
			dd($authData);

			// 3. Сохраняем информацию в сессии
			session([
				'yclients_user_token' => $authData['user_token'],
				'company_id'          => $authData['company_id'], // Сохраняем ID компании
				'user_id'             => null, // Будет установлен после сохранения в БД
			]);

			$company = Company::firstOrCreate(
				['id' => $authData['company_id']], // Ищем по ID YClients
				['name' => $authData['company_name']] // Если не найдена, создаем с именем
			);

			// 2. Сохраняем/обновляем информацию о пользователе
			$user = User::updateOrCreate(
				[
					'login'      => $this->login,
					'company_id' => $company->id, // Связываем пользователя с ID компании
				],
				[
					'name'                  => $authData['user_name'],
					'email'                 => $authData['user_email'],
					'phone'                 => $authData['user_phone'],
					'yclients_user_token'   => $authData['user_token'],
				]
			);

			// 3. Авторизуем пользователя в Laravel Guard
			auth()->login($user);

			// 4. Сохраняем информацию о компании в сессию (опционально, так как теперь есть связь через user)
			// Но это может быть полезно для быстрого доступа без запроса к БД
			session([
				'yclients_company_id'   => $company->id,
				'yclients_company_name' => $company->name,
			]);

			$this->success('Вы успешно вошли в систему!');

			return $this->redirect('/dashboard', navigate: true);

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

    public function render()
    {
        return view('livewire.user.user-auth');
    }
}
