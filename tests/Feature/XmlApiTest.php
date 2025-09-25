<?php

namespace Tests\Feature;

use Tests\TestCase;

class XmlApiTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIsStreamContent()
    {
        $response = $this->get('/api/xml-surveys');
        $this->assertTrue($response->isOk());
    }
}
