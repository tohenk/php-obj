# A PHP Object Representation

![Build Status](https://github.com/tohenk/php-obj/actions/workflows/continuous-integration.yml/badge.svg)
[![Latest Stable Version](https://poser.pugx.org/tohenk/php-obj/v/stable.svg)](https://packagist.org/packages/ntlab/php-obj)
[![Total Downloads](https://poser.pugx.org/tohenk/php-obj/downloads.svg)](https://packagist.org/packages/ntlab/php-obj) 
[![License](https://poser.pugx.org/tohenk/php-obj/license.svg)](https://packagist.org/packages/ntlab/php-obj)

Represent PHP object such as array as string, javascript, annotation, or YAML.

## Examples

* Represent array as string

  ```php
  <?php

  use NTLAB\Object\Arr;

  $a = new Arr([1, 2, 3], ['inline' => true]);
  echo (string) $a; // [1, 2, 3]

  $a = new Arr(['name' => 'Apple', 'color' => 'Red', 'description' => 'It\'s yummy...'], ['inline' => true]);
  echo (string) $a; // ['name' => 'Apple', 'color' => 'Red', 'description' => 'It\'s yummy...']
  ```

* Represent array as annotation

  ```php
  <?php

  use NTLAB\Object\Annotation;

  $a = new Annotation(['name' => 'Apple', 'color' => 'Red', 'description' => 'It\'s yummy...'], ['annotation' => '@Fruit', 'inline' => true]);
  echo (string) $a; // @Fruit(name="Apple", color="Red", description="It's yummy...")
  ```

* Represent array as javascript

  ```php
  <?php

  use NTLAB\Object\JS;

  $a = new JS(['name' => 'Apple', 'color' => 'Red', 'description' => 'It\'s yummy...'], ['inline' => true]);
  echo (string) $a; // {name: 'Apple', color: 'Red', description: 'It\'s yummy...'}
  ```

  * Represent array as YAML

  ```php
  <?php

  use NTLAB\Object\YAML;

  $a = new YAML(['name' => 'Apple', 'color' => 'Red', 'description' => 'It\'s yummy...']);
  echo (string) $a;
  // name:
  //     Apple
  // color:
  //     Red
  // description:
  //     It's yummy...
  ```
  