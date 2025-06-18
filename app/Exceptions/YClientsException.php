<?php

namespace App\Exceptions;

use Exception;
use Throwable;

// Или use RuntimeException; в зависимости от вашей логики

/**
 * Class YClientsException
 * @package App\Exceptions
 */
class YClientsException extends Exception
{
	// Опционально: можно добавить кастомные свойства или методы
	public function __construct($message = "", $code = 0, Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}
}
