<?php
use PHPUnit\Framework\TestCase;

class pingTest extends TestCase
{
	private $relay;

	public function setup()
	{
		$this->relay = new relay();
	}

	public function testPing()
	{
		$pingpongurl = "http://api.example.com/ping.php";
		$actual = $this->relay->fetch($pingpongurl);
		$expect = "pong";

		$this->assertEquals($actual, $expect);
	}

	/**
	 * @see http://requestb.in/{$id}?inspect
	 */ 
	public function testRequestBin()
	{
		$id = "0000000";

		$_GET = array("name" => "pong");
		$_POST = array("name" => "ping");

		$actual = $this->relay->fetch("http://requestb.in/{$id}");

		$expect = "ok";

		$this->assertEquals($actual, $expect);
	}
	
	// address
	// server time
}
