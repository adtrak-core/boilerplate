	<div class="gallery grid grid12_12">

		<div class="container">

			<?php 

			$images = get_field('gallery');

			if( $images ): ?>

			    <ul>
			        <?php foreach( $images as $image ): ?>
			            <li>
			                <a href="<?php echo $image['sizes']['large']; ?>">
			                     <img src="<?php echo $image['sizes']['gallery-300']; ?>" alt="<?php echo $image['alt']; ?>" />
			                </a>
			            </li>
			        <?php endforeach; ?>
			    </ul>

			<?php endif; ?>

		</div>

	</div>