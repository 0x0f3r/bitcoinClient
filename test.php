<?php
require_once('bitcoinClient.php');
$bitcoin = new bitcoinClient("http://root:root@localhost:8332");
echo $bitcoin->getMiscInfo(numBlocks), "<br />";
echo $bitcoin->getMiscInfo(numNodes), "<br />";
echo $bitcoin->getMiscInfo(diff), "<br />";
echo $bitcoin->accountActions(getBalance), "<br />";
foreach($bitcoin->accountActions(getAddress, "Me", true) as $address)
{
	echo $address, "<br />";
}

?>