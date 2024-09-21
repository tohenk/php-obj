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

namespace NTLAB\Object;

/**
 * Represents PHP object as array.
 *
 * @author Toha <tohenk@yahoo.com>
 */
class Arr extends Obj
{
    /**
     * (non-PHPdoc)
     * @see \NTLAB\Object\Obj::convert()
     */
    public function convert($value, $level = 0)
    {
        if ($value instanceof Arr) {
            $value = (string) $value;
        } elseif (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        } elseif (is_string($value)) {
            $q = '\'';
            $value = $q.str_replace($q, '\\\'', $value).$q;
        } elseif (null === $value) {
            $value = 'null';
        } elseif (is_array($value)) {
            $tmp = [];
            $useKey = !$this->isKeysNumeric($value);
            $multiline = !$this->getOption('inline');
            $eol = $multiline ? "\n" : '';
            foreach ($value as $k => $v) {
                // skip null value
                if (null === $v && $this->getOption('skip_null')) {
                    continue;
                }
                $v = $this->convert($v, 1);
                $tmp[] = $useKey ? sprintf('\'%s\' => %s', $k, $v) : $v;
            }
            $value = $eol.implode($multiline ? ",\n" : ', ', $tmp).$eol;
            $value = sprintf('[%s]', $value);
            if ($multiline) {
                $value = $this->wrapLines($value, 1);
            }
        } else {
            $value = (string) $value;
        }

        return $value;
    }
}
