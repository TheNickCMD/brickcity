<?php
$ads = true;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$pageName = "Communities";
require("/var/www/html/site/header.php");
require("/var/www/html/api/community/class.php");
?>
<div class="col-1-1">
  <a href="/community/" style="color:#000000">
  	<i class="fas fa-chevron-left"></i> Return to Groups
  </a>
</div>
<br><br>
<div class="page-title">
  All Groups
</div>
<?php
$popular = $bop->query("SELECT * FROM community ORDER BY members DESC LIMIT 0, 5000", [], true);
foreach($popular as $community)
{
  $members = $bop->query("SELECT COUNT(*) FROM community_member WHERE cid=? AND banned=0", [$community['id']])->fetchColumn();
  $owner = $com->getOwner($community['id']);
  $founder = $bop->get_user($community['founder']);
  ?>
  <div class="card border">
    <div class="col-2-12">
      <a href="/community/view/<?=$community['id']?>">
      <img src="<?php
      $adminBadge = "";
      switch($community['pending'])
      {
        case "0":
        ?>
https://storage.vertineer.com/thumbnails/awaiting.png<?php
        break;
        case "1":
        ?>
https://storage.vertineer.com/community/<?=$community['cache']?>.png<?php
        break;
        case "2":
        ?>
https://storage.vertineer.com/thumbnails/declined.png<?php
        break;
      }
                  if($community['verified'] == 1) {
              $adminBadge = '<img src="https://storage.vertineer.com/verified.png" width="16" title="This group has been verified by Vertineer." style="vertical-align:middle">';
            }
      ?>" class="image">
    </a>
    </div>
    <div class="col-6-12">
      <div class="item-name offsale disabled">
        <a style="color:#505050;" href="/community/view/<?=$community['id']?>"><?=$community['name']?></a> <?=$adminBadge?>
      </div>
      <p style="font-size:12px;"><?=substr(htmlentities($community['desc']), 0, 2500)?></p>
    </div>
    <div class="col-4-12">
      Created: <b><?=substr($community['created'], 5, 2) . "/" . substr($community['created'], 8, 2) . "/" . substr($community['created'], 0, 4)?></b>
      <br>
      Founder: <a href="/profile/<?=$founder->id?>"><?=htmlentities($founder->username)?></a>
      <br>
      Current Owner: <a href="/profile/<?=$owner->id?>"><?=htmlentities($owner->username)?></a>
      <br>
      Members: <?=$members?>
    </div>
  </div>
  <?php
}
?>

<?php
$bop->footer();
?>
