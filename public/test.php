<?php

$iphone = strpos($_SERVER['HTTP_USER_AGENT'],"iPhone");
$android = strpos($_SERVER['HTTP_USER_AGENT'],"Android");
$ipod = strpos($_SERVER['HTTP_USER_AGENT'],"iPod");

if ($android == true)
{
header('Location: market://details?id=com.boogiespot.gambierhotel');
}
else if ($iphone || $ipod == true)
{
header('Location: https://itunes.apple.com/au/app/mount-gambier-hotel/id639503073?mt=8');
}
else
{
header('Location: http://www.matthewshotels.com.au/mountgambierhotel');
}
?>