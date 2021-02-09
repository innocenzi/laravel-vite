<?php

namespace Innocenzi\Vite\Tests;

use Illuminate\Support\Facades\Blade;

class BladeDirectivesTest extends TestCase
{
    protected array $directives = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->directives = Blade::getCustomDirectives();
    }

    /** @test **/
    public function it_creates_directives()
    {
        $this->assertArrayHasKey('vite', $this->directives);
    }

    /** @test **/
    public function it_parses_script_directive_arguments()
    {
        $this->assertEquals(
            '<?php echo Innocenzi\Vite\Manifest::read()->toHtml(); ?>',
            $this->directives['vite']()
        );
        
        $this->assertEquals(
            '<?php echo Innocenzi\Vite\Manifest::read()->getEntry(e(resources/ts/main.ts))->toHtml(); ?>',
            $this->directives['vite']('resources/ts/main.ts')
        );
    }
}
