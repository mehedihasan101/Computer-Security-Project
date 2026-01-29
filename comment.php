<div class="comment">
  <div class="comment-header">
    <img src="<?php echo $commentpost['profilepic']; ?>" alt="Profile Photo" class="profile-photo"> <!-- Profile photo -->
    <h4 class="comment-name"><?php echo $commentpost['name']; ?></h4> <!-- Name beside the profile photo -->
  </div>
  <p><?php echo $commentpost['date']; ?></p>
  <p><?php echo $commentpost['comment']; ?></p>
  <?php $reply_id = $commentpost['id']; ?>
  <button class="reply" onclick="reply(<?php echo $reply_id; ?>, '<?php echo $commentpost['name']; ?>');">Reply</button>


  <?php
  unset($commentposts);
  $commentposts = mysqli_query($conn, "SELECT * FROM comment_box WHERE reply_id = $reply_id");
  if(mysqli_num_rows($commentposts) > 0) {
    foreach($commentposts as $commentpost){
      require 'reply.php';
    }
  }
  ?>
  
  <br>
</div>
