<?php

namespace Herrera\Annotations\Tests\Convert;

use Doctrine\Common\Annotations\DocLexer;
use Herrera\Annotations\Test\TestConvert;
use Herrera\Annotations\Tokens;
use Herrera\PHPUnit\TestCase;

class AbstractConvertTest extends TestCase
{
    public function testConvert()
    {
        $converter = new TestConvert();
        $tokens = new Tokens(
            array(
                array(DocLexer::T_AT),
                array(DocLexer::T_AT),
                array(DocLexer::T_AT),
                array(DocLexer::T_AT),
            )
        );

        $result = $converter->convert($tokens);

        $this->assertEquals(4, $result);
        $this->assertSame($tokens, $converter->tokens);
    }
}
