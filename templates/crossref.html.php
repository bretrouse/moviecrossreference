
<div id="correlation">

	<p class="header">
		<span class="left"><a href="http://www.imdb.com/<?php echo $item1->getType() == MCRReferenceObject::MOVIE ? 'title/tt' : 'name/nm'; echo $item1->getId(); ?>/" target="_blank"><?php echo $item1->getName(); ?></a></span>
		<span class="middle">x</span>
		<span class="right"><a href="http://www.imdb.com/<?php echo $item2->getType() == MCRReferenceObject::MOVIE ? 'title/tt' : 'name/nm'; echo $item2->getId(); ?>/" target="_blank"><?php echo $item2->getName(); ?></a></span>
	</p>

<?php if( $matches && ( $item1->getType() == $item2->getType() ) ): ?>
	<?php foreach( $matches as $match ): ?>
	<p class="match">
		<span class="left"><?php echo $ref1[$match][1]; ?></span>
		<span class="middle"><a href="http://www.imdb.com/<?php echo $item1->getType() == MCRReferenceObject::PERSON ? 'title/tt' : 'name/nm'; echo $match; ?>/" target="_blank"><?php echo $ref1[$match][0]; ?></a></span>
		<span class="right"><?php echo $ref2[$match][1]; ?></span>
	</p>
	<?php endforeach; ?>
<?php elseif( $matches ): ?>
	<p class="match">
		<span class="left"><?php echo $item1->getName(); ?></span>
		<span class="middle"><?php echo $matches[1]; ?></span>
		<span class="right"><?php echo $item2->getName(); ?></span>
	</p>
<?php else: ?>
	<p class="match"><span class="middle">No correlation could be found.</span></p>
	<p class="match">Think we may be wrong? Try waiting for the autocomplete results to pop-up when typing in a name and using the second or third option.  We try our best to know who you're looking for but where there are a lot of actors with the same name mistakes can be made.</p>
<?php endif; ?>
<?php if( $matches ): ?>
	<p id="social">
		Share these results with your friends: 
		<span id="tweet-this">
			<a href="http://twitter.com/share" class="twitter-share-button" data-url="http://moviexref.com/<?php echo $short_code; ?>" data-text="I just cross referenced <?php echo $item1->getName(); ?> with <?php echo $item2->getName(); ?>" data-count="none" data-counturl="http://moviexref.com/<?php echo $short_code; ?>">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
		</span>
		<span id="share-this">
			<a name="fb_share" id="fb_share" type="button" share_url="http://moviexref.com/<?php echo $short_code; ?>" href="http://www.facebook.com/sharer.php">Share</a><script src="http://static.ak.fbcdn.net/connect.php/js/FB.Share" type="text/javascript"></script>
		</span>
		<span id="permalink">
			<a id="permalink-link" href="http://moviexref.com/<?php echo $short_code; ?>">Permalink</a>
		</span>
	</p>
<?php endif; ?>

</div>