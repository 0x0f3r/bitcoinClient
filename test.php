<?php
header('Content-type: application/txt');
require_once('bitcoinClient.php');
$bitcoin = new bitcoinClient("http://root:root@localhost:8332");
if(isset($_GET['task'])){
  switch($_GET['task']){
     case numBlocks:
      echo $bitcoin->miscInfo(numBlocks);
      break;

     case numNodes:
      echo $bitcoin->miscInfo(numNodes);
      break;

     case diff:
      echo $bitcoin->miscInfo(diff);
      break;

     case balance:
      echo $bitcoin->accountInfo(balance, "bitcoinClient");
      break;
     
     case listAccounts:
      foreach($bitcoin->accountInfo(address, "bitcoinClient", true) as $address)
      {
	echo $address, ";";
      }
      break;
  }
}
?>