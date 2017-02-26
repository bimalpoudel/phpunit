<?php
class relay
{
	private $get;
	private $post;
	
	private $log = false;
	
	public function __construct()
	{
		/**
		 * Important to pass by reference
		 */
		$this->get = &$_GET;
		$this->post = &$_POST;
	}

	/**
	 * Save API Access
	 * @param bool $mode
	 */
	public function log($mode=false)
	{
		$this->log = $mode === true;
	}

	/**
	 * Generate current HTTP Referrer
	 * @return string
	 */
	private function http_referer()
	{
		$scheme = !empty($_SERVER["REQUEST_SCHEME"]) ? $_SERVER["REQUEST_SCHEME"] : "http";
		$port = !empty($_SERVER["SERVER_PORT"]) ? ":{$_SERVER["SERVER_PORT"]}" : "";
		$uri = !empty($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : "/";
		$server = !empty($_SERVER["SERVER_NAME"]) ? $_SERVER["SERVER_NAME"] : "localhost";
		$referer = "{$scheme}://{$server}{$port}{$uri}";

		return $referer;
	}
	
	private function http_useragent()
	{
		$useragent = isset($_SERVER["HTTP_USER_AGENT"])?$_SERVER["HTTP_USER_AGENT"]:"";
		return $useragent;
	}
	
	private function parse_merge($url="", $data=array())
	{
		$chunks = parse_url($url);
		#print_r($chunks);
		if(empty($chunks["scheme"])) $chunks["scheme"] = "http";
		if(empty($chunks["host"])) $chunks["host"] = "localhost";
		if(empty($chunks["port"])) $chunks["port"] = "";
		if(empty($chunks["user"])) $chunks["user"] = "";
		if(empty($chunks["pass"])) $chunks["pass"] = "";
		if(empty($chunks["path"])) $chunks["path"] = "/";
		if(empty($chunks["query"])) $chunks["query"] = "";
		if(empty($chunks["fragment"])) $chunks["fragment"] = "";

		#print_r($chunks);
		
		$chunks["pass"] = ($chunks["user"] || $chunks["pass"])?":{$chunks["pass"]}":"";
		$chunks["port"] = empty($chunks["port"])?"":":{$chunks["port"]}";

		$queries = array();
		parse_str($chunks["query"], $queries);
		#print_r($queries);
		#print_r(func_get_args());
		$queries = array_merge((array)$queries, (array)$data);
		
		$chunks["query"] = http_build_query($queries);
		$chunks["query"] = $chunks["query"]?"?{$chunks["query"]}":"";
		
		$chunks["fragment"] = $chunks["fragment"]?"#{$chunks["fragment"]}":"";

		$url = "{$chunks["scheme"]}://{$chunks["user"]}{$chunks["pass"]}{$chunks["host"]}{$chunks["port"]}{$chunks["path"]}{$chunks["query"]}{$chunks["fragment"]}";

		return $url;
	}
	
	public function fetch($url = "")
	{
		$_SERVER["HTTP_REFERER"] = $this->http_referer();
		$_SERVER["HTTP_USER_AGENT"] = $this->http_useragent();

		/**
		 * @todo Sanitize merge process
		 */
		$url = $this->parse_merge($url, $this->get);
		$get_parameters = http_build_query((array)$this->get); # Unused
		$post_parameters = http_build_query((array)$this->post);

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, count($this->post)>=1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_parameters);
		
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 50);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_REFERER, $_SERVER["HTTP_REFERER"]);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_TIMEOUT, 50);
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
		curl_setopt($ch, CURLOPT_VERBOSE, false);
		
		/**
		 * @todo Fix the file path
		 */
		curl_setopt($ch, CURLOPT_COOKIEJAR, "/tmp/cookie.jar");
		curl_setopt($ch, CURLOPT_COOKIEFILE, "/tmp/cookie.jar");
		$content_extracted = curl_exec($ch);

		if($errno = curl_errno($ch)) {
			$error_message = curl_strerror($errno);
			echo "cURL error ({$errno}):\n {$error_message}";
		}
		
		if($this->log)
		{
			$datetime = date("Y-m-d H:i:s");
			$requested_url = $_SERVER["REQUEST_URI"];
			file_put_contents("/tmp/curl.log", "
Time: {$datetime}
IP: {$_SERVER["REMOTE_ADDR"]}
	Requested: {$requested_url}
	Redirected to: {$url}
	GET: {$get_parameters}
	POST: {$post_parameters}

", FILE_APPEND|FILE_BINARY);
		}
		
		curl_close($ch);
		return $content_extracted;
	}
}