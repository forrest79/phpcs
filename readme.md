# Forrest79 Coding Standard for PHP

[![Latest Stable Version](https://poser.pugx.org/forrest79/phpcs/v)](//packagist.org/packages/forrest79/phpcs)
[![Monthly Downloads](https://poser.pugx.org/forrest79/phpcs/d/monthly)](//packagist.org/packages/forrest79/phpcs)
[![License](https://poser.pugx.org/forrest79/phpcs/license)](//packagist.org/packages/forrest79/phpcs)
[![Build](https://github.com/forrest79/phpcs/actions/workflows/build.yml/badge.svg?branch=master)](https://github.com/forrest79/phpcs/actions/workflows/build.yml)

> Based on (no more developed) https://github.com/consistence/coding-standard and work of https://github.com/klapuch. Thanks!

## Installation

The recommended way to install Forrest79 PHP Coding Standard is through Composer:

```sh
composer require --dev forrest79/phpcs
```

Forrest79 PHP Coding Standard requires PHP 8.0.


## How to use it:

- Create your CS XML and include this Forrest79CodingStandard:

```xml
<?xml version="1.0"?>
<ruleset name="MyOwnCS">
	<rule ref="./Forrest79CodingStandard/ruleset.xml"/>
</ruleset>
```

- Or you can use version with fully qualified global functions (`\` prefix before global functions i.e. `\is_array($x)`) and constants (`\` prefix before global constants i.e. `\FILE_APPEND`):

```xml
<?xml version="1.0"?>
<ruleset name="MyOwnCS">
	<rule ref="./Forrest79CodingStandard/ruleset-fully-qualified-global.xml"/>
</ruleset>
```

- You can use it and ignore some rules.

```xml
<?xml version="1.0"?>
<ruleset name="MyOwnCS">
	<rule ref="./Forrest79CodingStandard/ruleset.xml">
		<exclude name="Forrest79CodingStandard.Exceptions.ExceptionDeclaration.NotChainable"/>
	</rule>
</ruleset>
```

- Or just concrete files.

```xml
<rule ref="Forrest79CodingStandard.Exceptions.ExceptionDeclaration.NotChainable">
    <exclude-pattern>tests/Sniffs/TestCase.php</exclude-pattern>
</rule>
```

## PSR-4 settings

- Recommend is to proper set PSR-4 via these settings:

```xml
<rule ref="SlevomatCodingStandard.Files.TypeNameMatchesFileName">
    <properties>
        <property name="rootNamespaces" type="array" value="
            apps/home/app/src=>Home\App,
            apps/home/tests/src=>Home\Tests,
            packages/internals/src=>Forrest79,
            tools/src=>Tools,
        "/>
    </properties>
</rule>
```

   - key is directory, value is namespace

## Custom sniffs

### Exceptions\ExceptionDeclarationSniff

#### Forrest79CodingStandard.Exceptions.ExceptionDeclarationSniff.NotEndingWithException

Or exceptions should have `Exception` suffix.

#### Forrest79CodingStandard.Exceptions.ExceptionDeclarationSniff.NotChainable

Exceptions should be chainable, last constructor argument must be `\Throwable`.

#### Forrest79CodingStandard.Exceptions.ExceptionDeclarationSniff.IncorrectExceptionDirectory

All exceptions should be in `Exceptions` subdirectory. You can change subdirectory name via settings:

```xml
<rule ref="Forrest79CodingStandard.Exceptions.ExceptionDeclaration">
    <properties>
        <property name="exceptionsDirectoryName" type="string" value="exceptions"/>
    </properties>
</rule>
```

### Methods\MethodStructureSniff

#### Forrest79CodingStandard.Methods.MethodStructureSniff.AlphabeticalOrder

Check if class has public methods in alphabetical order. Set files via settings:

```xml
<rule ref="Forrest79CodingStandard.Methods.MethodStructure">
    <properties>
        <property name="checkFiles" type="array" value="
            apps/home/app/src/Configurator.php,
        "/>
    </properties>
</rule>
```

### NamingConventions\MethodStructureSniff

#### Forrest79CodingStandard.NamingConventions.ValidVariableNameSniff.NotCamelCaps

Check camel caps variable names.

### Nette\FinalInjectMethodSniff

#### Forrest79CodingStandard.Nette.FinalInjectMethodSniff.MissingFinal

For Nette Framework. If some presenter or other object uses inject functionality, class should be `final` or `inject` methods should be `final`.

### Nette\ParentCallSniff

#### Forrest79CodingStandard.Nette.ParentCallSniff.MissingParent

For Nette Framework. In presenters, all `beforeRender` methods should call their parent.

### Nette\PresenterMethodsSniff

#### Forrest79CodingStandard.Nette.PresenterMethodsSniff.NotProtected

For Nette Framework. In presenters, all `startup`, `beforeRender` and `createComponent` methods should be `protected`.


## General naming conventions

- Avoid abbreviations, use them only if long name would be less readable.
- For 2-letter shortcuts use `UPPERCASE`, for longer `PascalCase`.

```php
<?php

use Foo\IP\Bar;
use Foo\Php\Bar;
use Foo\UI\Bar;
use Foo\Xml\Bar;
```

## General formatting conventions

- Tab indentation is used.
- Files end with a single blank line `\n`.
- Unix-style (LF) line endings `\n` are used.
- If there is a list of information, where ordering has no semantic meaning, the list is sorted alphabetically.
  - Sorting concatenated words (e.g. `PascalCase`) takes into account original words:

```php
<?php

use LogAware;
use LogFactory;
use LogLevel;
use LogStandard;
use LogableTrait;
use LoggerInterface;
```

## PHP files

- Contains only PHP code (no inline HTML etc.).
- File does not have the closing tag `?>`.
- There are no characters (including BOM) before the PHP opening tag.
- Long opening tags are used (always `<?php`, never `<?`).
- There is one empty line after the line with the open tag.
- File either declares new symbols (classes, functions, constants, etc.) and causes no other side effects, or executes logic with side effects, but should not do both.
- Uses strict typing by enabling `declare(strict_types = 1);`.
   - This declaration is placed right behind the opening tag.
   - There is no space on each side of the `=` operator.

```php
<?php declare(strict_types=1);

namespace Forrest79;
```

## Strings

- Common strings are written using apostrophes (`'`). Only strings containing control characters (such as `\n`) may use double quotes (`"`).
- For concatenation of mixed strings and variables `sprintf()` is used. If in-place strings are not needed concatenation is done with only concatenation operator (`.`).
   - `.` is surrounded by one space on each side, unless it is on the beginning of the line.
   - Strings do not contain variables (`"Hello $name!"`).

```php
<?php

sprintf('%s/%s', $dir, $fileName);

// vs

$foo . $bar;
```

```sql
'SELECT `id`, `name` FROM `people`'
. 'WHERE `role` = 1'
. 'ORDER BY `name` ASC';
```

## Arrays

- Short array syntax is used (`[`, `]`) instead of the `array()` language construct.

## Namespaces

- Namespaces are written in `PascalCase`.
- Each file contains only one `namespace` declaration applied to the whole file.
   - Before the line with `namespace` there is one empty line.
- Types from other namespaces are imported with `use`.
   - `use` declarations are separated from `namespace` declaration with one empty line.
   - There is only one type imported per `use` declaration (one import per line).
   - `use` declarations are sorted alphabetically.
   - `use` declarations never begin with backslash (`\`).

```php
<?php

namespace Forrest79;

use Forrest79\Bar;
use Forrest79\Foo;

use DateTime;

use Lorem\Amet;
use Lorem\Ipsum\Dolor\Foo as DolorFoo;
use Lorem\Sit;

use ReflectionClass;
use ReflectionMethod;
```

## Types

- Type names are written in `PascalCase`.
- Type names are nouns.
- Types are placed in namespaces (not global space).
- Opening brace and closing brace of type are always on a separate line.
- All parts of types are indented with one tab.
- Only one type per file is defined, name of this file has the same name as the type.
- Multiple types referenced in `implements` are separated with comma and one space and are ordered alphabetically.
- Types referenced in code are always referenced using `Foo::class` syntax, never using a string.
- If the referenced type in static access is the "current" one, `self`/`static` is used instead of type name.
- If type can be null, always use `|null` not `?` - `string|null` instead of `?string`.

### Interfaces

- Interfaces are never prefixed with `I`.

### Scalar types

- Short type names are used in code (`int`, `bool`). This also applies to PHP functions which offer both variants.

```php
<?php

if (!is_int($foo)) {
	return (int) $foo;
}
```
- `float` is always used instead of `double` or `real`. This also applies to PHP functions which offer both variants.

## Variables

- Variable names are written in `camelCase`.
- `global` keyword is never used to declare global variables.

## Properties

- All variables rules apply.
- All properties have explicitly declared visibility with `private`, `protected` or `public`.
- `var` is never used.
- Only one property is declared per statement.

## Constants

- Constant names are written in `UPPER_CASE`.
- Constants are defined only inside classes using `const`, global constants are never defined.
- Constants have explicitly declared visibility with `private`, `protected` or `public`.
- If there are more constants, that "belong together", empty lines between them may be omitted.

```php
<?php

class Foo
{
	const FOO = 'foo';

	const VISIBILITY_PRIVATE = 'private';
	const VISIBILITY_PROTECTED = 'protected';
	const VISIBILITY_PUBLIC = 'public';

}
```

## Functions

- Function names are written in `camelCase`.
   - Calls to built-in PHP functions are exception to this rule and are written in `snake_case`.
- There is no space between the function name and the opening parenthesis.
- Opening brace and closing brace of type are always on separate line.
   - Exception: anonymous functions.
- Global functions are never declared, they should be defined inside a (static) class.
- Named functions (not anonymous) are never declared inside other functions.

### Argument list

- There should be type hint defined whenever possible (including scalar type hints).
   - Nullable types (`string|null`) are used to allow passing null to a type hinted argument. 
- There is no space after the opening parenthesis, and there is no space before the closing parenthesis.
- Arguments both in function declaration and in function call are separated with comma, followed by one space (`, `).

```php
<?php

class X
{

	public function __construct(Foo $foo, string $string)
	{
		// ...
	}

}
```

- Function declaration and call with arguments on multiple lines:
   - There is only one argument per line.

```php
<?php

class X
{

	public function __construct(
		Foo $foo,
		string $string,
	)
	{
		// ...
	}

}

new X(
	$foo,
	$string,
);
```

- Default argument values are used only when needed to either express optional argument (only at the end of the list) or to allow passing null to a type hinted argument.
   - Nullable types (`string|null`) are used to allow passing null to a type hinted argument and therefore default arguments are used only for optional arguments.
   - For scalar arguments default arguments are used only for optional arguments, not to allow passing nulls (see detailed example below).

```php
<?php

class X
{

	/**
	 * @param \Foo $a required type argument
	 * @param \Foo|null $b required argument, but nullable type needed
	 * @param string $c required scalar argument
	 * @param string|null $d required argument with nullable scalar
	 * @param string $e optional nullable scalar argument
	 * @param string|null $f optional nullable scalar argument
	 */
	public function __construct(
		Foo $a,
		Foo|null $b,
		string $c,
		string|null $d,
		string $e = '',
		string|null $f = null
	)
	{
		// ...
	}

}
```

- Variadic argument is written in this format: `@param \Foo ...$foo`.

### Return type

- There should be type hint defined whenever possible (including scalar type hints).
   - Nullable types (`string|null`) are used to allow returning null.
- There is no space after the closing parenthesis, colon immediately follows and then there is one space between the colon and the type.

```php
<?php

class X
{

	public function getFoo(): Foo
	{
		// ...
	}

}
```

- When there is nothing to return, `void` return type must be specified.

```php
<?php

class X
{

	public function process(Foo $foo): void
	{
		$foo->bar();
	}

}
```

- When method interrupt script, `never` return type must be specified.

```php
<?php

class X
{

	public function process(Foo $foo): never
	{
		exit(1);
	}

}
```

### Anonymous functions

- There is a space between the `function` keyword and the opening parenthesis.
- Opening brace is NOT placed on the next line.
- There is one space before and after the `use` keyword.

```php
<?php

array_walk($foo, function (Item $item) use ($bar): string {
	// ...
});
```

### Methods

- All methods have explicitly declared visibility with `private`, `protected` or `public`.
- Order of keywords in declaration:
   1. `final`/`abstract`,
   2. `private`/`protected`/`public`,
   3. `static`
- Constructor is always defined with `__construct` name, never using the old PHP behavior - with name same as class name.
- There is one new line before and after each function (so two new lines between two functions).

```php
<?php

class X
{

	final public static function foo(): void
	{
		// ...
	}


	abstract public static function bar(): void
	{
		// ...
	}

}
```

## Control structures

- Conditional statement is surrounded by parentheses.
   - There is no space after/before parentheses inside the statement.
   - There is one space before/after parentheses around the statement.
- Opening brace is placed on the same line as the conditional statement.
- `else if` is used instead of `elseif`.
- `case` statements in `switch` are indented with one tab, and their content on following lines again with another tab.
- `case` statements in `switch` end with a colon `:`.

```php
<?php

if ($foo) {
	// ...
}

if (
	$foo
	&& $bar
) {
	// ...
}

switch ($foo) {
	case 1:
	case 2:
		// ...
		break;
	default:
		// ...

}
```

- In `switch`, there must be a comment such as `// no break` when fall-through is intentional in a non-empty case body.
- Empty bodies of control structures are forbidden.
   - Exception is `catch`, but there must be a comment explaining situation.

## Expressions

- After all operators, there is one space. Before operators, there is one space too, unless it is on the beginning of a line.
- Logical operators `&&` and `||` are always used instead of `and` and `or`.
- All keywords are lowercase, exceptions are `true`, `false` and `null`.
- Strict comparisons are used by default (`===`), if there is need for `==`, usually a comment should be given explaining situation.
   - Magic PHP type conversions should be avoided - WRONG: `($foo)`, CORRECT: `($foo !== null)` - only expressions already containing boolean values should be written in `($foo)` form.
   - The same applies for `empty` function, so it is forbidden.
- [Yoda conditions](http://en.wikipedia.org/wiki/Yoda_conditions) should not be used.
- If expression needs to be written on multiple lines, operators belong on the beginning of the line.

```php
<?php

if (
	($lorem >= 3 && $lorem <= 5)
	|| $ipsum !== null
) {
	// ...
}
```

- There is always only one statement per line.
- One blank line may be used to separate other statements.
- If there are multiple method calls in a row, and it is needed to write this on multiple lines, all the method calls are indented (including the first one).

```php
<?php

$lorem
	->ipsum()
	->dolor()
	->sit()
	->amet();
```

- Parentheses in `new` statements should be always present, even if there are no arguments for constructor.

```php
<?php

new Foo();
```

- There is one space after type cast and no space inside the parentheses.
  - `(binary)` and `(unset)` casts are forbidden.
- For increments and decrements respective operators `++`/`--` are used instead of "manual" addition/subtraction.
- All static symbols should be accessed only with `::`, never using `$this`.
- `echo`, `print`, ... allowing both `echo('...')` and `echo ''` syntax are always used without parentheses and with one space after the keyword.

## Closures and callables

- Closure is preferred to use instead of a callable (callable might be required while implementing a third party interface though).
- Closures are invoked using `$closure()` instead of using functions.
   - `call_user_func` should never be needed.
   - `call_user_func_array` is not needed since PHP 5.6 - argument unpacking was introduced.

```php
<?php

function foo($foo, Closure $callback): void
{
	// ...
	$callback($bar);
	// ...
}

foo('foo', function (Bar $bar): void {
	// ...
});
```

```php
<?php

function foo($foo, Closure $callback): void
{
	// ...
	$callback(...$barArray);
	// ...
}

foo('foo', function (Bar ...$bars): void {
	// ...
});
```

## Exceptions

- Type name always ends with Exception.
- Exceptions are placed in a separate directory called `Exceptions`.
- Class name describes the use-case and should be very specific.
- Inheritance is used for implementation purposes (not for creating hierarchies) - such as `Forrest79\PhpException`, where `$code` argument is skipped.
- Constructor requires only arguments, which are needed, the rest of the message is composed in the constructor.
   - All exceptions should support exceptions chaining (allow optional `\Throwable` as last argument).
   - Arguments should be stored in private properties and available via public methods, so that exception handling may use this data.

```php
<?php

namespace Forrest79\Foo;

use Forrest79;

class LoremException extends Forrest79\PhpException
{
	private strinf $lorem;


	public function __construct(string $lorem, \Throwable $previous = null)
	{
		parent::__construct(sprintf('%s ipsum dolor sit amet', $lorem), $previous);
		$this->lorem = $lorem;
	}


	public function getLorem(): string
	{
		return $this->lorem;
	}

}
```

##Commenting

- `//` inline style comments are used, never `#`.
- Inline comment has a space between `//` and the text.
- If there is no explicit need to write something, it should be omitted - well named classes, variables, methods and arguments should be preferred.
- "Commented out" code is never present.

## PHPDoc

Structure for types and methods:

```php
<?php

/**
 * Short description - one line (optional)
 *
 * Long description (optional)
 *
 * Documentation annotations (optional)
 *
 * Code analysis annotations (optional)
 *
 * Application annotations (optional)
 *
 * @param string $foo only if some optional description is needed
 * @param int $bar only if some optional description is needed
 * @return bool only if some optional description is needed
 * @throws \MyException\BarException
 * @throws \MyException\FooException
 */
public function myMethod(string $foo, int $bar): bool;
```

Structure for properties and constants:

```php
<?php

/**
 * Short description - one line (optional)
 *
 * Long description (optional)
 *
 * Documentation annotations (optional)
 *
 * Code analysis annotations (optional)
 *
 * Application annotations (optional)
 *
 * @var string optional description
 */
private $foo;
```

### Annotation blocks

- Different types of annotations are grouped together (separated from other blocks by an empty line).
- See structure above.

### Short+long description

- Optional.
- If there is no explicit need to write something, it should be omitted - well named classes, variables, methods and arguments should be preferred.
- Descriptions which only rephrase method names (etc.) are never written.
- Clear code is better than long explanation.
- Short description has only one line (with no dot at the end).
- Long description may contain example usage.
- In long description formatting may be used (e.g. with HTML).

### Documentation annotations

- Optional.
- PHPDoc's annotations with documentation metadata like `@author`, `@copyright`, `@see`, `@link`, ...

### Code analysis annotations

- Are never user, use [PHPCS-Ignores](https://github.com/forrest79/phpcs-ignores) when needed.

### Application annotations

- Optional.
- Annotations, that have functional significance for the application, such as Symfony, Doctrine and custom annotations.

### Multi-line annotations

- Follows general formatting rules for dealing with separating statements to multiple lines.
- Two spaces are used for indentation.

```php
/**
 * @ManyToMany(targetEntity="Phonenumber")
 * @JoinTable(
 *   name="users_phonenumbers",
 *   joinColumns={@JoinColumn(name="user_id", referencedColumnName="id")},
 *   inverseJoinColumns={@JoinColumn(name="phonenumber_id", referencedColumnName="id", unique=true)},
 * )
 * @Foo
 **/
```

### Allowed types for @param, @return, @var

List of allowed types (long variants are used):

- `int`
- `bool`
- `string`
- `float`
- `resource`
- `null`
- `object`
- `mixed`
- array/collection (see below)
- type (see below)

Multiple different types are separated with `|`.

#### Mixed

- Used when nothing is known about the type.

#### Array/collection

- Written as `array<int, type>`, `array<string, type>` or `list<type>`.
- If the values are of more than one type, then `array<int, mixed>` is used (also if there is no knowledge about the types).
- If associative array is expected (or a Map), in description, there should be description of used format, such as `array<string, string> $names format: lastName(string) => firstName (string)`.
- If there are more nested arrays/collections, this is expressed with more `array<>`, e.g. `array<int, array<string, integer>>` means array of arrays of integers.

#### Type

- FQN with a leading backslash.
- If the referenced type is the "current" one, `self`/`static` is used instead of type name.

```php
<?php

use DateTime;
use DateTimeImmutable;

/**
 * @param DateTimeImmutable $date calendar date
 * @param array<string> $events
 * @param int|null $interval
 * @return DateTime
 */
public function myMethod(DateTimeImmutable $date, array $events, int $interval = null): DateTime
{
	// ...
}
```

### @param

- Can be omitted when type hint is the same as annotation.
- Annotations are in the same order as defined in the argument list.

```php
<?php

use DateTime;

/**
 * @param string $foo optional description
 * @param int $bar optional description
 * @param DateTime ...$dates optional description
 */
public function myMethod(string $foo, int $bar, DateTime ...$dates)
{
	// ...
}
```

### @return

- If there are no `return` statements in the method, `@return` is not present.
- Can be omitted when type hint is the same as annotation.

### @var

- If the `@var` annotation is the only annotation and there is no long description in the PHPDoc, then one-line format is used:

```php
<?php

/** @var string optional description */
private $foo;
```

- Inline `@var` is used to define types for variables, where the type is not clear.
  - Uses docblock comment `/** ... */`.
  - Type is defined first, followed by variable name.

```php
<?php

/** @var Foo $foo optional description */
$foo = $container->getService('foo');
```

### @throws

- For implemented methods `@throws` is never used.
- `@throws` is only used in interfaces or abstract methods as part of the defining contract.
- `@throws` annotations are sorted alphabetically (according to the exception name).

### Constants

- `@var` is not used for constants (type is defined by its value).
- If there is no need for other annotations or description, then PHPDoc is omitted.
