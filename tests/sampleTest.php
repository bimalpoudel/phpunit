<?php
use PHPUnit\Framework\TestCase;

class sampleTest extends TestCase
{
	public function setup()
	{
	}

	public function testSomethingThatWorks()
	{
		$value = false;
		$this->assertFalse($value);
	}
}
