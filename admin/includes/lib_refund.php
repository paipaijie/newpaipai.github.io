<?php

require_once ROOT_PATH . 'admin/alipay/AopSdk.php';

//  
function alipay_refund($out_trade_no,$refund_amount){
        
        $appId = "2018052960298201";

        $Private_Key = "MIIEogIBAAKCAQEAu9/670kbrCGIXSQAhwgfUV5BRS2TU+PR/hOOiyFPcH+3oMZ/+xSTUh/ciH1r9d+Zv9xlcpI9o9abHOi9t3+7DCWt7DrUZKdPxr+fpJnvYQKVsodXJVbxAT6tVxUwDT6L4ZkhoWcQfwW5q8I9oy12uzT8E/P91apqPDkIVcN4VYv+8DU+bkcZNV/byL4QNOl81DUd99JF7DhYmtNvg8ZiJZawVlT/2vEzk/0rPftbuiePJi05wQ8QDgQYsR0ivAX+RkHXYnO4s8RKZccXzb/toNNDAMXnRC+yVeODus9lM5uUIGTmX+txPjOY2vqVXrNcWkR7ihMnxmBkBqOSPjxbtQIDAQABAoIBAEAH1j43bHb99rvHyWY8HnBc72HkZpHw02EEyVAhyjFNVgg1nuiz2oHi2gaquGDUFFKPiwPBU8DDI5p2uDISFm3NablZU4n6e/YA2SgATtWBTAPMnKbdOsZx1iIX1oitCH+a8RtRRQ4FpYIkcV1r4Utsg31JavmnWw2rYmvYcuq0dnFHaDPAqBIT58Y4MHWPfmhv081IqqUIv06pluxMKLHfFl49Aus9iSAUcNWm4zpF3IPhKVtC9sQQgFtYDgfhL0aL5dwYIraFSdEfAnP0xFloC6UkEMcG7MSuYzB2/sJ/6yqInqBBR/IK830D97hqmlA5OVY0BmzLwfxjV6duOAECgYEA3EwVRZfXCBAPcREWrErgMje2D9gvj0ph89ChkYV/3anROS2pc9wEkuz+HeL0oVlUREicXix4mGt7oqrVKMrHS9cEu6nEZBohTzXAJ3/B5M6TZkB7QbXy3D/Vf0d/Ra7FsfilSKfajhIpnRUa+mptnpTpIh4dRWNIFT3BBH53y00CgYEA2lK6dCAOMHj7ZgROvZ0ES2/0dlEwYEk5vItgYcbb6ndH8DiV5QZBHb7dHb6DgkAUUJ8+Lz5B58iX0HqhucJ94aiCSL3XsB9pwy7TXh2uKyJtplG3UugwgZkLXB6oVpuXAq1j0hB062LWNOWDT3FPy8RmPhrAvhF3ITZ8XTaIDgkCgYAEvVZP0v57W+0ZH6b/OfUs32n5WUtcp9sHpdt0CA1CNZ8cvdDBf2BR0Ot+tR1SQ1u+xFko5YgKr3VPgKFpaUN0bEHbz8E77hv9WkiyENuZEqC1OHnuZf29o1nyjRU96I60T4cu0v2uxNFEYencrWGCYe3KIB+9GG+6lUOL1zS4mQKBgAEvmduvoOQahCMVuufExpUyUn2iC0U6oJmpmChv0BoB894WMJpB4nCq7MCQnF5Ewtd9RfUzvV/o8woq4F4F9HEZXTvP7DAEu6pXwsYsGxoY4ceZggltQXYywqEJeZie178n+fbWKAxvSQKYjJIN54UP2A5WTt25cpA3n41SEoMhAoGAe7vNfWwvhQGLkQGtoeo51sw9LbzWKU1FEdLjMbw1Qq/Y0V23iUpRvNAB6cAv7XCCzqv7+zEnYF3J4abryLXZ+h/SUYgPjSULrDBBWU+iFfUwoM9PsV1O7Ie97U1cav8j02VTid+8fQUHGBVewyMjQT8HlPS83xWaELm7YNQCJBg=";

        $Public_Key = "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAkir0NEbJmQivQNlcE016iiizkMFsQoCAGzZgeRzMtSRFXD8yAr3ExWqH7bbt2h0s2BvUI4+fWCSFo0PfssD4S4TKFXehf8M+dYWMqwF/xtU26SOWEJJPDYrJn3391qbKgI9AjOCUo9OFGZxCSYxs8JYxJLDTHQfTp+IGuiQK3rfrR+4en3RddmUu5neF0R9kQuI8O7wvwQOexZeoHnMZSJRTRwfBUQQz3b6+LfevjPXe70RTyUoEDhvNGNA2AXhg3eqOodoeslAKNBWRDN8UlWVKMwcl5TF7o7TjIIoESDPwQNzojQOpoTG+r1g6GoYrf0oo7wOEVYQiJo2bIgdnNwIDAQAB";       

        $aop = new AopClient ();
		$aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
		$aop->appId = $appId;
		$aop->rsaPrivateKey = $Private_Key;
		$aop->alipayrsaPublicKey=$Public_Key;
		$aop->apiVersion = '1.0';
		$aop->signType = 'RSA2';
		$aop->postCharset='UTF-8';
		$aop->format='json';
        
        

		$request = new AlipayTradeRefundRequest ();
		$request->setBizContent("{" .
			"\"out_trade_no\":\"{$out_trade_no}\"," .
	//		"\"trade_no\":\"{$trade_no}\"," .
			"\"refund_amount\":\"{$refund_amount}\"," .
			"\"refund_reason\":\"保证金退款\" " .
		"}");
        
        $result = $aop->execute ( $request); 

		$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        $result_msg = $result->$responseNode->sub_msg;
  
 
		if(!empty($resultCode)&&$resultCode == 10000){
		            return array('code'=>$resultCode,'sub_msg'=>$result_msg);
		} else {
		            return array('code'=>$resultCode,'sub_msg'=>$result_msg);
		}	
	

}

?>




