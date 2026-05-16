<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is executed within the
| directory of the test case class, which gives you access to a set of
| methods that can be used to interact with the application and its.
|
*/

uses(TestCase::class, RefreshDatabase::class)->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that certain values
| meet certain criteria. The "expect()" function gives you access to a
| set of "expectations" methods that you can use to assert anything.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing
| code that you want to reuse throughout your tests. Functions are the
| perfect place to define those helpers.
|
*/

function actingAsUser()
{
    // return test()->actingAs(...);
}
