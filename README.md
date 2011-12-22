#Template component</h1>
##Introduction</h2>
This standalone template component is a part of Spoon Library.

##Variables
###Syntax
The most basic example looks like this:

	{$name}

If {$name} doesn't contain any value, null is returned. It's also possible to use a dot as a separator.

	{$foo.bar}

This has several implications as to what 'bar' actually is.

* check if $foo is an array and 'bar' is an element
* check if $foo is an object and 'bar' is a public property
* check if $foo is an object and 'bar' is a public method
* check if $foo is an object and 'getBar' is a public method

If none of the checks above give any results, null is returned as with simple variables.

###Modifiers
Modifiers are functions you can apply to variables. They can be chained and even contain
subvariables as arguments. They're executed from left to right.

	{$name|dump}
	{$name|uppercase}
	{$name|shuffle}
	{$name|truncate(10)}
	{$name|truncate($length)}

You can map your own functions/methods to modifiers. The only requirement is that they
need to be a valid <a href="http://www.php.net/callback">callback</a>. All the examples
below are ok and should work just fine.

	$environment = new Environment();
	$environment->mapModifier('strlen', 'strlen');
	$environment->mapModifier('date', array('MyClass', 'date'));
	$environment->mapModifier('date', array(new MyClass(), 'date'));
	$environment->mapModifier('test', function($value){ return test($value) });

###Arguments
Arguments can be either strings, integers or subvariables.

	{$name|substring(0, 5}}
	{$name|substring(0, $length)}
	{$name|sprintf('foo')}

As you can see. There's no need to encapsulate the subvariable with brackets.

###Subvariables
As seen in the example above, subvariables can be used in a nifty way. However there are some
restrictions. At this moment it's not possible to apply modifiers to subvariables. I'm planning
to add this in the future.

The same rules apply as with regular variables concerning the chaining of subvariables.

	{$name|sprintf($foo.bar.baz)}

##Comments
Explain comments

##Including templates
The 'include' tag is used to include other templates. The path is always based on the
location of the template wherin the include tag resides. Following examples are ok.

	{include 'template.tpl'}
	{include $template}
	{include $path . '/template.tpl'}
	{include '/home/spoon/' . $directory . '/template.tpl'}

##Control structures
### If else
### For loop
