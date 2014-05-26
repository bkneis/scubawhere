<?php
echo date('l jS \of F Y H:i:s A');
?>

<?php
/*
$ch = curl_init( 'http://scubawhere.com/company' );

$strCookie = 'scubawhere_session=' . $_COOKIE['scubawhere_session'] . '; path=/';

curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
curl_setopt( $ch, CURLOPT_COOKIE, $strCookie );

$result = curl_exec( $ch );

curl_close( $ch );

$result = json_decode( $result );
if( isset( $result->id ) ) {
    echo "logged in<br><br>".json_encode($result);
}
else {
	echo "not logged in<br><br>".json_encode($result);
}
*/
?>
