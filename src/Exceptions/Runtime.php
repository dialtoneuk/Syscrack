<?php

	namespace Framework\Exceptions;

	/**
	 * Lewis Lancaster 2016
	 *
	 * @package Framework\Exceptions
	 */

	use Error;

	/**
	 * Class ApplicationExeption
	 *
	 * @package Framework\Exceptions
	 */
	class ApplicationException extends Error
	{

	}

	;

	/**
	 * Class DatabaseExeption
	 *
	 * @package Framework\Exceptions
	 */
	class DatabaseException extends Error
	{

	}

	;

	/**
	 * Class ViewExeption
	 *
	 * @package Framework\Exceptions
	 */
	class ViewException extends Error
	{

	}

	;

	/**
	 * Class ApiExeption
	 *
	 * @package Framework\Exceptions
	 */
	class ApiException extends Error
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
	}

	;

	/**
	 * Class ConsoleExeption
	 *
	 * @package Framework\Exceptions
	 */
	class ConsoleException extends Error
	{

	}

	;

	/**
	 * Class SyscrackException
	 *
	 * @package Framework\Exceptions
	 */
	class SyscrackException extends Error
	{

	}

	;

	/**
	 * Class SessionException
	 *
	 * @package Framework\Exceptions
	 */
	class SessionException extends Error
	{

	}

	;

	/**
	 * Class LoginException
	 *
	 * @package Framework\Exceptions
	 */
	class LoginException extends Error
	{

	}

	;