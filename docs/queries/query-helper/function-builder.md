Function Builder
------------------
The function builder is a simple helper class to help writing and maintaining functions for use with, for example (but not limited to), the analytics component.

As the resulting function can be casted to a string, the usage is pretty straightforward.

Example
-------
```php
<?php

use Solarium\Builder\Analytics\FunctionBuilder;

// ...

$query = $client->createSelect();
$analytics = $query->getAnalytics();

$expr = FunctionBuilder::expr();
$builder = FunctionBuilder::create()
    ->where($expr->div(
        $expr->sum(
            'a',
            $expr->fillMissing('b', 0)
        ),
        $expr->add(
            10.5,
            $expr->count(
                $expr->mult('a', 'c')
            )
        )
    ))
;

$analytics
    ->addFunction('sale()', (string) $builder->getFunction())
;

// which would be equal to
$analytics
    ->addFunction('sale()', 'div(sum(a,fill_missing(b,0)),add(10.5,count(mult(a,c))))')
; 
```