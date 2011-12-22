#Template component</h1>
##Introduction</h2>
This standalone template component is a part of Spoon Library.

##Variables
###Syntax

	{$name}

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
below are ok and should work.

	$environment = new Environment();
	$environment->mapModifier('strlen', 'strlen');
	$environment->mapModifier('date', array('MyClass', 'date'));
	$environment->mapModifier('date', array(new MyClass(), 'date'));
	$environment->mapModifier('test', function($value){ return test($value) });

###Arguments
Explain arguments and their 'limitations'.

###Subvariables
Explain subvariables.

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
