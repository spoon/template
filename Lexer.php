<?php

/*
* This file is part of Spoon Library.
*
* (c) Davy Hellemans <davy@spoon-library.com>
*
* For the full copyright and license information, please view the license
* file that was distributed with this source code.
*/

namespace Spoon\Template;
use Spoon\Template\Environment;

/**
 * Creates an array of token objects from source code.
 *
 * @author Davy Hellemans <davy@spoon-library.com>
 */
class Lexer
{
	/**
	 * Regexes used for lexing the template source code.
	 *
	 * @var string
	 */
	const REGEX_NAME = '/\$?[A-Za-z_][A-Za-z0-9_]*/A';
	const REGEX_NUMBER = '/[0-9]+(?:\.[0-9]+)?/A';
	const REGEX_STRING = '/"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"|\'([^\'\\\\]*(?:\\\\.[^\'\\\\]*)*)\'/As';

	/**
	 * List of punctuation characters.
	 *
	 * @var string
	 */
	const PUNCTUATION = '()[]{}?:.,|';

	/**
	 * Lexing states, based on the template source code.
	 *
	 * @var string
	 */
	const STATE_BLOCK = 'block';
	const STATE_DATA = 'data';
	const STATE_VAR = 'var';

	/**
	 * Keeps track of the opened/closed brackets in the source code.
	 *
	 * @var array
	 */
	protected $brackets;

	/**
	 * Current cursor position.
	 *
	 * @var int
	 */
	protected $cursor;

	/**
	 * Total number of characters in the source.
	 *
	 * @var int
	 */
	protected $end;

	/**
	 * The template environment.
	 *
	 * @var Environment
	 */
	protected $environment;

	/**
	 * Optional filename for this source you want to tokenize.
	 *
	 * @var string
	 */
	protected $filename;

	/**
	 * Current line number.
	 *
	 * @var int
	 */
	protected $line;

	/**
	 * The template source code to lex.
	 *
	 * @var string
	 */
	protected $source;

	/**
	 * The current lexing state.
	 *
	 * @var string
	 */
	protected $state;

	/**
	 * The type of start/end tags used for lexing.
	 *
	 * @var array
	 */
	protected $tags = array(
		'comment' => array('{*', '*}'),
		'block' => array('{', '}'),
		'variable' => array('{$', '}')
	);

	/**
	 * The list of all tokens found in the template source code.
	 *
	 * @var array
	 */
	protected $tokens;

	/**
	 * Constructor.
	 *
	 * @param Environment $environment
	 */
	public function __construct(Environment $environment)
	{
		$this->environment = $environment;
	}

	/**
	 * Adds a new token to the list.
	 *
	 * @param string $type
	 * @param mixed[optional] $value
	 */
	protected function addToken($type, $value = null)
	{
		// text tokens must have a value
		if($type == Token::TEXT && $value == '')
		{
			return;
		}

		$this->tokens[] = new Token($type, $value, $this->line);
	}

	/**
	 * Processes a block.
	 */
	protected function processBlock()
	{
		// matching end comment tag has been found
		if(preg_match('/\s*\}\s*\n?/A', $this->source, $match, null, $this->cursor))
		{
			$this->addToken(Token::BLOCK_END);
			$this->moveCursor($match[0]);
			$this->state = self::STATE_DATA;
		}
		else $this->processExpression();
	}

	/**
	 * Processes a comment.
	 */
	protected function processComment()
	{
		if(!preg_match('/(?:\*\}\s*|\*\})\n?/s', $this->source, $match, PREG_OFFSET_CAPTURE, $this->cursor))
		{
			throw new SyntaxError('Unclosed comment', $this->line, $this->filename);
		}

		$this->moveCursor(
			mb_substr(
				$this->source, $this->cursor,
				$match[0][1] - $this->cursor,
				$this->environment->getCharset()
			) . $match[0][0]
		);
	}

	/**
	 * Processes data.
	 */
	protected function processData()
	{
		$position = $this->end;

		// find comment, variable or block
		foreach(array('comment', 'variable', 'block') as $type)
		{
			$tmpPosition = mb_strpos(
				$this->source,
				$this->tags[$type][0],
				$this->cursor,
				$this->environment->getCharset()
			);

			if($tmpPosition !== false && $tmpPosition < $position)
			{
				$position = $tmpPosition;
				$token = $this->tags[$type][0];
			}
		}

		// no matches found, return the rest of the template as text token
		if($position === $this->end)
		{
			$this->addToken(
				Token::TEXT,
				mb_substr(
					$this->source,
					$this->cursor,
					$this->end,
					$this->environment->getCharset()
				)
			);

			$this->cursor = $this->end;
			return;
		}

		// push the template text first
		$text = mb_substr(
			$this->source,
			$this->cursor,
			$position - $this->cursor,
			$this->environment->getCharset()
		);
		$this->addToken(Token::TEXT, $text);
		$this->moveCursor($text . $token);

		switch($token)
		{
			case '{*':
				$this->processComment();
				break;

			case '{$':
				$this->addToken(Token::VAR_START);
				$this->state = self::STATE_VAR;
				break;

			case '{':
				$this->addToken(Token::BLOCK_START);
				$this->state = self::STATE_BLOCK;
				break;
		}
	}

	/**
	 * Process an expression.
	 */
	protected function processExpression()
	{
		// dit dient om spaties op kaka plaatsen te negeren.
		if(preg_match('/\s+/A', $this->source, $match, null, $this->cursor))
		{
			$this->moveCursor($match[0]);

			// we still expect something, but the source seems to end
			if($this->cursor >= $this->end)
			{
				throw new SyntaxError(
					sprintf('Unexpected end of file: Unclosed "%s"',
						$this->state === self::STATE_BLOCK ? 'block' : 'variable'
					),
					$this->line,
					$this->filename
				);
			}
		}

		/*
		 * Expression regex pattern for:
		 * or, and, ==, !=, <, >, >=, <=, +, -,, *, /, %
		 */
		$pattern = '/and(?=[ ()])|\>\=|\<\=|\!\=|\=\=|or(?=[ ()])|%|\+|\-|\/|\>|\<|~|\*/A';

		// operators
		if(preg_match($pattern, $this->source, $match, null, $this->cursor))
		{
			$this->addToken(Token::OPERATOR, $match[0]);
			$this->moveCursor($match[0]);
		}

		// names
		elseif(preg_match(self::REGEX_NAME, $this->source, $match, null, $this->cursor))
		{
			$this->addToken(Token::NAME, $match[0]);
			$this->moveCursor($match[0]);
		}

		// numbers
		elseif(preg_match(self::REGEX_NUMBER, $this->source, $match, null, $this->cursor))
		{
			$this->addToken(Token::NUMBER, ctype_digit($match[0]) ? (int) $match[0] : (float) $match[0]);
			$this->moveCursor($match[0]);
		}

		// punctuation
		elseif(strpos(self::PUNCTUATION, $this->source[$this->cursor]) !== false)
		{
			// opening bracket
			if(strpos('([{', $this->source[$this->cursor]) !== false)
			{
				$this->brackets[] = array($this->source[$this->cursor], $this->line);
			}

			// closing bracket
			elseif(strpos(')]}', $this->source[$this->cursor]) !== false)
			{
				if(empty($this->brackets))
				{
					throw new SyntaxError(
						sprintf('Unexpected "%s"', $this->source[$this->cursor]), $this->line, $this->filename
					);
				}

				list($expect, $line) = array_pop($this->brackets);
				if($this->source[$this->cursor] != strtr($expect, '([{', ')]}'))
				{
					throw new SyntaxError(
						sprintf('Unclosed "%s"', $expect), $this->line, $this->filename
					);
				}
			}

			$this->addToken(Token::PUNCTUATION, $this->source[$this->cursor]);
			++$this->cursor;
		}

		// strings
		elseif(preg_match(self::REGEX_STRING, $this->source, $match, null, $this->cursor))
		{
			$this->addToken(Token::STRING, stripcslashes(substr($match[0], 1, -1)));
			$this->moveCursor($match[0]);
		}

		// unlexable
		else
		{
			throw new SyntaxError('The source can not be lexed.');
		}
	}

	/**
	 * Process variable.
	 */
	protected function processVariable()
	{
		if(preg_match('/\}/A', $this->source, $match, null, $this->cursor))
		{
			$this->addToken(Token::VAR_END);
			$this->moveCursor($match[0]);
			$this->state = self::STATE_DATA;
		}

		else $this->processExpression();
	}

	/**
	 * Moves the cursor forward based on the string length of the first argument.
	 *
	 * @param string $text
	*/
	protected function moveCursor($text)
	{
		$this->cursor += mb_strlen($text, $this->environment->getCharset());
		$this->line += mb_substr_count($text, "\n", $this->environment->getCharset());
	}

	/**
	 * Returns a stream of tokens found in the source.
	 *
	 * @return array
	 * @param string $source
	 * @param string $filename
	 */
	public function tokenize($source, $filename = null)
	{
		$this->source = str_replace(array("\r\n", "\r"), "\n", $source);
		$this->cursor = 0;
		$this->line = 1;
		$this->end = mb_strlen($this->source, $this->environment->getCharset());
		$this->tokens = array();
		$this->state = self::STATE_DATA;
		$this->brackets = array();
		$this->filename = $filename;

		while($this->cursor < $this->end)
		{
			switch($this->state)
			{
				case self::STATE_DATA:
					$this->processData();
					break;

				case self::STATE_BLOCK:
					$this->processBlock();
					break;

				case self::STATE_VAR:
					$this->processVariable();
					break;
			}
		}

		$this->addToken(Token::EOF);
		return $this->tokens;
	}
}
