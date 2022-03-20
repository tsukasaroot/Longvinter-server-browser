<?php

use Core\Routes;
use Core\Http;
use Core\Token;

Routes::post(route: '/renew_token', closure: function () {
	$token = new Token();
	$token_input = apache_request_headers()['auth-token'] ?? '';
	$token->renewToken($token_input);
});

Routes::get(route: '/', closure: function () {
	Http::sendJson([ 'message' => 'Welcome' ]);
});

Routes::post(route: '/', closure: function () {
	Http::sendJson([ 'message' => 'Welcome' ]);
});

Routes::post(route: '/welcome', action: 'WelcomeController@test_post');