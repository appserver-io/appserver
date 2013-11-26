Annotations
===========

[![Build Status][]](https://travis-ci.org/herrera-io/php-annotations)

The Annotations library is for tokenizing and converting Doctrine-styled
[annotations][]. Unlike the Doctrine Annotations library, this one does not
require that classes or constants exist, nor are they evaluated.

> The [`DocLexer`][] class from the Doctrine Annotations library is used
> to generate the tokens from the annotated docblocks. This library handles
> the filtering, validation, and conversion of those tokens.

Example
-------

```php
use Herrera\Annotations\Tokenizer;

$tokenizer = new Tokenizer();

$tokens = $tokenizer->parse(
    <<<DOCBLOCK
/**
 * @My\Annotation(
 *     a="string value",
 *     @Nested,
 *     {"a list"},
 *     A_CONSTANT
 * )
 */
DOCBLOCK
);

/*
 * array(
 *     array(DocLexer::T_AT),
 *     array(DocLexer::T_IDENTIFIER, 'My\\Annotation'),
 *     array(DocLexer::T_OPEN_PARENTHESIS),
 *     array(DocLexer::T_IDENTIFIER, 'a'),
 *     array(DocLexer::T_EQUALS),
 *     array(DocLexer::T_STRING, 'string value'),
 *     array(DocLexer::T_COMMA),
 *     array(DocLexer::T_AT),
 *     array(DocLexer::T_IDENTIFIER, 'Nested'),
 *     array(DocLexer::T_OPEN_CURLY_BRACES),
 *     array(DocLexer::T_STRING, 'a list'),
 *     array(DocLexer::T_COMMA),
 *     array(DocLexer::T_IDENTIFIER, 'A_CONSTANT'),
 *     array(DocLexer::T_CLOSE_CURLY_BRACES),
 *     array(DocLexer::T_CLOSE_PARENTHESIS)
 * )
 */
```

Installation
------------

Add it as a [Composer][] dependency:

```
$ composer require herrera-io/annotations=~1.0
```

Tokenizing
----------

To tokenize a docblock comment, you will first need to create an instance of
`Tokenizer`. The object can be re-used to parse as many docblocks as needed:

```php
use Herrera\Annotations\Tokenizer;

$tokenizer = new Tokenizer();

$tokenizer->ignore(
    array(
        'author',
        'package'
    )
);

$aliases = array('ORM' => 'Doctrine\\ORM\\Mapping');

$parsed = $tokenizer->parse($docblock, $aliases);
```

The `ignore()` method allows you to specify a list of annotations to ignore.
By default, none are ignored, so it may be beneficial to register the default
list provided by Doctrine:

```php
array(
  'Annotation', 'Attribute', 'Attributes', 'Required', 'SuppressWarnings',
  'TODO', 'Target', 'abstract', 'access', 'api', 'author', 'category', 'code',
  'codeCoverageIgnore', 'codeCoverageIgnoreEnd', 'codeCoverageIgnoreStart',
  'copyright', 'deprec', 'deprecated', 'endcode', 'example', 'exception',
  'filesource', 'final', 'fixme', 'global', 'ignore', 'ingroup', 'inheritDoc',
  'inheritdoc', 'internal', 'license', 'link', 'magic', 'method', 'name',
  'override', 'package', 'package_version', 'param', 'private', 'property',
  'return', 'see', 'since', 'static', 'staticVar', 'staticvar', 'subpackage',
  'throw', 'throws', 'todo', 'tutorial', 'usedby', 'uses', 'var', 'version',
)
```

The `$aliases` argument allows you to specify a list of aliases that may
have been used for the name of the annotation. For example. it is common
practice to shorten the name of Doctrine ORM annotations using the following:

```php
use Doctrine\ORM\Mapping as ORM;
```

The `$aliases` example demonstrates how only the `ORM` namespace alias will
be mapped to `Doctrine\ORM\Mapping`. Additional aliases can be specified at
the same time:

```php
$aliases = array(
    'Assert' => 'Symfony\Component\Validator\Constraints',
    'ORM' => 'Doctrine\ORM\Mapping',
    'Route' => 'Sensio\Bundle\FrameworkExtraBundle\Configuration\Route',
);
```

The value of `$parsed` is an array of tokens. Each token will contain the
token's numeric identifier, followed by the value parsed from the docblock
(if applicable).

> You can find a reference of the [token identifiers here][].

This example docblock:

```php
/**
 * @author Some Author <some@author.com>
 *
 * @package MyPackage
 *
 * @ORM\Column(name="MyColumn")
 */
```

will yield the following tokens in `$tokens`:

```php
$parsed = array(
    array(DocLexer::T_AT),
    array(DocLexer::T_IDENTIFIER, 'Doctrine\\ORM\\Mapping\\Column'),
    array(DocLexer::T_OPEN_PARENTHESIS),
    array(DocLexer::T_IDENTIFIER, 'name'),
    array(DocLexer::T_EQUALS),
    array(DocLexer::T_STRING, 'MyColumn'),
    array(DocLexer::T_CLOSE_PARENTHESIS)
);
```

Converting
----------

Once you have parsed a docblock for its tokens, you may find the need to convert
the list of tokens into another format. Before I cover the available converters,
I need to show you how to create an instance of `Tokens` and `Sequence` which
are consumed by the converters.

### Tokens and Sequence

Converters use either the `Tokens` or `Sequence` class when converting a list
of tokens into an alternative format. The `Tokens` class acts like an array,
but it will also validate the tokens as they are being used. The `Sequence`
class is an extension of `Tokens`, but it also validates the order in which
the tokens are used.

The converters only require that you use `Tokens`, but they are compatible
with the `Sequence` class as well. The only time you may find need for the
`Sequence` class is for debugging annotation issues, or if you are accepting
tokens from anything besides the `Tokenizer` class.

Creating an instance of either class is very simple:

```php
use Herrera\Annotations\Sequence;
use Herrera\Annotations\Tokens;

$tokens = new Tokens($parsed);
$sequence = new Sequence($parsed);
$sequence = new Sequence($tokens); // also accepts a Tokens object
```

### To Array

An instance of `ToArray` is used to convert tokens to a simple array.

```php
use Herrera\Annotations\Convert\ToArray;

$toArray = new ToArray();

$array = $toArray->convert($tokens);
```

The value of `$array` is an array of objects. Each object represents a single
annotation in the docblock. Each object will have two properties: `name` and
`values`. Any values contained in `()` will be in `values`, including nested
annotations.

The following example:

```php
$array = $toArray->convert(
    new Tokens (
        $tokenizer->parse(
        <<<DOCBLOCK
/**
 * @Annotation\A("Just a simple value.")
 * @Annotation\B(
 *     name="SomeName",
 *     nested=@Annotation(),
 *     {
 *         "an array",
 *         {
 *             "within an array"
 *         }
 *     }
 * )
 */
DOCBLOCK
        )
    )
);
```

will result with the following array:

```php
$array = array(
    (object) array(
        'name' => 'Annotation\\A',
        'values' => array(
            'Just a simple value.'
        )
    ),
    (object) array(
        'name' => 'Annotations\\B',
        'values' => array(
            'name' => 'SomeName',
            'nested' => (object) array(
                'name' => 'Annotation',
                'values' => array()
            ),
            array(
                'an array',
                array(
                    'within an array'
                )
            )
        )
    ),
);

echo $array[0]->name;  // "Annotation\A"
echo $array[0]->values[0]; // "Just a simple value."
echo $array[1]->values['nested']->name; // "Annotation"
```

### To String

An instance of `ToString` is used to convert tokens to their string
representation.

```php
use Herrera\Annotations\Convert\ToString;

$toString = new ToString();

$string = $toString->convert($tokens);
```

Using this example:

```php
$string = $toString->convert(
    new Tokens(
        $tokenizer->parse(
        <<<DOCBLOCK
/**
 * @Annotation\A("Just a simple value.")
 * @Annotation\B(
 *     name="SomeName",
 *     nested=@Annotation(),
 *     {
 *         "an array",
 *         {
 *             "within an array"
 *         }
 *     }
 * )
 */
DOCBLOCK
        )
    )
);
```

The result will be similar, but without any of the formatting:

```php
$string = <<<STRING
@Annotation\A("Just a simple value.")
@Annotation\B(name="SomeName",nested=@Annotation(),{"an array",{"within an array"}})';
STRING;
```

While formatting is supported by the string converter, it is very limited in
the number of options it provides:

- `setBreakChar($char)` &mdash; Sets the line break character. (default: `\n`)
- `setIndentChar($char)` &mdash; Sets the indentation character. (default: ` ` (space))
- `setIndentSize($size)` &mdash; Sets the indentation size. (default: `0` (zero))
- `useColonSpace($bool)` &mdash; Toggles whether a space should be added after a colon that is used for assignment. (default: `false`) (example: `@Name({key: "value"})`)

With a minor modification:

```php
$toString->setIndentSize(4);
```

We can get a formatted string returned back to us:

```php
$string = <<<STRUNG
@Annotation\A(
    "Just a simple value."
)
@Annotation\B(
    name="SomeName",
    nested=@Annotation(),
    {
        "an array",
        {
            "within an array"
        }
    }
)
STRUNG;
```

### To XML

An instance of `ToXml` is used to convert tokens to an XML document.

```php
use Herrera\Annotations\Convert\ToXml;

$toXml = new ToXml();

$doc = $toXml->convert($tokens);
```

Using this example:

```php
$doc = $toXml->convert(
    new Tokens(
        $tokenizer->parse(
        <<<DOCBLOCK
/**
 * @Annotation\A("Just a simple value.")
 * @Annotation\B(
 *     name="SomeName",
 *     nested=@Annotation(),
 *     {
 *         "an array",
 *         {
 *             "within an array"
 *         }
 *     }
 * )
 */
DOCBLOCK
        )
    )
);

echo $doc->saveXML();
```

will result in the following XML:

```xml
<?xml version="1.0"?>
<annotations>
  <annotation name="Annotation\A">
    <value type="string">Just a simple value.</value>
  </annotation>
  <annotation name="Annotation\B">
    <value key="name" type="string">SomeName</value>
    <annotation key="nested" name="Annotation"/>
    <values>
      <value type="string">an array</value>
      <values>
        <value type="string">within an array</value>
      </values>
    </values>
  </annotation>
</annotations>

```

You can also validate annotation XML using `ToXml::validate($input)`, where
`$input` can be an XML string or an instance of `DOMDocument`. If you only
 need access to the XML schema, you can get the file path using the class
 constant `ToXml::SCHEMA`.

 > While you may be able to get the schema path using the
 > `HERRERA_ANNOTATIONS_SCHEMA` constant, I don't recommend it. It isn't
 > available until the ToXml class is loaded, and the the name is not
 > guaranteed to be consistent.

[`DocLexer`]: https://github.com/doctrine/annotations/blob/master/lib/Doctrine/Common/Annotations/DocLexer.php
[Build Status]: https://travis-ci.org/herrera-io/php-annotations.png?branch=master
[Annotations]: http://docs.doctrine-project.org/projects/doctrine-common/en/latest/reference/annotations.html
[Composer]: http://getcomposer.org/
[token identifiers here]: https://github.com/doctrine/annotations/blob/master/lib/Doctrine/Common/Annotations/DocLexer.php#L35
