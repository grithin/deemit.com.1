<div class="section">
	<div class="title">
		Comments
	</div>
	<div class="content">
		<p>
			<a class="button" href="../comment?_id=<?=$page->id?>">Add Comment</a>
		</p>
		<div id="comments">
<?	if($page->comments){?>
<?		foreach($page->comments as $comment){?>
			<div class="comment" style="margin-left:<?=$comment['order_depth']*5?>px;" id="comment-<?=$comment['id']?>" data-commentId="<?=$comment['id']?>">
				<div class="header">
					<span class="title"><?=htmlspecialchars($comment['title'])?></span> (<?=DH::time($comment['time_created'])?>)
					by <?=DH::user($comment['user_name'],$comment['user_id'])?>
					<a href="../comment?_id=<?=$page->id?>&parent=<?=$comment['id']?>" title="reply">[Reply]</a>
					
						<span class="commentStat" data-type="significance"><?=$comment['significance']?></span>
						<span class="commentStat" data-type="enjoyment"><?=$comment['enjoyment']?></span>
							<?=DH::voteOnComment($comment['id'])?>
					
				</div>
				<div class="text"><?=$comment['text']?></div>
			</div>
<?		}?>
<?	}?>
		</div>
	</div>
</div>
