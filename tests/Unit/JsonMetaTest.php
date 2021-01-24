<?php

namespace Tben\LaravelJsonAPI\Tests\Units;

use Tben\LaravelJsonAPI\Tests\TestCase;
use Tben\LaravelJsonAPI\Facades\JsonMeta;

class JsonMetaTest extends TestCase
{
    /**
     * Undocumented function
     *
     * @return void
     */
    public function testAddNotation()
    {
        // Testing a single add
        JsonMeta::addMetaNotation('test', 'testing');

        $this->assertEquals(
            JsonMeta::viewMetaAll(),
            ['test' => 'testing']
        );

        // Test replacement
        JsonMeta::addMetaNotation('test', 'testing2');

        $this->assertEquals(
            JsonMeta::viewMetaAll(),
            ['test' => 'testing2']
        );

        // Test adding
        JsonMeta::addMetaNotation('test2', 'testing2');

        $this->assertEquals(
            JsonMeta::viewMetaAll(),
            ['test' => 'testing2', 'test2' => 'testing2']
        );
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function testAddArray()
    {
        // Testing a single add
        JsonMeta::addArray([
            "test" => "testing"
        ]);

        $this->assertEquals(
            JsonMeta::viewMetaAll(),
            ['test' => 'testing']
        );

        // Test replacement
        JsonMeta::addArray([
            "test" => "testing2"
        ]);

        $this->assertEquals(
            JsonMeta::viewMetaAll(),
            ['test' => 'testing2']
        );

        // Test adding
        JsonMeta::addArray([
            "test2" => "testing2"
        ]);

        $this->assertEquals(
            JsonMeta::viewMetaAll(),
            ['test' => 'testing2', 'test2' => 'testing2']
        );
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function testEmpty()
    {
        $this->assertEquals(
            JsonMeta::viewMetaAll(),
            null
        );
    }
}