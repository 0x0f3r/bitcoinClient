<?php
/*  
		Copyright 2011, Fabian Heredia
    Licensed under the Apache License, Version 2.0 (the "License");
	you may not use this file except in compliance with the License.
	You may obtain a copy of the License at
	 
		http://www.apache.org/licenses/LICENSE-2.0
     
	Unless required by applicable law or agreed to in writing, software
	distributed under the License is distributed on an "AS IS" BASIS,
	WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
	See the License for the specific language governing permissions and
	limitations under the License.
*/
require_once('jsonRPCClient.php');

	// Enumeration of info and actions
	const error 			= -1;
	// Info
	const numBlocks 		= 0;
	const numNodes 			= 1;
	const diff 				= 2;
	const generating 		= 3;
	const hashRate 			= 4;
	// Actions
	const getAddress		= 5;
	const getAssociated		= 6;
	const getBalance 		= 7;
	const newAddress		= 8;
	const internalTransfer 	= 9;

class bitcoinClient
{	
	// Global vars
	private $client;
	
	public function __construct($address)
	{
		$this->client = new jsonRPCClient($address);
	}
	
	// Backs up wallet to the specified path with or without filename.
	public function backUp($path)
	{
		return $this->client->backupwallet($path);
	}
	
	// !Emergency use only! will, while the script is runn it will redirect
	// all incoming bitcoins to an address elsewhere. This should work well
	// with IDS, IPS systems in order to minimize financial losses.
	// TODO: Avoid timeout
	public function emergencyFlush($address)
	{
		while(1)
		{
			$balance = $this->getBalance();
			$this->sendBitcoins($address, $balance);
			// Default is 1 second sleep between calls, you might change this
			// as needed.
			sleep(1);
			// Until the timeout issue is resolve, only 6 minutes of protection
			// is offered.(300 secs)
		}
	}
	
	//Cobine in one function internal and external transactions
	// Send the specify ammount of bitcoins to an address.
	public function sendBitcoins($address, $ammount)
	{
		return $this->client->sendtoaddress($address, $ammount);
	}
	
	// This is misc info not required for normal operation. It should be self
	// desciptive. You should also be able to use it in the following manner:
	// - $myClient = new bitcoinClient("http://root:@localhost:8332");
	// - echo myClient::getMiscInfo(numNodes);
	public function getMiscInfo($type)
	{
		switch($type)
		{
			case numBlocks:
				return $this->client->getblockcount();
			case numNodes:
				return $this->client->getconnectioncount();
			case diff:
				return $this->client->getdifficulty();
			case generating:
				return $this->client->generating();
			case hashRate:
				return $this->client->gethashespersec();
			default:
				return error;
		}
	}
	
	public function accountActions($action, $account = null, $all = false)
	{
		switch($action)
		{
			case getAddress:
				if($account == null){
					// We cannot miss the $accoutn argument
					return error;
				}
				else{
					return (!$all) ? $this->client->getaccountaddress($account) : $this->client->getaddressesbyaccount($account);
				}
			case getAssociated:
				// Should be $address instead of $account
				return ($account == null) ? $this->client->getaccount() : $this->client->getaccount($account);
			case getBalance:
				return ($account == null) ? $this->client->getbalance() : $this->client->getbalance($account);
			case newAddress:
				return ($account == null) ? $this->client->getnewaddress() : $this->client->getnewaddress($account);
			default:
				return error;
		}
	}
}
?>