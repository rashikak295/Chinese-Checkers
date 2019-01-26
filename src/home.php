<?php

session_start();
$_SESSION['logged_in'] = true; 
$_SESSION['last_activity'] = time(); 
$_SESSION['expire_time'] = 1*60; 

if(!isset($_SESSION['uname'])){
  header('Location:./SignIn.php');
}



?>
<!DOCTYPE html>
<html>
<head>
  <title></title>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/core.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.0.4/socket.io.js"></script>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
  <link rel="stylesheet" href="../styles/style_board.css">
  <link rel="stylesheet" type="text/css" href="../styles/style.css" />
  <style type="text/css">
    #game{
      display: none;
      cursor: pointer;
      max-width: 80vw;
      max-height: 500px;
    }
    body{
      font-size:1.7em;
    }
    select{
      font-size:1em;
    }
  </style>
</head>
<body style="display: none;">
  <div class="col-lg-12 col-md-12">
    <div class="col-lg-4 col-md-4" style="display: inline-block;font-size: 50%;">
      
      <span style="font-size:3ex;">Hello, <?php echo $_SESSION['uname']; ?>!</span><br>
    <button type="button" id="logoutbutton" style="background-color: rgba(255, 255, 255, 0.3);color: white; border: rosybrown;border-style: solid; border-width: 1px;width: 100px;height: 50px;" class="btn" onclick="pleaseonLogout()">Logout</button>
    
  </div>


   <form class="col-lg-4 col-md-4" style="display: inline-block;">
      
        Select your opponent:<br>
        
          <select id="users" style="width: 20vw;">

          </select>
          <input type="submit" name="submit" value="Play" id="connectionreq" style="display: inline; width: 20vw;"  class="col-lg-3"/>
        
    </form>
    <table class="col-lg-4 col-md-4">
      <tr>
        <td>Win</td>
        <td>Loss</td>
        <td>Draw</td>
      </tr>
      <?php
        include './connection/connection.php';
        $query = $pdo->prepare('select win,loss,draw from member where uname=?');
        $query->bindValue(1,$_SESSION['uname']);
        $query->execute();
        if ($query->rowCount()) {
            // output data of each row
            while($row =  $query->fetch(PDO::FETCH_ASSOC)) {
                //print_r($row) ;
                ?>
                <tr>
                <?php
                echo "<td>". $row["win"]. "</td><td>". $row["loss"]. "</td><td> " . $row["draw"] . "</td>";
                ?>
                </tr>
                <?php
            }
        } else {
            echo "0 results";
        }

      ?>
    </table>
  </div>
  <center>
    <!--Let's build a game!
    <button name="btnfun1" onClick='location.href="?button1=1"'>EMIT</button>-->
   
    <section id="game">
      <h1>Chinese Checkers</h1>
    <div class="board">
      <div id="0"></div>
      <div id="1"></div>
      <div id="2"></div>
      <div id="3"></div>
      <div id="4"></div>
      <div id="5"></div>
      <div id="6"></div>
      <div id="7"></div>
      <div id="8"></div>
      <div id="9"></div>
      <div id="10"></div>
      <div id="11"></div>
      <div id="12"></div>
      <div id="13"></div>
      <div id="14"></div>
      <div id="15"></div>
      <div id="16"></div>
      <div id="17"></div>
      <div id="18"></div>
      <div id="19"></div>
      <div id="20"></div>
      <div id="21"></div>
      <div id="22"></div>
      <div id="23"></div>
      <div id="24"></div>
      <div id="25"></div>
      <div id="26"></div>
      <div id="27"></div>
      <div id="28"></div>
      <div id="29"></div>
      <div id="30"></div>
      <div id="31"></div>
      <div id="32"></div>
      <div id="33"></div>
      <div id="34"></div>
      <div id="35"></div>
      <div id="36"></div>
      <div id="37"></div>
      <div id="38"></div>
      <div id="39"></div>
      <div id="40"></div>
      <div id="41"></div>
      <div id="42"></div>
      <div id="43"></div>
      <div id="44"></div>
      <div id="45"></div>
      <div id="46"></div>
      <div id="47"></div>
      <div id="48"></div>
      <div id="49"></div>
      <div id="50"></div>
      <div id="51"></div>
      <div id="52"></div>
      <div id="53"></div>
      <div id="54"></div>
      <div id="55"></div>
      <div id="56"></div>
      <div id="57"></div>
      <div id="58"></div>
      <div id="59"></div>
      <div id="60"></div>
      <div id="61"></div>
      <div id="62"></div>
      <div id="63"></div>
    </div>
    <p id="instructions">Red starts!</p>  
    </section>
  </center>
</body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script type="text/javascript">

  var socket = io.connect("http://localhost:3002");
//******************************************************************//
  // Getting ready
  var config = getInitconfig();
  window.config = config;
  //initializeBoard(false);
  var game_object;
  // Start game
  $('.board > div').click(function() {
    // Select cell
    if ($('div.selected').length == 0) {
      var selectedIndex = $(this).attr('id');
      var hasBounced = false;
      window.hasBounced = hasBounced;
      window.selectedIndex = selectedIndex;
      // Check if it's the player's dot that's selected
      if (config.board[selectedIndex].empty == false) {
        if (isSelectable(selectedIndex)) {
          config.board[selectedIndex].selected = true;
          $('#' + selectedIndex).toggleClass('selected');
        }
        else {
          if (config.player%2 == 0) {
            $('#instructions').text('Select only red cells!');
          } else {
            $('#instructions').text('Select only blue cells!');
          }
        }
      } else {
        $('#instructions').text('Too far!');
      }
    } else if ($('div.selected').length == 1) {
      var targetIndex = $(this).attr('id');
      window.targetIndex = targetIndex;
      occupyCell(targetIndex);
    }
  });

  // Functions
  function isSelectable(selectedIndex) {
    if (
      (config.player % 2 == 0 && config.board[selectedIndex].red == true)
      || (config.player % 2 == 1 && config.board[selectedIndex].blue == true)
    ) {
      return true;
    } else {
      return false;
    }
  }

  function isAccessible(destination, origin) {
    if (
      config.board[destination].empty == true
      && destination != origin
      // Checking columns
      && destination%8 >= origin%8-1
      && destination%8 <= origin%8+1
      // Checking lines
      && Math.floor(destination/8) >= Math.floor(origin/8)-1
      && Math.floor(destination/8) <= Math.floor(origin/8)+1
    ) {
      return true;
    } else {
      return false;
    }
  }

  function occupyCell(targetIndex) {
    var willBounce = false;
    for (i=0; i < canBounce(window.selectedIndex).length; i++) {
      if (canBounce(window.selectedIndex)[i] == window.targetIndex) {
        willBounce = true;
      }
    }
    // Unselect dot
    if (window.targetIndex == window.selectedIndex) {
      config.board[window.selectedIndex].selected = false;
      $('.selected').removeClass('selected');
      $('#instructions').text('Okay, which one then?');
      if (window.hasBounced == true) {
        game_object = {
          "Board":config.board,
          "Turn":php_var,
        }
        $('#game').css( 'pointer-events', 'none');
        socket.emit('board config',game_object);
        document.getElementById("hiddenVal").value = config.board;
        nextPlayer();

      }
    } else if (willBounce == true) {
      // First jump
      if (config.player % 2 == 0) {
        $('#' + window.targetIndex).html(config.redDot);
        config.board[window.targetIndex].red = true;
        config.board[window.targetIndex].empty = false;
      } else {
        $('#' + window.targetIndex).html(config.blueDot);
        config.board[window.targetIndex].blue = true;
        config.board[window.targetIndex].empty = false;
      }
      // Free previous cell
      config.board[window.selectedIndex].selected = false;
      config.board[window.selectedIndex].empty = true;
      config.board[window.selectedIndex].red = false;
      config.board[window.selectedIndex].blue = false;
      $('.selected').html('');
      $('.selected').removeClass('selected');
      // Recursive bounce
      if (canBounce(window.targetIndex).length > 0) {
        $('#' + window.targetIndex).addClass('selected');
        window.selectedIndex = window.targetIndex;
        config.board[window.selectedIndex].selected = true;
        $('#instructions').text('Bounce again? Unselect if no');
        window.hasBounced = true;
      } else {
        game_object = {
          "Board":config.board,
          "Turn":php_var,
        }
        $('#game').css( 'pointer-events', 'none');
        socket.emit('board config',game_object);
        nextPlayer();
      }
    } else if (isAccessible(window.targetIndex, window.selectedIndex) && willBounce == false) {
      if (config.player % 2 == 0) {
        $('#' + window.targetIndex).html(config.redDot);
        config.board[window.targetIndex].red = true;
        config.board[window.targetIndex].empty = false;
      } else {
        $('#' + window.targetIndex).html(config.blueDot);
        config.board[window.targetIndex].blue = true;
        config.board[window.targetIndex].empty = false;
      }
      // Free previous cell
      config.board[window.selectedIndex].selected = false;
      config.board[window.selectedIndex].empty = true;
      config.board[window.selectedIndex].red = false;
      config.board[window.selectedIndex].blue = false;
      game_object = {
          "Board":config.board,
          "Turn":php_var,
        }
      $('#game').css( 'pointer-events', 'none');
      socket.emit('board config',game_object);
      nextPlayer();
      $('.selected').html('');
      $('.selected').removeClass('selected');
    } else {
      $('#instructions').text('Not possible!');
    }
  }

  function canBounce(origin) {
    var possibleBounces = new Array();
    function xDifference(index) {
      return index%8 - origin%8;
    }
    function yDifference(index) {
      return Math.floor(index/8) - Math.floor(origin/8);
    }
    window.xDifference = xDifference;
    window.yDifference = yDifference;
    for (closeCell=0; closeCell<64; closeCell++) {
      if (
        closeCell != origin
        && config.board[closeCell].empty == false
        // Checking columns
        && closeCell%8 >= origin%8-1
        && closeCell%8 <= origin%8+1
        // Checking lines
        && Math.floor(closeCell/8) >= Math.floor(origin/8)-1
        && Math.floor(closeCell/8) <= Math.floor(origin/8)+1
      ) {
        for (farCell=0; farCell<64; farCell++) {
          if (
            // Check distance
            isAccessible(farCell, closeCell)
            // Check direction
            && xDifference(farCell) == (xDifference(closeCell)) * 2
            && yDifference(farCell) == (yDifference(closeCell)) * 2
            // Prevent moves back to origin
            && farCell != window.selectedIndex
          ) {
            possibleBounces.push(farCell);
          }
        }
      }
    }
    return possibleBounces;
  }
  window.canBounce = canBounce;

  function nextPlayer() {
    
    window.hasBounced = false;
    if (hasWon() != 'none') {
      if (hasWon() == 'red') {
        $('#instructions').text('Red wins! Congratulations!');
        socket.emit('Game End',"red");
      } else {
        $('#instructions').text('Blue wins! Congratulations!!');
        socket.emit('Game End',"blue");
      }
      // Reset game
      getInitconfig();
      initializeBoard(false);
    } else if (config.player%2 == 0) {
      $('#instructions').css('color', '#ad0101');
      $('#instructions').text('Red\'s turn!');
    } else {
      $('#instructions').css('color', '#3F51B5');
      $('#instructions').text('Blue\'s turn!');
    }

  }

  function getInitconfig() {
    var board = new Array(64);
    var player = 0, j=4, k=0;
    var blueDot = "<div class='dotBorder'><div class='blueDot'></div></div>";
    var redDot = "<div class='dotBorder'><div class='redDot'></div></div>";
    for (i=0; i<64; i++) {
      board[i] = {};
      board[i].red = false;
      board[i].blue = false;
      board[i].empty = true;
      board[i].selected = false;
      board[i].accessible = false;
    }
    return {
      board: board,
      player: player,
      blueDot: blueDot,
      redDot: redDot
    }
  }

  function initializeBoard(boardAvail,nextplayer) {
    if(boardAvail == false){
      var k=5, l=0;
      // Clean up first
      for (i=0; i<64; i++) {
        config.board[i].empty = true;
        config.board[i].red = false;
        config.board[i].blue = false;
        $('#' + i).html('');
      }
      for(i=0; i<64; i++) {
        k--;
        for (j=0; j<k; j++) {
          config.board[i+j].empty = false;
          config.board[i+j].red = true;
          $('#' + (i+j)).html(config.redDot);
        }
        i += 7;
      }
      for(i=39; i<64; i++) {
        l++;
        for (j=0; j<l; j++) {
          config.board[i-j].empty = false;
          config.board[i-j].blue = true;
          $('#' + (i-j)).html(config.blueDot);
        }
        i += 7;
      }
   }
   else{
    //alert("YO");
     for (i=0; i<64; i++) {
        config.board[i].empty = true;
        config.board[i].red = false;
        config.board[i].blue = false;
        $('#' + i).html('');
      }
    for (i=0; i<64; i++) {
        if(boardAvail[i].red)
          $('#' + i).html(config.redDot);
        if(boardAvail[i].blue)
          $('#' + i).html(config.blueDot);
      }
    config.board = boardAvail;
   }
   if(nextplayer){
      console.log("Next player");
      config.player++;
      nextPlayer();
    }
   return config.board;
  }
  window.initializeBoard = initializeBoard;
  function setBoard(data){
    console.log(data);
    config.board = data;
  }
  window.setBoard = setBoard;
  function hasWon() {
    var gameOver = true, k=5, l=0, winner = '';
    for(i=0; i<64 && gameOver == true; i++) {
      k--;
      for (j=0; j<k; j++) {
        if (config.board[i+j].blue == false) {
          gameOver = false;
        }
      }
      i += 7;
    }
    if (gameOver == true) {
      return 'blue';
    } else {
      for(i=39; i<64; i++) {
        l++;
        for (j=0; j<l; j++) {
          if(config.board[i-j].red == false) {
            gameOver = false;
          }
        }
        i += 7;
      }
      if (gameOver == true) {
        return 'red';
      }
    }
    return 'none';
  }
//******************************************************************//
  var php_var = '<?php echo $_SESSION["uname"];?>';
  console.log(php_var);
  socket.emit('send message',php_var);
  socket.on('new message', function(data){
    console.log(data.msg);
  });
  socket.emit('new user',php_var);
  
  socket.on("get users",function(data){
    console.log(data);
    var uniqueNames = [];
    $.each(data, function(i, el){
        if($.inArray(el, uniqueNames) === -1) uniqueNames.push(el);
    });
    var html = ""
    $('select').html('');
    for (var i = 0; i < uniqueNames.length; i++) {
      if(uniqueNames[i] != php_var)
        html += '<option value="'+uniqueNames[i]+'">'+uniqueNames[i]+'</option>';
    }
    $("select").html(html);
  });
  $('#connectionreq').click(function(){
    socket.emit('connection request',{"player1":php_var,"player2":$('select').val()});
  });

  socket.on('catch request',function(data){
    if (data.player2 == '<?php echo $_SESSION['uname']; ?>') {
      var what = confirm('Don you want to accept the request?');
      if (what == true){
        //alert('You selected yes');
        var board = new Array(64);
        board = initializeBoard(false,false);
        game_object = {

          "Board":board,
          "Turn":php_var,
          "Opponent":data.player1,
        }
        socket.emit('initial config',game_object,function(data){
            if(data){
              $("#game").show();
            }
        });
      }
      else{
        alert('You selected no');
      }
    }
  });
  socket.on('initial board',function(data){
      var board = new Array(64);
      $('#game').show();
      if(php_var == data.Turn){
          $('#game').css( 'pointer-events', 'none');
      }
      board = initializeBoard(false,false);
  });
  socket.on('catch board',function(data){
      $('#game').show();
      if(php_var != data.Turn){
        $('#game').css( 'pointer-events', 'auto');
      }
      initializeBoard(data.Board,true);
  });
  socket.on("Game",function(data){
    console.log("herehehrheh")
    if(data != php_var){
      confirm("Other person left the game!\nYou Won!!")
      window.location.reload(); 
    }

  })
  $("body")[0].style.display = "block";
  function pleaseonLogout(){
    socket.emit("Logout End",php_var)
    document.location.replace("./logout.php")
  }

</script>
</html>
  <?php 
  if( $_SESSION['last_activity'] < time()-$_SESSION['expire_time']) {
      echo '<script type="text/javascript">',
       'pleaseonLogout();',
       '</script>';
  } else{ 
      $_SESSION['last_activity'] = time(); 
  }
  ?>