<!DOCTYPE html>
<html>
<head>
	<!-- <link rel="icon" href="assets/img/icon.png"> -->
	<title>Valentine Wall | Open Access BPO</title>
	<link href="https://fonts.googleapis.com/css?family=Gochi+Hand|Montserrat&display=swap" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="http://172.17.0.2/project/freedomwall/wall/assets/css/all.min.css">
	<link rel="stylesheet" type="text/css" href="http://172.17.0.2/project/freedomwall/wall/assets/css/style.css">
	
</head>

<body translate="no" style="">
	<div class="freedom-wall">
		<ul data-postcount="{{count($posts)}}" data-maxpost="<?php 
		if(isset($_SESSION['postCount'])) {
			echo $_SESSION['postCount'];
		}

		else {
			echo '';
		}
		?>">
			<?php
				if (isset($_SESSION["firstPost"]) && isset($_SESSION["lastPost"]) && isset($_SESSION['postCount'])) {
					$fp=$_SESSION["firstPost"];
					$lp=$_SESSION["lastPost"];
					$postCount=$_SESSION["postCount"];
					$i=0;

					foreach ($posts as $post) {
						if ($post['id']>=$fp) {
							if ($i++ == $postCount) break;

							if ($post['img'] !== "") {
								$bg = 'background-image: url('.$post["img"].');';
							}

							else {
								$bg = "";
							}

							$length = 150;
							$end = "...";
							$string = strip_tags($post['message']);

						    if (strlen($string) > $length) {

						        // truncate string
						        $stringCut = substr($string, 0, $length);

						        // make sure it ends in a word so assassinate doesn't become ass...
						        $string = substr($stringCut, 0, strrpos($stringCut, ' ')).$end;
						    }
						    
							?>
							<li class="post-it" data-post="<?php echo $post['id']; ?>">
								<a href="#"

									<?php
										if ($bg != null) {
											echo "class='polaroid'";
										}

										else {
											echo "style=$bg";
										}
									?>
									>

									<div class="img" style="<?php echo $bg; ?>">
										
									</div>
									<p style="display: none; white-space:pre-wrap;">
										<?php
											echo $post['message'];
										?>
										<br/><strong style="color:#333">From: {{$post['from']}}</strong>
									</p>
									<p>
										<?php
											echo $string;
										?><br/><strong style="color:#3ea6ff; ">From: {{$post['from']}}</strong>
									</p>
								</a>
							</li>
							<?php
						}
					}
					session_destroy();
				}

				else {
					$postCount=5000;

					$i=0;

					foreach ($posts as $post) {
						if ($i++ == $postCount) break;

						if ($post['img'] !== "" && !empty($post['img'])) {
							$bg = 'background-image: url('.$post["img"].');';
						}

						else {
							$bg = "";
						}

						$length = 100;
						$end = "...";

						$string = strip_tags($post['message']);

					    if (strlen($string) > $length) {
					        $stringCut = substr($string, 0, $length);
					        $string = substr($stringCut, 0, strrpos($stringCut, ' ')).$end;
					    }
					    
						?>
						<li class="post-it" data-post="<?php echo $post['id']; ?>">
							<a href="#"

								<?php
									if ($bg != null) {
										echo "class='polaroid'";
									}

									else {
										echo "style=$bg";
									}
								?>
								>

								<div class="img" style="<?php echo $bg; ?>">
									
								</div>
								<p style="display: none; white-space:pre-wrap;"">
									<?php
										echo $post['message'];
									?>
									<br/><strong style="color:#333;">From: {{$post['from']}}</strong>
								</p>
								<p>
									<?php
										echo $string;
									?><br/><strong style="color:#3ea6ff; font-size: x-small;">From: {{$post['from']}}</strong>
								</p>
							</a>
						</li>
						<?php
					}
				}
			?>
		</ul>
		<div class="full-message">
			<div class="message minimized">
				<div class="close">
					<i class="fas fa-minus-circle"></i>
				</div>
				<div class="flag" data-messageID="">
					<i class="fas fa-flag"></i>
				</div>
				<img src="">
				<p></p>
			</div>
			<div class="image-overlay">
				<div class="close-flag close-overlay">
					<i class="fas fa-times-circle"></i>
				</div>
				<img src="">
			</div>
		</div>
		<div class="flagged">
			<label>
				<span>Reason: </span>
				<textarea name="flagReason" placeholder="State Your Reason Here.."></textarea>
				<button type="button" id="submitReason">
					Submit <i class="fas fa-angle-right"></i>
				</button>
			</label>
			<div class="close-flag">
				<i class="fas fa-times-circle"></i>
			</div>
		</div>
		<div class="pagination hidden">
			<div class="buttons">
				<div class="prev">
					<i class="fas fa-angle-left"></i>
				</div>
				<div class="next">
					<i class="fas fa-angle-right"></i>
				</div>
			</div>
		</div>
		<div class="system-message">
			<p></p>
		</div>
	</div>
	<script type="text/javascript" src="http://172.17.0.2/project/freedomwall/wall/assets/js/jquery.min.js"></script>
	<script type="text/javascript" src="http://172.17.0.2/project/freedomwall/wall/assets/js/script.js"></script>

	<script type="text/javascript">
		

		
	</script>
</body>


</html>