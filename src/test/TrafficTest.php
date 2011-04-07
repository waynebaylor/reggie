<?php

class ConventionalControllerTest extends PHPUnit_Framework_TestCase
{
	protected function setup() {
		Traffic::context('/');
	}
	
	public function testParseUriNoPrefixesDefaultSegment() {
		$result = Traffic::parseUri('hello/world', array(), 'q');
		
		$this->assertEquals($result['className'], 'Hello');
		$this->assertEquals($result['action'], 'world');
		$this->assertEmpty($result['params']);
	}
}

?>