<?php
//include auth_session.php file for authetication
//include("auth_session.php");
//include("db/active_users.php");


?>
<!DOCTYPE html>
<html>
  <head>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <style>
      input,
      button,
      p {
        padding: 10px;
      }
    </style>
  </head>
  <body>
  
    <input type="text" id="message" autocomplete="off" />
    <button onclick="transmitMessage()">Send</button>

    <div>
      <h2> <?php echo $_SESSION['username']; ?></h2>
      <ul id="messages"></ul>
    </div>
    
  </body>


  <script>

      // Create a new WebSocket.
      let socket = new WebSocket("ws://localhost:2222/");

      // Define the message
      let message = document.getElementById("message").value;
      let from = "Anto";
      let to = "Ak";
      function transmitMessage() {
        message = document.getElementById("message").value;
        let data = {
          from : from,
          data: message,
          to: to,
          type: "message",
          
        };
        socket.send(JSON.stringify(data));
      };

      socket.onopen = function (ev) {
        let data = {
          from : resp,
          to: to,
          data: 'onOpen',
          type: "new",
        };
        socket.send(JSON.stringify(data));
        console.log("Connected to server ");
      };

      socket.onmessage = function (e) {
        let RecivedData = JSON.parse(e.data);
        console.log("RecivedData>>>", RecivedData)
        $('#messages').append(`<li> <strong>${RecivedData.from} :</strong> ${RecivedData.data}</li>`);
      };
    </script>
</html>
