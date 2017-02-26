<?php
use PHPUnit\Framework\TestCase;

class pingTest extends TestCase
{
	public function setup()
	{
	}

	public function testPing()
	{
		// query
		// response is pong
		$relay = new relay();
		$actual = $relay->fetch("http://localhost/angular/public_html/api/ping.php");
		$expect = "pong";
		$this->assertEquals($actual, $expect);
	}
}
