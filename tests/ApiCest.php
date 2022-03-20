<?php

class ApiCest
{
	private string $token;
	
	public function __construct()
	{
		$this->token='620858b67e1654.23842957';
	}
	
	public function tryHomePage(ApiTester $I)
	{
		$I->haveHttpHeader('auth-token', $this->token);
		$I->sendGet('/');
		$I->seeResponseCodeIs(200);
		$I->seeResponseIsJson();
		
		$I->sendPost('/');
		$I->seeResponseCodeIs(200);
		$I->seeResponseIsJson();
	}
	
	public function tryWelcome(ApiTester $I)
	{
		$I->haveHttpHeader('auth-token', $this->token);
		$I->sendPost('/welcome', ['welcome' => 'test']);
		$I->seeResponseCodeIs(200);
		$I->seeResponseIsJson();
		$I->seeResponseContainsJson(['message' => 'received', 'input' => 'test']);
		
		$I->sendPost('/welcome');
		$I->seeResponseCodeIs(404);
		$I->seeResponseIsJson();
		$I->seeResponseContainsJson(['error' => 'Argument not provided']);
	}
	
	public function tryCatch_all(ApiTester $I)
	{
		$I->haveHttpHeader('auth-token', $this->token);
		$I->sendGet('/t');
		$I->seeResponseCodeIs(404);
		$I->seeResponseIsJson();
		$I->seeResponseContainsJson(['Error' => '404 not found']);
	}
	
	public function tryRenew_Token(ApiTester $I)
	{
	
	}
}