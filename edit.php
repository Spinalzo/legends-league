<?php
require_once("config.php");
if (!isset($_SESSION["user_id"]) || $_SESSION["user_id"] == "") {
    // not logged in send to login page
    // redirect("index.php");
}

$status = FALSE;
if ( authorize($_SESSION["access"]["EDIT"]["EDITALL"]["create"]) ||
authorize($_SESSION["access"]["EDIT"]["EDITALL"]["edit"]) ||
authorize($_SESSION["access"]["EDIT"]["EDITALL"]["view"]) ||
authorize($_SESSION["access"]["EDIT"]["EDITALL"]["delete"]) ) {
 $status = TRUE;
}

if ($status === FALSE) {
 die("You dont have the permission to access this page");
}

// set page title
$title = "Edit-Center";

// if the rights are not set then add them in the current session
if (!isset($_SESSION["access"])) {

    try {

        $sql = "SELECT mod_modulegroupcode, mod_modulegroupname FROM module "
                . " WHERE 1 GROUP BY `mod_modulegroupcode` "
                . " ORDER BY `mod_modulegrouporder` ASC, `mod_moduleorder` ASC  ";


        $stmt = $DB->prepare($sql);
        $stmt->execute();
        $commonModules = $stmt->fetchAll();

        $sql = "SELECT mod_modulegroupcode, mod_modulegroupname, mod_modulepagename,  mod_modulecode, mod_modulename FROM module "
                . " WHERE 1 "
                . " ORDER BY `mod_modulegrouporder` ASC, `mod_moduleorder` ASC  ";

        $stmt = $DB->prepare($sql);
        $stmt->execute();
        $allModules = $stmt->fetchAll();

        $sql = "SELECT rr_modulecode, rr_create,  rr_edit, rr_delete, rr_view FROM role_rights "
                . " WHERE  rr_rolecode = :rc "
                . " ORDER BY `rr_modulecode` ASC  ";

        $stmt = $DB->prepare($sql);
        $stmt->bindValue(":rc", $_SESSION["rolecode"]);


        $stmt->execute();
        $userRights = $stmt->fetchAll();

        $_SESSION["access"] = set_rights($allModules, $userRights, $commonModules);

    } catch (Exception $ex) {

        echo $ex->getMessage();
    }
}
include 'php-snippet/module/htmlhead.php';
?>
	<body>
		<?php
			include 'php-snippet/module/header.php';
			include 'php-snippet/module/main-navigation.php';
			include 'php-snippet/module/xs-navigation.php';
		?>


		<main data-gurk="main" class="edit">
			<section class="layout left">

				<h1>Murka Edit Center</h1>

				<aside>
					<ul class="aside-menu" data-frontend="aside-menu">
						<li data-index="1" class="active"><span>Players</span></li>
						<li data-index="2"><span>Cards</span></li>

<?php if (authorize($_SESSION["access"]["EDIT"]["EDITALL"]["delete"])) { ?>
            <li data-index="3"><span>Register</span></li>
						<li data-index="4"><span>Votes</span></li>
<?php } ?>
					</ul>
				</aside>




				<article id="player" data-frontend="edit" data-target="1" style="display: block;">

					<h2 data-frontend="panel-headline-all" class="edit-headline arrow-direction">Player List</h2>
					<div class="edit-block" style="display: block;">
						<ul id="playerList" class="edit-list"></ul>
					</div>

					<h2 data-frontend="panel-headline-all" class="edit-headline">Add Player</h2>
					<div class="edit-block">
						<div class="form-row">

							<div class="form-col two">
								<input id="addName" type="text" placeholder="new player">
							</div>

              <div class="form-col two">
								<div class="select-wrap"><select id="addNationSelect"></select></div>
							</div>

						</div>

            <div class="form-row-inline">
                <div class="chosewrapper"><div id="addAvatarPicChose" class="choselist"></div></div>
            </div>

            <div class="form-row">
              <div class="form-col three shift-right">
                <button onclick="addPlayer()" class="btn primary">Save</button>
              </div>
            </div>

						<div id="addPlayerMsg"></div>
					</div>

<?php if (authorize($_SESSION["access"]["EDIT"]["EDITALL"]["delete"])) { ?>

					<h2 data-frontend="panel-headline-all" class="edit-headline">Edit Player</h2>
					<div class="edit-block">


            <div class="form-row">

							<div class="form-col three">
								<div class="select-wrap"><select id="editPlayer"></select></div>
							</div>

							<div class="form-col three">
								<input id="newName" type="text" placeholder="new name">
							</div>


              <div class="form-col three">
                <div class="select-wrap"><select id="editNationSelect"></select></div>
              </div>

            </div>

            <div class="form-row-inline">
                <div class="chosewrapper"><div id="editAvatarPicChose" class="choselist"></div></div>
            </div>

            <div class="form-row">

							<div class="form-col three shift-right">
								<button onclick="editPlayer()" class="btn primary">Edit</button>
							</div>

						</div>

						<div id="editPlayerMsg"></div>
					</div>


					<h2 data-frontend="panel-headline-all" class="edit-headline">Delete Player</h2>
					<div class="edit-block">
						<div class="form-row-inline">
							<div class="form-col three">
								<div class="select-wrap"><select id="deletePlayer"></select></div>
							</div>
							<div class="form-col three">
								<button onclick="deletePlayer()" class="btn secondary">Delete</button>
							</div>
						</div>
						<div id="deletePlayerMsg"></div>
					</div>
<?php } ?>


				</article>

				<article id="card" data-frontend="edit" data-target="2">

					<h2 data-frontend="panel-headline-all" class="edit-headline arrow-direction">Card List</h2>
					<div class="edit-block" style="display: block;">
						<ul id="cardList" class="edit-list"></ul>
						<div class="form-row" style="display: none;">
							<div class="form-col three shift-right">
								<button onclick="refreshCardLists()" class="btn primary">Refresh</button>
							</div>
						</div>
					</div>


<?php if (authorize($_SESSION["access"]["EDIT"]["EDITALL"]["delete"])) { ?>

					<h2 data-frontend="panel-headline-all" class="edit-headline">Add Card</h2>
					<div class="edit-block">

            <div class="form-row">
  						<div class="form-col two"><div class="select-wrap"><select id="addCardPicSelect"></select></div></div>
              <div class="form-col two"><input type="text" id="addCardName" placeholder="new card"></div>
  					</div>

            <div class="form-row">
              <div class="form-col two"><div class="select-wrap"><select id="addCardCatSelect"></select></div></div>
              <div class="form-col two"><input type="number" id="addCardPoints" placeholder="points" min="-10" max="10"></div>
            </div>

  					<div class="form-row">
  						<div class="form-col three shift-right">
  							<button onclick="addCard()" class="btn primary">Save</button>
  						</div>
  					</div>

  					<div id="addCardMsg"></div>

					</div>

          <h2 data-frontend="panel-headline-all" class="edit-headline">Edit Card</h2>
          <div class="edit-block">

            <div class="form-row">

              <div class="form-col two">
                <div class="select-wrap"><select id="editCard"></select></div>
              </div>
              <div class="form-col two">
                <div class="select-wrap"><select id="editCardPicSelect"></select></div>
              </div>

            </div>

            <div class="form-row">

              <div class="form-col three">
                <input id="newCardName" type="text" placeholder="new name">
              </div>
              <div class="form-col three"><div class="select-wrap"><select id="editCardCatSelect"></select></div></div>
              <div class="form-col three">
                <input id="newCardPoints" type="number" placeholder="new points" min="-10" max="10">
              </div>
            </div>

            <div class="form-row">
              <div class="form-col shift-right three">
                <button onclick="editCard()" class="btn primary">Edit</button>
              </div>
            </div>

            <div id="editCardMsg"></div>

          </div>


					<h2 data-frontend="panel-headline-all" class="edit-headline">Delete Card</h2>
					<div class="edit-block">
						<div class="form-row-inline">
							<div class="form-col three">
									<div class="select-wrap"><select id="deleteCard"></select></div>
								</div>
								<div class="form-col three">
									<button onclick="deleteCard()" class="btn secondary">Delete</button>
								</div>
						</div>
						<div id="deleteCardMsg"></div>
					</div>


<?php } ?>

				</article>

<?php if (authorize($_SESSION["access"]["EDIT"]["EDITALL"]["delete"])) { ?>
        <article id="register" data-frontend="edit" data-target="3">

          <h2 data-frontend="panel-headline-all" class="edit-headline arrow-direction">Register User</h2>
					<div class="edit-block" style="display: block;">

              <form action="reg/register.php" method="post">

          		<div class="form-row">

                <div class="form-col three">
          				<input type="text" size="24" maxlength="50" name="u_username" placeholder="username">
          		  </div>

            		<div class="form-col three">
            				<input type="password" size="24" maxlength="50" name="u_password" placeholder="password">
            		</div>

                <div class="form-col three">
          				<input type="password" size="24" maxlength="50" name="password2" placeholder="repeat password">
                </div>

          		</div>

          		<div class="form-row">
                <div class="form-col three">
            			<input id="inputrole" type="hidden" size="24" maxlength="50" name="u_rolecode" value="ADMIN">
                  <div class="select-wrap">
              			<select id="role">
              				<option value="ADMIN">Admin</option>
              				<option value="SUPERADMIN">Super Admin</option>
              			</select>
                  </div>
                </div>
          		</div>

          		<div class="form-row">
                <div class="form-col three shift-right">
                  <input type="submit" value="Save" class="btn primary">
                </div>
              </div>

          		</form>

            </diV>

        </article>


        <article id="vote" data-frontend="edit" data-target="4">
      		Vote List<br>
      		<button onclick="refreshVoteLists()">Refresh</button>
      		<ul id="voteList"></ul>
      		<ul id="fancyVoteList"></ul>

      		Add Vote<br>
      		<select id="votePlayer"></select>
      		<select id="voteCard"></select><br>
      		<button onclick="addVote()">Vote</button>
      		<div id="addVoteMsg"></div><hr>
      	</article>


<?php } ?>


			</section>

	</main>
	<?php
		include 'php-snippet/module/footer.php';
	?>
		<script src="js/global.js"></script>
		<script src="js/frontend.js"></script>
    <script src="js/edit.js"></script>
    <script>
    $('#role').on('change', function() {
    		var thisValue = $( this ).val();
    		$(  '#inputrole' ).val(thisValue);
    });
    </script>




	</body>
</html>
