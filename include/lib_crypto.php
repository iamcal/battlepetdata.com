<?php

	#
	# authenticated encryption with sodium's secretbox (XSalsa20-Poly1305), keyed by a hash of
	# the secret so it can be any length. The output is base64 of the nonce and the ciphertext.
	#

	function crypto_key($key){

		return sodium_crypto_generichash($key, '', SODIUM_CRYPTO_SECRETBOX_KEYBYTES);
	}

	#################################################################

	function crypto_encrypt($data, $key){

		$nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
		return base64_encode($nonce.sodium_crypto_secretbox($data, $nonce, crypto_key($key)));
	}

	#################################################################

	# '' for anything crypto_encrypt() didn't make with this key
	function crypto_decrypt($enc_b64, $key){

		$enc = base64_decode($enc_b64, true);
		$n = SODIUM_CRYPTO_SECRETBOX_NONCEBYTES;
		if ($enc === false || strlen($enc) < $n + SODIUM_CRYPTO_SECRETBOX_MACBYTES) return '';

		$dec = sodium_crypto_secretbox_open(substr($enc, $n), substr($enc, 0, $n), crypto_key($key));

		return $dec === false ? '' : $dec;
	}

	#################################################################
