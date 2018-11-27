<?php

require_once ROOT_PATH . 'admin/alipay/AopSdk.php';

//  
function alipay_refund($out_trade_no,$refund_amount){
        
        $appId = "2018080960980568";

        $Private_Key = "MIIEowIBAAKCAQEAuhrrxPoA/aEXWbDdP+tWq4u7OFfhW8j+iXxRBfN+0yBjntjsm8RjRaRE5DVaOgkbAFQvtz9932pmvp4Z9iwlE01ygB098c2MePTCKk+lHWwGgMBtJQybyaCICcKe+oewarUAmdN5kfo/+RspCyAUzS8VRWE36nhFOS+PgxjS44go59JYkr0K/Zr0eKz9skldL61THF/ABsSQCtQ0FrQvkTCLlyyy7VRKfFpSp+9yeFKrrtjTyBtmbwzvArrfS1sxjJJiyZV4xjnC+LElkUc3GlMccbD6UrfW2rnPkQTjPLwIlaz+hjLNDvCDX+5+zHSot1pVV4srEbJe4Nx1IlC2jQIDAQABAoIBAH2EdKmIWCy3flEuuTpIFk9i0aWhl8vY1tuRbfAOluX8PVRAR+yS2sV4kkI8PZtQ0tY48WmtN878K0m6xhXDFkpbfLMthb7U/D4EDgSNbPZYClbB8ZsAUv38+GNk0OZ1p6WAaGRXPfIXjxgKmnb3bzAn5jfB5v2LmlyEWE1565oq7d0EWs2AC2mOjibWX+4qI1d4TtWnoNhypUF+LLjGNwrMZS1jPp+N7+ZCwzfJ0dnqAZ/beTcQp02tQKVZnIkqk67Ivrp4/nmdB5I+jmq/7OjSDhAW04FCq3z9dAoxx/d0jHPiPNrhgxOmVlOFbovyLvi5anopM+uehq7NxdISdt0CgYEA9JaOtgu6ZrcSxW5omRGVL5nrBtIsrX1qBvGkx2mAC5MmlF3zga1heuZGCZ0d8/ntZIU/byhmbg3c5X0/9bxrY+quDuzN46QHPLGTNOwoA23OCFfYVdji6e9i6nReXYCXVkxID8m7JnRazzPVAx6jm+SEOPUPlcphVORnKUmwIhcCgYEAwsnSzq20qZVCUeL6la43LB84xw0rX0+qTQab07Sy0vRXTvydye014F0dId6glE0V8JgporJJm15RSR3pOOCWenYNRyR9EoPdtxm3Au752OJiEXcDOETEVuR3CfMGwbrjrFv4WdAa4RN/B4Kcp1isjRZvZLtIh1JReL1JA9I7RvsCgYEAh3/9MedPbeYQVSY0dlBiWMupg87eB4MKQSyPwBW3ZTIxVlKyO24lKeP2ew768BXOqTiUiu1TybaOYOjBjU7EI8d4hDY2Xd2aUMqDpfcKt9OWJ9Mau3x7QWRoZUDwolZCAQIjjfORZd1iDZZGdQL9Wrj/c1p4NuC2iUQ7zojPNiMCgYApomnks0xCUxzx08or5Cj2zVWddjsnDCgifp8gCSeP35opz+UKHCG/6ycm7ib0i+V8n4mGtQT2qJpHPpVzCs4fLsx3wFzbcEQbtutfPSU6Lmk3wnB1vn0Y9YxcHA39qqyvUpkq2Uhlg31LZaeGPXXkUj9NEJrYEchIPm002+K/sQKBgHcJxzU51J6tFtImxjlC8qVUX9UvKJPKFMav7o14XsbNBe1A7mKycodsBZR2fi6fOoR5THgMUzd7RJOg0+XclCOM5kpzYh8H+HLfALi6p5DQm4YKOx49ECmxZh/RNVkIZX0kpbILB2XEgAgkkoYSqMCkPEzVdTaWh198QGsHBwVH";

        $Public_Key = "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAwHkIZ+53EV5fDhS6QDrJ5Potff8IfhD5cRZ1FxyUAvvSKV1XwCGER5SwTS48MopQGPWCOEpflrgoQoYC318ABW6hOhXOLJV6y2fx8fBsLpOVZZXbq9Fdwiwd+7qno92TY1chOP7kDPzF+yXk6MGJ8cLkYDcw9bmWq9HIUkwbP6EfuxmY0gloK+qcl6I8dYiDf6h5XiFiM8/EePNUZWlG1BsoNBR/FVQvAAt316VzBCgedK1qcRTwsNZZ15MqT+U7HjrL0vd4gLK4QFnonLodYkJ3O7ITT0/uRd4CsuRy+d+w8ygCBGECPwJ09ZoW2vihp7SqTbsr3XYbcJeSH7SklwIDAQAB";

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

        return array('code'=>$resultCode,'sub_msg'=>$result_msg);
	

}

?>




