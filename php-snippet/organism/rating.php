<article id="rating" class="rating" style="display: none">
<h2>Create Results</h2>

<?php
include 'php-snippet/molecule/matcharrange.php';
include 'php-snippet/molecule/matchside.php';
?>

	<div id="fix-result" class="rating-container" style="display: none;">

		<div class="form-row-inline">
			<div class="form-col shift-right three">
				<button id="checkresult" class="btn primary">OK?</button>
			</div>
		</div>

	</div>



		<div id="fix-all" class="rating-container" style="display: none;">


			<h4 data-frontend="resultrow-rate-headline">Rating</h4>

			<div id="rating-result" data-result="Player-Rate" class="form-result-block">
				<ul>
					<li></li>
				</ul>
			</div>

			<h4 data-frontend="resultrow-card-headline">Cards</h4>

			<div data-result="Player-Card" class="form-result-block">
				<ul>
					<li></li>
				</ul>
			</div>

			<input type="hidden" id="date-match">
			<input type="hidden" id="author" data-id="judge" value="<?php echo $_SESSION['user_id']; ?>">





			<div class="form-row">
				<div class="form-col shift-middle three">
					<div id="deleteresult" class="btn secondary">Delete Result</div>
				</div>
				<div class="form-col three">
					<!--<button id="saveall" class="btn primary">Save</button>-->
					<button class="btn primary" onclick="save()">Save</button>
				</div>
			</div>

		</div>





</article>

<pre>
<?php // print_r($_POST); ?>
</pre>
