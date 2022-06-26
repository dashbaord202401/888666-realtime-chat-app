<?php
namespace ChatApp;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
class Chat implements MessageComponentInterface
{
    protected $clients;
    protected $activeUsers=array();
    
    function __construct()
    {
        $this->clients = new \SplObjectStorage;
        echo "Server Started \n";
    }

    // Called when the socket is opened
    public function onOpen(ConnectionInterface $conn)
    {
        // Add  connection to the clients object
        $this->clients->attach($conn);
        echo "New Connection : ID : ({$conn->resourceId})\n";
        
    }
    // Called when the message is sent
    public function onMessage(ConnectionInterface $from, $msg)
    {
        $new_msg =json_decode($msg);
        $type = $new_msg->type;
        $fromUser = $new_msg->from;

        // new connection
        if($type == "new") { 

            // Adding used id to active users array
            $this->activeUsers[$fromUser] = $from;
            // If it already exists
            if(in_array($from,$this->activeUsers)){
                print_r("existing user");
            }
            
        } else {
            $data = $new_msg->data;
            $toUser = $new_msg->to;
            
            echo "to user : $toUser";
            echo "message : $data";

            try{
                if($this->activeUsers[$toUser]==true)
                {
                    // Sending message to the Receiver
                    $this->activeUsers[$toUser]->send(json_encode($new_msg));
                }
                else
                {
                    print_r("connection not available");
                }
            }
            catch(Exception $e)
            {
                print_r("err : ",$e->getMessage());
            }  
        }        
    }
    // Called when rhe connection closes
    public function onClose(ConnectionInterface $conn)
    {
        // Remove from Clients Object
        $this->clients->detach($conn);
        // Remove from active users array
        unset($this->activeUsers[$conn->resourceId]);
        echo "User {$conn->resourceId} has disconnected\n";
        
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
    
}
?>