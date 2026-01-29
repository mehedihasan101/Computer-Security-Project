<div class="reply">
    <div class="comment-header">
        <!-- Ensure the profile picture URL is correctly passed and not empty -->
        <img src="<?php echo isset($commentpost['profilepic']) ? $commentpost['profilepic'] : 'images2\default_user.png'; ?>" 
             alt="Profile Photo" 
             class="profile-photo"> <!-- Profile photo -->
        <h4 class="comment-name"><?php echo isset($commentpost['name']) ? $commentpost['name'] : 'Anonymous'; ?></h4>
    </div>
    
    <!-- Ensure date and comment exist -->
    <p><?php echo isset($commentpost['date']) ? $commentpost['date'] : 'No Date'; ?></p>
    <p><?php echo isset($commentpost['comment']) ? $commentpost['comment'] : 'No Comment'; ?></p>

    <!-- Reply button for the nested reply -->
    <?php $reply_id = isset($commentpost['id']) ? $commentpost['id'] : 0; ?>
    <button class="reply" onclick="reply(<?php echo $reply_id; ?>, '<?php echo isset($commentpost['name']) ? $commentpost['name'] : 'Anonymous'; ?>');">Reply</button>


    <?php
    // Run the SQL query to get replies
    unset($commentposts);
    $commentposts = mysqli_query($conn, 
        "SELECT u.profilepic as profilepic, c.id, c.name, c.email, c.date, c.comment, c.reply_id
         FROM comment_box as c 
         JOIN user as u ON c.email = u.email 
         WHERE reply_id = $reply_id"
    );
    
    // Check if there are any replies
    if(mysqli_num_rows($commentposts) > 0) {
        // Loop through each reply and include reply.php
        foreach($commentposts as $commentpost) {
            // Only include reply.php if there is data
            if (!empty($commentpost)) {
                require __DIR__ . '/reply.php'; // Ensure correct path
            }
        }
    }
    ?>
</div>
