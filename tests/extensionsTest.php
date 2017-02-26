<?php
use PHPUnit\Framework\TestCase;

class extensionsTest extends TestCase
{
	public function setup()
	{
	}

	public function testCurlExists()
	{
		$value = extension_loaded("curl");
		$this->assertTrue($value);
	}
}
