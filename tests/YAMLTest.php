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
use NTLAB\Object\YAML;

class YAMLTest extends TestCase
{
    public function testConvert()
    {
        $yaml = YAML::create(['key1' => true, 'key2' => 'test', 'key3' => null, 'array' => ['a', 'b', 1], 'test' => ['a' => 'value1', 'b' => 'value2'], 'x' => [['y'], ['z']]]);
        $this->assertEquals("key1: true\nkey2: test\nkey3: ~\narray: [a, b, 1]\ntest:\n    a: value1\n    b: value2\nx:\n    - [y]\n    - [z]", (string) $yaml, 'Array is properly converted as YAML');
        $this->assertEquals("key1: true\nkey2: test\nkey3: ~\narray: [a, b, 1]\ntest: {a: value1, b: value2}\nx:\n    - [y]\n    - [z]", (string) $yaml->setOption('inline', true), 'Array is properly converted as inlined YAML');
        $yaml = YAML::create(['a' => 'One, two, three', 'b' => 'a: b', 'c' => '@something']);
        $this->assertEquals("a: 'One, two, three'\nb: 'a: b'\nc: '@something'", (string) $yaml, 'YAML specials value properly converted');
    }
}
