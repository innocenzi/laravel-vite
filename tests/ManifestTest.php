<?php

namespace Innocenzi\Vite\Tests;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Innocenzi\Vite\Manifest;

class ManifestTest extends TestCase
{
    /** @test */
    public function it_can_read_a_manifest_from_the_config()
    {
        Config::set('vite.build_path', __DIR__ . '/manifests');
        $manifest = Manifest::read();

        $this->assertCount(1, $manifest->getEntries());
        $this->assertArrayHasKey('resources/js/app.js', $manifest->getEntries());
    }

    /** @test */
    public function it_can_read_a_manifest_from_its_path()
    {
        $manifest = Manifest::read(__DIR__ . '/manifests/two_entries.json');

        $this->assertCount(2, $manifest->getEntries());
        $this->assertArrayHasKey('resources/js/app.js', $manifest->getEntries());
        $this->assertArrayHasKey('resources/js/admin.js', $manifest->getEntries());
    }
    
    /** @test */
    public function it_outputs_scripts_and_css_tags_from_the_manifest()
    {
        $manifest = Manifest::read(__DIR__ . '/manifests/with_css.json');

        $this->assertCount(1, $manifest->getEntries());
        $this->assertEquals(
            '<script src="/build/app.83b2e884.js"></script><link rel="stylesheet" href="/build/app.e33dabbf.css" />',
            $manifest->toHtml()
        );
    }
    
    /** @test */
    public function it_outputs_client_scripts_in_a_local_environment()
    {
        App::bind('env', fn () => 'local');
        $manifest = Manifest::read(__DIR__ . '/manifests/with_css.json');

        $this->assertCount(2, $manifest->getEntries());
        $this->assertEquals(
            \implode('', [
                '<script type="module" src="http://localhost:3000/@vite/client"></script>',
                '<script type="module" src="http://localhost:3000/resources/js/app.js"></script>',
            ]),
            $manifest->toHtml()
        );
    }
}
