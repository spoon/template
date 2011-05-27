<?php

namespace spoon\template;

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
