<?php
namespace Azera\Component\Queryable;

use Closure;

class Expression
{

	const VALID_VAR_REGEX = '(?:[a-zA-Z_][a-zA-Z0-9_]*,?)+';

	/**
	 * Compiled expression to php excutable
	 * @var string
	 */
	private $compiled;

	/**
	 * Expression
	 * @var string
	 */
	private $expression;

	private $multiInput = false;

	public function __construct( $expression )
	{

		$this->expression = $expression;

		if ( is_string($expression) )
			$this->compileFromString( $expression );
		elseif ( $expression instanceof Closure )
			$this->compiled = $expression;

	}

	private function compileFromString( $expression )
	{

		// Extract local variable name
		if ( !preg_match('/^(?<varname>' . static::VALID_VAR_REGEX .')\s*\=\>\s*(?<code>.*)$/', $expression, $out ) )
			if ( !preg_match( '/^'. static::VALID_VAR_REGEX .'/' , $x = trim(current(explode('=>',$expression))) ) )
				throw new InvalidExpressionException( $expression , sprintf('Invalid variable name "%s" in expression "%s"',$x,'%s') );
			else
				throw new InvalidExpressionException( $expression );

		$x = $out['varname']; $code = $out['code'];

		$vars = explode( ',' , $x );

		$result = "";

		if ( count($vars) > 1 )
		{
			$this->multiInput = true;
			foreach ( $vars as $i => $var )
				$result .= "$$var = &\$input[$i];\n";
		} else {
			$var = current($vars);
			$result .= "$$var = &\$input;\n";
		}

		$code = preg_replace('/\b('.implode('|',$vars).')\b/','$\1', $code );

		$result .= "return $code;";

		$this->compiled = $result;

	}

	public function execute( $input )
	{
		if ( $this->multiInput ) $input = func_get_args();
		return $this->compiled instanceof Closure ? call_user_func_array($this->compiled, [ $input ] ) : eval( $this->compiled );
	}

	public function toPHP()
	{
		return $this->compiled;
	}

}
?>