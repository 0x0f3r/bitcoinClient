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
	// TODO: Add Documentation
	const error 			= -1;
	// Info
	// - Misc
	const numBlocks 		= 0;
	const numNodes 			= 1;
	const diff 				= 2;
	const generating 		= 3;
	const hashRate 			= 4;
	// - Account
	const address			= 5;
	const associated		= 6;
	const balance 			= 7;
	const newAddress		= 8;
	const listAccounts		= 9;
	const validate			= 10;
	// Actions

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
	
	// TODO: Automatically choose between move, sendtoaddress and sendfrom
	// Send the specify ammount of bitcoins to an address.
	public function send($address, $ammount, $from = null)
	{
		// Simple send
		if($from == null) return sendtoaddress($address, $ammount);
		
		// $from specified
		$info = array($this->accountInfo(validate, $address), $this->accountInfo(validate, $from));
		if(info[0]['isvalid'] && info[1]['isvalid'])
		{
			return (info[0]['ismine']) ? $this->client->move($this->accountInfo(associated, $from), $this->accountInfo(associated, $address), $ammount) : $this->client->sendfrom($from, $address, $ammount);
		}
		else if(info[0]['isvalid'])
		{
		// Still working on this
			return (info[0]['ismine']) ? $this->client->move($from, $this->accountInfo(associated, $address), $ammount) : $this->client->sendfrom($this->accountInfo(address, $from), $address, $ammount);
		}
		else if(info[1]['isvalid'])
		{
			return (info[1]['ismine']) ? $this->client->move($this->accountInfo(associated, $from), $address, $ammount) : $this->client->sendfrom($this->accountInfo(address, $from), $address, $ammount);
		}
	}
	
	// This is misc info not required for normal operation. It should be self
	// desciptive. You should also be able to use it in the following manner:
	// - $myClient = new bitcoinClient("http://root:@localhost:8332");
	// - echo myClient->miscInfo(numNodes);
	public function miscInfo($type)
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
				return $this->client->getinfo();
		}
	}
	
	// Arg1 is Address or Account
	// Arg2 is if we display some or all info
	public function accountInfo($action, $arg1 = null, $arg2 = false)
	{
		switch($action)
		{
			case address:
				if($arg1 == null){
					// We cannot miss the $arg1 argument
					return error;
				}
				else{
					return (!$arg2) ? $this->client->getaccountaddress($arg1) : $this->client->getaddressesbyaccount($arg1);
				}
			case associated:
				return ($arg1 == null) ? error : $this->client->getaccount($arg1);
			case balance:
				return ($arg1 == null) ? $this->client->getbalance() : $this->client->getbalance($arg1);
			case newAddress:
				return ($arg1 == null) ? $this->client->getnewaddress() : $this->client->getnewaddress($arg1);
			case listAccounts:
				return $this->client->listaccounts();
			case validate:
				return ($arg1 == null) ? error : $this->client->validateaddress($arg1);
			default:
				return error;
		}
		// ~/.bitcoin/bitcoin.conf
	}
}
?>