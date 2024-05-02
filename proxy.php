<?php
$url = $_GET["url"];
if (!preg_match("~^https?://~i", $url)) {
  $url = "http://" . $url;
}
 
$request_headers = array();
foreach (getallheaders() as $header => $value) {
  $request_headers[] = $header . ": " . $value;
}
 
$context = stream_context_create($request_headers);
$response = file_get_contents($_GET["url"], false, $context);
$response_headers = explode("\r\n", substr($response, 0, curl_getinfo($ch, CURLINFO_HEADER_SIZE)));
$response_body = substr($response, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
curl_close($ch);
 
$propagate_headers = array("content-type"); //add additonal headers you want to propagate to this array in lower case
foreach ($response_headers as $header) {
  if (in_array(strtolower(explode(": ", $header)[0]), $propagate_headers)) {
    header($header);
  }
}
 
// you only need the following headers if the proxy and WebGL are hosted on different domains
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Accept, X-Access-Token, X-Application-Name, X-Request-Sent-Time");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Origin: *");
// you can replace the "*" above with "http://your-webgl-domain" to prevent unauthorized use of your proxy
// in which case you should also append "Origin: http://your-webgl-domain" header to the request
 
echo $response_body;

?>