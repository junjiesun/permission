<?php
/*
 * With reference to https://github.com/olect/Optimus
 * With reference to https://github.com/brainfoolong/cryptojs-aes-php/blob/master/cryptojs-aes.php
 * 
 * */
namespace Lib\Support\CipherCode;

use Lib\Support\CipherCode\Exception\CipherCodeException;

class CipherCode
{
	private $publicKey;
	private $privateKey;
	
	private $publicKeyPath = 'password_key/rsa_public_key.pem';
	private $privateKeyPath = 'password_key/rsa_private_key.pem';
	
	public function __construct()
	{	
		if ( !file_exists($this->publicKeyPath) || !file_exists($this->privateKeyPath) )
		{
			throw new CipherCodeException("File does not exist Please make sure the");
		}
				
		$prviateResults = file_get_contents($this->privateKeyPath);
		$publicResults = file_get_contents($this->publicKeyPath);

		// 这个函数可用来判断私钥是否是可用的，可用返回资源id Resource id  
		$pi_key = openssl_pkey_get_private($prviateResults);
		// 这个函数可用来判断公钥是否是可用的 
		$pu_key = openssl_pkey_get_public($publicResults);
		
		if ( $pi_key == FALSE || $pu_key == FALSE )
		{
			throw new CipherCodeException("Password key is not available");
		}

		$this->publicKey = $pu_key;
		$this->privateKey = $pi_key;
	} 
	
	public function encrypted( $data )
	{
		$encrypted = '';	
		// 私钥加密	
		// openssl_private_encrypt( $data, $encrypted, $this->privateKey );
		// 公钥加密 
		openssl_public_encrypt( $data, $encrypted, $this->publicKey );
		
		// 加密后的内容通常含有特殊字符，需要编码转换下，在网络间通过url传输时要注意base64编码是否是url安全的 
		$encrypted = base64_encode($encrypted);
		
		return $encrypted;
	}
	
	public function decrypted( $encrypted )
	{
		$decrypted = '';
		// 私钥加密的内容通过公钥可用解密出来 	
		// openssl_public_decrypt( base64_decode($encrypted), $decrypted, $this->publicKey );
		// 公钥加密的内容通过私钥可用解密出来
		openssl_private_decrypt( base64_decode($encrypted), $decrypted, $this->privateKey );
		
		return $decrypted;
	}
	
	
}
