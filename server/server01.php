<?php

class ChatServer
{
    private $clients = array();
    private $commands = null;
    
    public function __construct()
    {
        $this->commands = new commands();
        $this->_init();
    }
    
    private function _init()
    {
//        $socket = stream_socket_server("udp://192.168.42.2:1113", $errno, $errstr, STREAM_SERVER_BIND);
        $socket = stream_socket_server("udp://127.0.0.1:1113", $errno, $errstr, STREAM_SERVER_BIND);
        if (!$socket) {
            die("$errstr ($errno)");
        }

        do
            {
            $pkt = stream_socket_recvfrom($socket, 255, 0, $peer);

            $this->clients[substr($peer, 0, 14)] = array('socket' => $socket, 'peer' => $peer);

            $message = $this->_parse_request($pkt);
            $this->_broadcast($message, $peer);
        } while ($pkt !== false);
    }
    
    public function _parse_request($pkt)
    {
        if (substr($pkt, 0, 1) == '/')
        {
            $command = substr($pkt, 1, strpos($pkt, ' '));
            return $this->commands->$command(explode(' ', substr($pkt, strpos($pkt, ' '))));
        }
        else
        {
           return $pkt;
        }
    }

    private function _broadcast($message, $from)
    {
        foreach ($this->clients AS $client)
        {
            $messageSend = '['.substr($from, 0, 14).'] ' . $message."\n";
            stream_socket_sendto($client['socket'], $messageSend, 0, $client['peer']);
        }
    }
}