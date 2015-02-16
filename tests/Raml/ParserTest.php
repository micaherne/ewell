<?php

class ParserTest extends \PHPUnit_Framework_TestCase {

	private $parser;
	private $raml;

	public function setUp() {
		$this->parser = new Raml\Parser(__DIR__.'/../fixtures/raml-tutorial-200/jukebox-api.raml');
		$this->raml = $this->parser->getRaml();
	}

	public function testRootValues() {

		$raml = $this->raml;

		$this->assertEquals("Jukebox API", $raml['title']);
		$this->assertEquals("http://jukebox.api.com", $raml['baseUri']);
		$this->assertEquals("v1", $raml['version']);

		$this->assertCount(3, $raml['schemas']);

	}

	public function testGetSchemas() {
		$schemas = $this->parser->getSchemas();
		$this->assertCount(3, $schemas);
		$this->assertArrayHasKey('song', $schemas);
	}

	public function testGetResourceTypes() {
		$resourceTypes = $this->parser->getResourceTypes();
		$this->assertCount(3, $resourceTypes);
		$this->assertArrayHasKey('readOnlyCollection', $resourceTypes);
	}

	public function testGetTraits() {
		$traits = $this->parser->getTraits();
		$this->assertCount(3, $traits);
		$this->assertArrayHasKey('orderable', $traits);
	}

	public function testGetResources() {
		$resources = $this->parser->getResources(false);
		$this->assertCount(3, $resources);
		$this->assertArrayHasKey('/songs', $resources);
	}

}