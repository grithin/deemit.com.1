<?
///Handling curl functions
/*
Copyright (c) 2008 Sean Huber - shuber@huberry.com

Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the
"Software"), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

https://github.com/theshock/curl
*/



/**
* A basic CURL wrapper
*
*
* @author Sean Huber <shuber@huberry.com>
* @author Fabian Grassl
**/
class Curl{
	public $headers = array();///<Add headers into this variable
	public $cookie_file = null;///<the file path used for the curl cookie
	public $follow_redirects = true;///<determines whether curl will follow redirects
	public $options = array();///<curl options
	public $referrer = null;///<fabricated referrer
	public $user_agent = null;///<fabricated user agen to use.  Defaults to the user agent requesting the page
	public $error = '';///<curl errors
	public $validate_ssl = false;
	public $timeout = 30;///<timeout after which curl fails
	public $deleteCookie = true;///<option of whether to delete cookie on curl object destruction
	
	public $request = null;
	
	public function __construct(){
		$this->user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Curl/PHP '.PHP_VERSION;
		$this->cookie_file = '/tmp/curl.'.str_replace(' ','_',microtime());
		
		//remove this unwanted header which is set by default by curl
		$this->headers['Expect'] = '';
	}
	public function __destruct(){
		if($this->deleteCookie){
			@unlink($this->cookie_file);
		}
	}
	
	public function delete($url, $vars = array()){
		return $this->request('DELETE', $this->create_get_url($url, $vars));
	}
	///send GET curl request
	/**
	@param	url	url to send to
	@param	vars	variables to append to the url, see Http::appendsUrl()
	@return curlResponse object
	*/
	public function get($url, $vars = array()){
		return $this->requestFix('GET', $this->create_get_url($url, $vars));
	}

	protected function create_get_url($url, $vars = array()){
		if($vars){
			$url = Http::appendsUrl($vars,$url,false);
		}
		return $url;
	}
	
	public function head($url, $vars = array()){
		return $this->request('HEAD', $this->create_get_url($url, $vars));
	}
	///send POST curl request
	/**
	@param	url	url to send to
	@param	vars	variables to send in post
	@return curlResponse object
	*/
	public function post($url, $vars, $files = null){
		return $this->requestFix('POST', $url, $vars, $files);
	}
	/**
	curl library fails to handle relative paths with pathed cookies correctly, so have to manually absolutize paths
	*/
	public function requestFix($method, $url, $post_vars = array(), $files = null){
		//fixed post to handle relative url paths with 
		$url = Http::getAbsoluteUrl($url);
		$response = $this->request($method, $url, $post_vars, $files);
		if ($this->follow_redirects){
			while($response->headers['Location']){
				$response = $this->get(Http::getAbsoluteUrl($response->headers['Location']));
			}
		}
		return $response;
	}

	public function request($method, $url, $post_vars = array(), $files = null){
		$this->error = '';
		$this->request = curl_init();

		$this->setRequestOptions($url, $method, $post_vars, $files);
		$this->setRequestHeaders();

		$response = curl_exec($this->request);

		if ($response){
			$response = new CurlResponse($response);
		}else{
			$this->error = curl_errno($this->request).' - '.curl_error($this->request);
		}

		curl_close($this->request);

		return $response;
	}
	
	protected function setRequestHeaders(){
		$headers = array();
		foreach ($this->headers as $key => $value){
			$headers[] = $key.': '.$value;
		}
		curl_setopt($this->request, CURLOPT_HTTPHEADER, $headers);
	}

	protected function setRequestOptions($url, $method, $vars, $files = null){
		$purl = parse_url($url);

		if ($purl['scheme'] == 'https'){
			curl_setopt($this->request, CURLOPT_PORT , empty($purl['port'])?443:$purl['port']);
			if ($this->validate_ssl){
				curl_setopt($this->request,CURLOPT_SSL_VERIFYPEER, true);
				curl_setopt($this->request, CURLOPT_CAINFO, dirname(__FILE__).'/cacert.pem');
			}else{
				curl_setopt($this->request, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($this->request, CURLOPT_SSL_VERIFYHOST, 2);
			}
		}

		$method = strtoupper($method);
		switch ($method){
			case 'HEAD':
				curl_setopt($this->request, CURLOPT_NOBODY, true);
				break;
			case 'GET':
				curl_setopt($this->request, CURLOPT_HTTPGET, true);
				break;
			case 'POST':
				curl_setopt($this->request, CURLOPT_POST, true);
				break;
			default:
				curl_setopt($this->request, CURLOPT_CUSTOMREQUEST, $method);
		}

		curl_setopt($this->request, CURLOPT_URL, $url);

		if ($files || !empty($vars)){
				if ('POST' != $method){
					throw new InvalidArgumentException('POST-vars may only be set for a POST-Request.');
				}
				if($files){
					foreach($files as &$file){
						if($file[0] != '@'){
							$file = '@'.$file;
						}
					}
					unset($file);
					curl_setopt($this->request, CURLOPT_POSTFIELDS, Arrays::merge($files,$vars));
				}else{
					curl_setopt($this->request, CURLOPT_POSTFIELDS, Http::buildQuery($vars));
				}
		}elseif ('POST' == $method){
			throw new InvalidArgumentException('POST-vars must be set for a POST-Request.');
		}

		# Set some default CURL options
		curl_setopt($this->request, CURLOPT_HEADER, true);
		curl_setopt($this->request, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->request, CURLOPT_USERAGENT, $this->user_agent);
		curl_setopt($this->request, CURLOPT_TIMEOUT, $this->timeout);

		if ($this->cookie_file){
			curl_setopt($this->request, CURLOPT_COOKIEFILE, $this->cookie_file);
			curl_setopt($this->request, CURLOPT_COOKIEJAR, $this->cookie_file);
		}

		/* relative paths fix, see requestFix
		if ($this->follow_redirects){
			curl_setopt($this->request, CURLOPT_FOLLOWLOCATION, true);
		}
		*/

		if ($this->referrer){
			curl_setopt($this->request, CURLOPT_REFERER, $this->referrer);
		}

		# Set any custom CURL options
		foreach ($this->options as $option => $value){
			curl_setopt($this->request, $option, $value);
		}
	}

}

///Parses the response from a Curl request into an object containing the response body and an associative array of headers
/**
Curl response headers available in $headers attribute.  Response body available in $body attribute.
@note	__toString method is set to print out the body of the response
@author Sean Huber <shuber@huberry.com>
**/
class CurlResponse{
	public $body = '';
	public $headers = array();

	function __construct($response){
		do{
			list($header, $response) = explode("\r\n\r\n", $response, 2);
			# handle 1xx responses and 3xx redirects
			list($statusLine) = explode("\r\n", $header, 2);
		}
		while (!empty($response) && preg_match('/\h((1|3)\d{2})\h/',$statusLine));

		$this->body = $response;

		$headers = explode("\r\n", $header);

		# Extract the version and status from the first header
		$version_and_status = array_shift($headers);
		preg_match('#HTTP/(\d\.\d)\s(\d\d\d)\s(.*)#', $version_and_status, $matches);
		$this->headers['Http-Version'] = $matches[1];
		$this->headers['Status-Code'] = $matches[2];
		$this->headers['Status'] = $matches[2].' '.$matches[3];

		# Convert headers into an associative array
		foreach ($headers as $header){
			preg_match('#(.*?)\:\s(.*)#', $header, $matches);
			$this->headers[$matches[1]] = $matches[2];
		}
	}

	public function __toString(){
		return $this->body;
	}

	public function isHtml(){
		$type = isset($this->headers['Content-Type'])?$this->headers['Content-Type']:'';
		if (preg_match('/(x|ht)ml/i', $type)){
			return true;
		}else{
			return false;
		}
	}

	public function getMimeType(){
		$type = isset($this->headers['Content-Type'])?$this->headers['Content-Type']:false;
		if ($type){
			list($type) = explode(";", $type);
			$type = trim($type);
		}
		return $type;
	}
}