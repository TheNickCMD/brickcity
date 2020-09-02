<?php
require("/var/www/html/site/bopimo.php");

if(!$bop->logged_in())
{
  die(header('location: /home'));
}
if(isset($_POST['req']))
{
  require("/var/www/html/api/rest.php");
  switch($_POST['req'])
  {
    /*
    if the request is bio
    */
    case "bio":
      if($bop->antiJake($_POST['bio']))
      {
        $rest->error('Error changing bio: All fields are required.');
        die();
      }
      $bio = $_POST['bio'];
      if(strlen($bio) < 5 || strlen($bio) > 4000)
      {
        $rest->error('Error changing bio: Bio must be 5-4000 characters long.');
        die();
      }
      if($bop->tooFast(5))
      {
        $rest->error('Error changing bio: You are performing actions too fast. Please wait.');
        die();
      }
      $user = $bop->local_info();
      $bop->update_("users", ["bio" => $bio], ["id" => $user->id]);
      $bop->updateFast();
      $rest->success();
      break;
    case "username":
      if($bop->antiJake($_POST['username']))
      {
        $rest->error('Error changing username: All fields are required.');
        die();
      }
      if(strlen($_POST['username']) < 3 || strlen($_POST['username']) > 20)
      {
        $rest->error('Error changing username: Username must be 3-20 characters long.');
        die();
      }
      if($bop->tooFast(5))
      {
        $rest->error('Error changing username: You are performing actions too fast. Please wait.');
        die();
      }
      if($bop->local_info()->bop < 100)
      {
        $rest->error('Error changing username: You do not have enough Vix.');
        die();
      }
      if($bop->user_exists($_POST['username'])) {
        $rest->error('Error changing username: Username already taken.');
        die();
      }
      if(!ctype_alnum($_POST['username'])) {
        $rest->error('Error changing username: Username can only contain alphanumeric characters.');
        die();
      }
      $bop->insert("username_changes", [
        "user" => $_SESSION["id"],
        "old_username" => $bop->local_info()->username,
        "new_username" => $_POST['username']
      ]);
      $bop->update_("users", ["username" => $_POST['username'], "bop" => $bop->local_info()->bop - 100], ["id" => $_SESSION['id']]);
      $bop->updateFast();
      $rest->success();
    break;
  }



  /*
  actual cool page
  */
} else {
  require("/var/www/html/site/header.php");
  $user = $bop->local_info();
  ?>
  <style>
    hr {
      border:1px solid #d3d3d3;
    }
    .cool-input {
      margin-bottom:10px;
    }
  </style>
  <div class="banner success hidden">
    [Success]
  </div>
  <div class="banner danger hidden">
    [Error]
  </div>
  <div class="content">
    <div class="col-1-1">
		 <div class="page-title">Settings</div>
      <div class="col-2-12">
        <div class="shop-button current" data-category="1">Info</div>
        <div class="shop-button" data-category="2">Security</div>
        <div class="shop-button" data-category="3">Payments</div>
      </div>

      <!-- Your Info !-->
      <span id="1">
        <div class="col-10-12">
          <div class="card b" id="1">
            <div class="top">
              Info
            </div>
          </div>
          <div class="card border">
            <b>Your Bio:</b>
            <br>
            <div class="col-1-1 cool-input" style="padding-right:0px;">
              <textarea id="bio" style="height:300px;resize:none;width:100%;"><?=htmlentities($user->bio)?></textarea><button style="float:right;" class="button success" req="bio">Update Bio</button>
            </div>
          </div>
          <div class="card border">
            <b>Email (<?=($user->verified == 1) ? '<font color="green">Verified</font>' : '<font color="red">Not Verified</font>'?>)</b>
            <br>
            <form id="submitEmail" method="POST" action=""><input type="email" id="email" placeholder="Email Here" class="width-100" value="<?=htmlentities($user->email)?>"><input type="submit" value="Update" class="button success width-100"></form>
          </div>
          <div class="card border">
            <b>Change Username (100 Vix)</b>
            <br>
            <form id="submitUsername" method="POST"><input type="text" id="username" placeholder="New username here" class="width-100"><button class="button success width-100" req="username">Update</button></form>
          </div>
        </div>
      </span>

      <!-- Security !-->
      <span id="2" class="hidden col-10-12">
        <div class="card b">
          <div class="top">
            Security
          </div>
        </div>
        <div class="card border">
          <b>Password</b>
          <br>
          <div class="col-1-1"><button id="resetPassword" class="width-100 button success">Send email to change password.</button></div>
        </div>
		<div class="card border">
          <b>Secure Sign Out</b>
          <br>
          <div class="col-1-1"><button href="https://vertineer.com/account/logout.php" class="width-100 button success">Sign out of all other sessions.</button></div>
        </div>
      </span>
      <!-- Privacy !-->
      <span class="hidden col-10-12" id="3">
        <div class="card b">
          <div class="top">
            Payments
          </div>
        </div>

        <?php
        $transactions = $bop->query("SELECT * FROM payment_logs WHERE user=? ORDER BY time DESC", [$localUser->id], true);
        if(count($transactions) == 0)
        {
          ?>
          <div class="card border">You have made no transactions.</div>
          <?php
        } else {
          foreach($transactions as $row)
          {
            ?>
            <div class="card border">You bought <?=$row['amount']?> <?=$bop->timeAgo($row['time'])?></div>
            <?php
          }
        }
        ?>
      </span>
      </div>
    </div>
  </div>
  <script src="account.js?<?=rand()?>"></script>
  <?php
  $bop->footer();
}
?>
