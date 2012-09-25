<?php
//$stdin = fopen('php://stdin', 'r');

echo "Server: ";
$srvaddr = trim(fgets(STDIN));

//$fp = stream_socket_client("udp://96.22.77.202:1113", $errno, $errstr);
$fp = stream_socket_client("udp://$srvaddr:1113", $errno, $errstr);
stream_set_blocking($fp, FALSE) or die ('Failed to disable server blocking');


if (!$fp)
{
    echo "ERROR: $errno - $errstr<br />\n";
}
else
{
    echo "Username: ";
    $username = trim(fgets(STDIN));
    stream_set_blocking(STDIN, FALSE) or die ('Failed to disable stdin blocking');

    $welcome = "$username has joined the room";
    $write = fwrite_with_retry($fp, $welcome);
    
    while (true)
    {
        $line = trim(fgets(STDIN));
        $write = fwrite_with_retry($fp, $line);

        echo fread($fp, 300);
    }
}
fclose($fp);

function fwrite_with_retry($sock, &$data)
{
    $bytes_to_write = strlen($data);
    $bytes_written = 0;

    while ( $bytes_written < $bytes_to_write )
    {
        if ( $bytes_written == 0 ) {
            $rv = fwrite($sock, $data);
        } else {
            $rv = fwrite($sock, substr($data, $bytes_written));
        }

        if ( $rv === false || $rv == 0 )
            return( $bytes_written == 0 ? false : $bytes_written );

        $bytes_written += $rv;
    }

    return $bytes_written;
}
