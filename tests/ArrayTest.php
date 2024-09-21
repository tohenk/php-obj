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
use NTLAB\Object\Arr;

class ArrayTest extends TestCase
{
    public function testConvert()
    {
        $arr = new Arr(['key1' => true, 'key2' => 'test', 'key3' => null, 'array' => ['a', 'b\'c', [1, 2]]]);
        $this->assertEquals("[\n    'key1' => true,\n    'key2' => 'test',\n    'key3' => null,\n    'array' => [\n        'a',\n        'b\'c',\n        [\n            1,\n            2\n        ]\n    ]\n]", (string) $arr, 'Array is properly converted as array representation');
        $this->assertEquals("['key1' => true, 'key2' => 'test', 'key3' => null, 'array' => ['a', 'b\'c', [1, 2]]]", (string) $arr->setOption('inline', true), 'Array is properly converted as inlined array representation');
    }
}
