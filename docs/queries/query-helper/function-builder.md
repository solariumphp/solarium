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
$client = new Solarium\Client($config);
$analytics = $query->getAnalytics();

$builder = FunctionBuilder::create()
    ->where(FunctionBuilder::expr()->div(
        FunctionBuilder::expr()->sum(
            'a',
            FunctionBuilder::expr()->fillMissing('b', 0)
        ),
        FunctionBuilder::expr()->add(
            10.5,
            FunctionBuilder::expr()->count(
                FunctionBuilder::expr()->mult('a', 'c')
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