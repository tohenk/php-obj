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
        $this->obj = new TestObj();
    }

    public function testKeys()
    {
        $this->assertTrue($this->obj->isArrayKeysNumeric(['test', 'a']), 'Array keys is numeric if it has no keys');
        $this->assertTrue($this->obj->isArrayKeysNumeric([0 => 'test', 1 => 'a']), 'Array keys is numeric if it keys start from 0');
        $this->assertFalse($this->obj->isArrayKeysNumeric(['a' => 'test', 'b' => 'a']), 'Array keys is not numeric if it has non numeric keys');
        $this->assertFalse($this->obj->isArrayKeysNumeric(['a' => 'test', 1 => 'a']), 'Array keys is not numeric if it has mixed keys');
        $this->assertFalse($this->obj->isArrayKeysNumeric([1 => 'test', 2 => 'a']), 'Array keys is not numeric if it keys does\'t start from 0');
    }

    public function testWrap()
    {
        $s = "[\na\nb\nc\n]";
        $this->assertEquals($s, $this->obj->wrap($s), 'Wrap lines return as is the wrapper option no set');
        $this->obj->setOption('wrapper', '%s');
        $this->assertEquals($s, $this->obj->wrap($s), 'Wrap lines return as is the level is 0');
        $this->assertEquals("[\n    a\n    b\n    c\n]", $this->obj->wrap($s, 1), 'Wrap lines return inner content wrapped');
    }
}

class TestObj extends Obj
{
    public function isArrayKeysNumeric($array)
    {
        return $this->isKeysNumeric($array);
    }

    public function wrap($lines, $lvl = 0)
    {
        return $this->wrapLines($lines, $lvl);
    }
}