<?php

/*
 * The MIT License
 *
 * Copyright (c) 2024 Toha <tohenk@yahoo.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace NTLAB\Object\Test;

use PHPUnit\Framework\TestCase;
use NTLAB\Object\Obj;

class ObjectTest extends TestCase
{
    protected $obj;

    protected function setUp(): void
    {
        $this->obj = TestObj::create("first\nsecond\n\nthird");
    }

    public function testIsKeysNumeric()
    {
        $this->assertTrue($this->obj->isKeysNumeric(['test', 'a']), 'Array keys is numeric if it has no keys');
        $this->assertTrue($this->obj->isKeysNumeric([0 => 'test', 1 => 'a']), 'Array keys is numeric if it keys start from 0');
        $this->assertFalse($this->obj->isKeysNumeric(['a' => 'test', 'b' => 'a']), 'Array keys is not numeric if it has non numeric keys');
        $this->assertFalse($this->obj->isKeysNumeric(['a' => 'test', 1 => 'a']), 'Array keys is not numeric if it has mixed keys');
        $this->assertFalse($this->obj->isKeysNumeric([1 => 'test', 2 => 'a']), 'Array keys is not numeric if it keys does\'t start from 0');
    }

    public function testQuote()
    {
        $s = 'It\'s "quoted" text';
        $this->assertEquals('\'It\\\'s "quoted" text\'', $this->obj->quote($s), 'Quoted string in single quote');
        $this->assertEquals('"It\'s \\"quoted\\" text"', $this->obj->quote($s, TestObj::DOUBLE_QUOTE), 'Quoted string in double quote');
        $this->assertEquals('`It\'s "quoted" text`', $this->obj->quote($s, '`'), 'Quoted string in custom quote');
    }

    public function testWrapLines()
    {
        $s = "[\na\nb\nc\n]";
        $this->assertEquals($s, $this->obj->wrapLines($s), 'Wrap lines return as is the wrapper option no set');
        $this->assertEquals($s, $this->obj->wrapLines($s), 'Wrap lines return as is the level is 0');
        $this->assertEquals("[\n    a\n    b\n    c\n]", $this->obj->wrapLines($s, 1), 'Wrap lines return inner content wrapped');
        $whitespace = "[\none\n    \n\ntwo\n]";
        $this->assertEquals("[\n    one\n        \n\n    two\n]", $this->obj->wrapLines($whitespace, 1), 'Wrap lines return inner content wrapped');
    }

    public function testJoinLines()
    {
        $a = ['a', 'b', 'c'];
        $this->assertEquals("a,\nb,\nc", $this->obj->joinLines($a), 'Join lines using default delimiter and EOL');
        $this->assertEquals("a, b, c", $this->obj->joinLines($a, true), 'Join lines using default delimiter and inlined');
        $this->assertEquals("a;\nb;\nc", $this->obj->joinLines($a, ';'), 'Join lines using semicolon delimiter and EOL');
        $this->assertEquals("a; b; c", $this->obj->joinLines($a, ';', true), 'Join lines using semicolon delimiter and inlined');
    }

    public function testLevel()
    {
        $this->obj->setOption('level', 1);
        $this->assertEquals("    first\n    second\n\n    third", (string) $this->obj, 'Output is leveled');
    }
}

class TestObj extends Obj
{
    public function isKeysNumeric($array)
    {
        return parent::isKeysNumeric($array);
    }

    public function quote($str, $quote = self::SINGLE_QUOTE)
    {
        return parent::quote($str, $quote);
    }

    public function wrapLines($lines, $level = 0, $flags = self::WRAP_SKIP_FIRST | self::WRAP_SKIP_LAST)
    {
        return parent::wrapLines($lines, $level, $flags);
    }

    public function joinLines($lines, $delimiter = ',', $inline = null)
    {
        return parent::joinLines($lines, $delimiter, $inline);
    }
}