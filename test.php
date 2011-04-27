<?php
require_once('bitcoinClient.php');
$bitcoin = new bitcoinClient("http://root:root@localhost:8332");
echo $bitcoin->miscInfo(numBlocks), "<br />";
echo $bitcoin->miscInfo(numNodes), "<br />";
echo $bitcoin->miscInfo(diff), "<br />";
echo $bitcoin->accountInfo(balance, "bitcoinClient"), "<br />";
foreach($bitcoin->accountInfo(address, "bitcoinClient", true) as $address)
{
	echo $address, "<br />";
}

?>