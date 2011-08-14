<?php

/*
* This file is part of Spoon Library.
*
* (c) Davy Hellemans <davy@spoon-library.com>
*
* For the full copyright and license information, please view the license
* file that was distributed with this source code.
*/

namespace spoon\template;

/**
 * Every extension is based on this class.
 *
 * @author Davy Hellemans <davy@spoon-library.com>
 */
class Extension
{
	const NAME = 'extension';

	/**
	 * Fetch the name for this extension.
	 *
	 * @return string
	 */
	public function getName()
	{
		return self::NAME;
	}
}
