<?php
namespace Framework\Exceptions;

/**
 * Lewis Lancaster 2016
 *
 * @package Framework\Exceptions
 */

use RuntimeException;

/**
 * Class ApplicationExeption
 *
 * @package Framework\Exceptions
 */

class ApplicationException extends RuntimeException{};

/**
 * Class DatabaseExeption
 *
 * @package Framework\Exceptions
 */

class DatabaseException extends RuntimeException{};

/**
 * Class ViewExeption
 *
 * @package Framework\Exceptions
 */

class ViewException extends RuntimeException{};

/**
 * Class ApiExeption
 *
 * @package Framework\Exceptions
 */

class ApiException extends RuntimeException
{

	public function getArray()
	{

		return array(
			'error' => true,
			'stack' => [
				'message' => $this->getMessage()
			]
		);
	}
};

/**
 * Class ConsoleExeption
 * 
 * @package Framework\Exceptions
 */

class ConsoleException extends RuntimeException{};

/**
 * Class SyscrackException
 *
 * @package Framework\Exceptions
 */

class SyscrackException extends RuntimeException{};

/**
 * Class SessionException
 * 
 * @package Framework\Exceptions
 */

class SessionException extends RuntimeException{};

/**
 * Class LoginException
 *
 * @package Framework\Exceptions
 */

class LoginException extends RuntimeException{};