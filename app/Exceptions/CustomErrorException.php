<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Throwable; // Используйте Throwable для совместимости с Laravel

class CustomErrorException extends Exception
{
	protected string $customMessage;
	protected int $statusCode;
	protected array $data; // Дополнительные данные для ответа

	public function __construct(string $message = "", int $code = 0, array $data = [], Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);
		$this->customMessage = $message;
		$this->statusCode = $code ?: 400; // По умолчанию 400 Bad Request
		$this->data = $data;
	}

	// Метод для рендеринга исключения в HTTP-ответ
	public function render($request)
	{
		return new JsonResponse([
			'status' => 'error',
			'message' => $this->customMessage,
			'code' => $this->statusCode,
			'data' => $this->data,
		], $this->statusCode);
	}
}
