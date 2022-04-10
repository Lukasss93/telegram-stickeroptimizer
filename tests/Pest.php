<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

use App\Models\Chat;
use Illuminate\Support\Str;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Media\File;
use SergiX44\Nutgram\Testing\FakeNutgram;

uses(Tests\TestCase::class)
    ->beforeEach(function () {
        $this->chat = Chat::firstOrCreate([
            'chat_id' => 123456789,
        ], [
            'first_name' => 'Tony',
            'last_name' => 'Stark',
            'username' => 'tony.stark',
            'language_code' => 'it',
            'started_at' => now(),
            'blocked_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    })
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
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
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

/**
 * @return FakeNutgram
 */
function bot()
{
    return app(FakeNutgram::class);
}

/**
 * @param callable $callable
 * @return FakeNutgram
 */
function botFromCallable(callable $callable)
{
    return $callable();
}

function partialMockBot($callback)
{
    $mock = Mockery::mock(bot())->makePartial();
    $callback($mock);
    test()->swap(Nutgram::class, $mock);
}

/**
 * @param string $filePath
 * @return \Mockery\MockInterface|File
 */
function mockFile(string $filePath)
{
    $fullPath = base_path('tests'.DIRECTORY_SEPARATOR.$filePath);

    $file = Mockery::mock(new File(bot()));
    $file->file_id = Str::random(3);
    $file->file_unique_id = Str::random(6);
    $file->file_size = filesize($fullPath);
    $file->file_path = $fullPath;
    $file->shouldReceive('url')->andReturn($fullPath);

    return $file;
}
