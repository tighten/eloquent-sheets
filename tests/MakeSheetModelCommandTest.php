<?php

namespace Tests;

use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Test;

class MakeSheetModelCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (File::exists(__DIR__.'/Models/MySheetModel.php')) {
            File::delete(__DIR__.'/Models/MySheetModel.php');
        }
        config(['sushi.cache-path' => $this->cachePath = __DIR__.'/cache']);
    }

    #[Test]
    public function it_creates_a_sheet_model_without_committing_any_crimes()
    {
        $this->artisan('make:sheet-model')
            ->expectsQuestion('Where would you like to create your sheet model?', __DIR__.'/Models')
            ->expectsQuestion('What do you want the class name of your new model to be?', 'MySheetModel')
            ->expectsQuestion('Copy and paste the full URL of your Google Sheet from your browser address bar:', 'https://docs.google.com/spreadsheets/d/1HxNqqLtc614UVLoTLEItfvcdcOm3URBEM2Zkr36Z1rE/edit#gid=688412530')
            ->expectsQuestion('We were unable to determine the namespace you want to use for your model. Please provide it:', 'Tests\Models')
            ->expectsQuestion('Ready to write model Tests\\Models\\MySheetModel at '.__DIR__.'/Models/MySheetModel.php'.'?', 'yes')
            ->assertExitCode(0);

        $this->assertFileExists(__DIR__.'/Models/MySheetModel.php');

        $work = \Tests\Models\MySheetModel::all();

        $this->assertEquals(3, $work->count());

        $this->assertFileExists('tests/cache/sushi-tests-models-my-sheet-model.sqlite');

        $work->first()->invalidateCache();
        $this->assertFileDoesNotExist('tests/cache/sushi-tests-models-my-sheet-model.sqlite');
    }
}
