<?php

class ConventionalControllerTest extends PHPUnit_Framework_TestCase
{
	protected function setup() {
		Traffic::context('/');
	}
	
	public function testParseUriNoArgsNoParams() {
		$c = new ConventionalController();
		$result = $c->parseUri('hello/world');
		
		$this->assertEquals($result['className'], 'Hello');
		$this->assertEquals($result['action'], 'world');
		$this->assertEmpty($result['params']);
	}
	
	public function testParseUriNoArgsWithSeparator() {
		$c = new ConventionalController();
		$result = $c->parseUri('hello/world/q');
		
		$this->assertEquals($result['className'], 'Hello');
		$this->assertEquals($result['action'], 'world');
		$this->assertEmpty($result['params']);
	}
	
	public function testParseUriNoArgsWithParams() {
		$c = new ConventionalController();
		$result = $c->parseUri('hello/world/q/value/7');
		
		$this->assertEquals($result['className'], 'Hello');
		$this->assertEquals($result['action'], 'world');
		$this->assertEquals($result['params'][0], 'value');
		$this->assertEquals($result['params'][1], '7');
	}
	
	public function testParseUriWithDirNoParams() {
		$c = new ConventionalController('dir');
		$result = $c->parseUri('hello/world');
		
		$this->assertEquals($result['className'], 'dir_Hello');
		$this->assertEquals($result['action'], 'world');
		$this->assertEmpty($result['params']);
	}
	
	public function testParseUriWithDirWithParamSeparator() {
		$c = new ConventionalController('dir');
		$result = $c->parseUri('hello/world/q');
		
		$this->assertEquals($result['className'], 'dir_Hello');
		$this->assertEquals($result['action'], 'world');
		$this->assertEmpty($result['params']);
	}
	
	public function testParseUriWithDirWithParams() {
		$c = new ConventionalController('dir');
		$result = $c->parseUri('hello/world/q/value/7');
		
		$this->assertEquals($result['className'], 'dir_Hello');
		$this->assertEquals($result['action'], 'world');
		$this->assertEquals($result['params'][0], 'value');
		$this->assertEquals($result['params'][1], '7');
	}
	
	public function testParseUriNoParamsWithEmptyDirAndSeparator() {
		$c = new ConventionalController('', 'qw');
		$result = $c->parseUri('hello/world');
		
		$this->assertEquals($result['className'], 'Hello');
		$this->assertEquals($result['action'], 'world');
		$this->assertEmpty($result['params']);
	}
	
	public function testParseUriWithSeparatorEmptyDir() {
		$c = new ConventionalController('', 'qw');
		$result = $c->parseUri('hello/world/qw');
		
		$this->assertEquals($result['className'], 'Hello');
		$this->assertEquals($result['action'], 'world');
		$this->assertEmpty($result['params']);
	}
	
	public function testParseUriWithEmptyDirSeparatorAndParams() {
		$c = new ConventionalController('', 'qw');
		$result = $c->parseUri('hello/world/qw/value/7');
		
		$this->assertEquals($result['className'], 'Hello');
		$this->assertEquals($result['action'], 'world');
		$this->assertEquals($result['params'][0], 'value');
		$this->assertEquals($result['params'][1], '7');
	}
	
	public function testParseUriWithDirAndSeparatorNoParams() {
		$c = new ConventionalController('dir', 'qw');
		$result = $c->parseUri('hello/world');
		
		$this->assertEquals($result['className'], 'dir_Hello');
		$this->assertEquals($result['action'], 'world');
		$this->assertEmpty($result['params']);
	}
	
	public function testParseUriWithDirAndSeparator() {
		$c = new ConventionalController('dir', 'qw');
		$result = $c->parseUri('hello/world/qw');
		
		$this->assertEquals($result['className'], 'dir_Hello');
		$this->assertEquals($result['action'], 'world');
		$this->assertEmpty($result['params']);
	}
	
	public function testParseUriWithDirAndSeparatorAndParams() {
		$c = new ConventionalController('dir', 'qw');
		$result = $c->parseUri('hello/world/qw/value/7');
		
		$this->assertEquals($result['className'], 'dir_Hello');
		$this->assertEquals($result['action'], 'world');
		$this->assertEquals($result['params'][0], 'value');
		$this->assertEquals($result['params'][1], '7');
	}
}

?>