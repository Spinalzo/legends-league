<?php
						if (isset($_SESSION["username"]) && $_SESSION["user_id"] != "") {
							 	echo '
								<article id="resultBlockCreate">
										<div class="form-row">

											<div class="form-col three">
												<label>Create match for:</label>
												<input id="free-date" type="date">
											</div>
											<div class="form-col three">
												<label>Create for chosen date...</label>
												<button id="newresult-free" data-action="create-free" class="btn primary" onclick="loadPlayerListsReal()">Create</button>
											</div>
											<div class="form-col three">
												<label>...or create match for today</label>
												<div id="newresult" data-action="create" class="btn primary" onClick="loadPlayerListsReal()">Create</div>
												<input type="hidden" id="date" data-result="date" value="" name="">
											</div>
										</div>

										<div class="form-row">


										</div>
								</article>
								';
						 }
?>

<article id="resultBlock" class="result-day">





</article>
