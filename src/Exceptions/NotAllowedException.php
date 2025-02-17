<?php
/**
 * @package EvolutionScript
 * @author: EvolutionScript S.A.C.
 * @Copyright (c) 2010 - 2020, EvolutionScript.com
 * @link http://www.evolutionscript.com
 */

namespace EvolutionPHP\Routing\Exceptions;

class NotAllowedException extends \Exception
{
	public function __construct($message = "", $code = 0, \Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}
}