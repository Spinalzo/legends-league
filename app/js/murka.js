var myapp = angular.module('myapp', []);
var refreshList = [];


myapp.controller('resultCtrl', function($scope, $http) {
	
	// add game
	$scope.add = function() {
		$http({
			method: 'POST',
			url: 'api.php/game',
			data: {host: $scope.host, guest: $scope.guest, date: $scope.date, author: $scope.author}
		}).then(function successCallback(data) {
			
			if(isNaN(data.data)) {
				switch(true) {
					case (data.data.substring(0, 8) == 'NOT NULL'):
						$scope.error = 'All fields are required.'; break;
					default:
						$scope.error = data.data;
				}
			} else {
				$scope.resForm.$setUntouched();
				$scope.host = ''; $scope.guest = ''; $scope.author = ''; $scope.date = new Date();
				$scope.teamcheck = false;
				$scope.error = 'Result added.';
				$scope.addMatchups(data.data);
				refreshResPlayerList();
				refreshGameList();
			}
		}, function errorCallback(data) {
		});	
	}
	
	$scope.addMatchups = function(gameId) {
		for(var i = 0; i < $scope.hostTeam.length; i++) {
			$http({
				method: 'POST',
				url: 'api.php/matchup',
				data: {playerId: $scope.hostTeam[i].id, gameId: gameId, team: 'host'}
			}).then(function successCallback(data) {
								
			}, function errorCallback(data) {
			});	
		}
		
		for(var i = 0; i < $scope.guestTeam.length; i++) {
			$http({
				method: 'POST',
				url: 'api.php/matchup',
				data: {playerId: $scope.guestTeam[i].id, gameId: gameId, team: 'guest'}
			}).then(function successCallback(data) {
				
					
			}, function errorCallback(data) {
			});	
		}
		
		
	}
	

	
	// get game list
	refreshGameList = function() {
		$http({
			method: 'GET',
			url: 'api.php/resultsList'
		}).then(function successCallback(data) {
			$scope.resultsList = data.data;
			
		}, function errorCallback() {
			$scope.resultsList = [];
			$scope.error = 'Unable to load results list.';
		});	
	}
	refreshGameList();
	
	
	// get player list
	refreshResPlayerList = function() {
		$http({
			method: 'GET',
			url: 'api.php/player'
		}).then(function successCallback(data) {
			$scope.playerList = data.data;
			$scope.hostTeam = [];
			$scope.guestTeam = [];
			$scope.count = $scope.playerList.length;
		}, function errorCallback() {
			$scope.playerList = [];
			$scope.error = 'Unable to load player list.';
		});	
	}
	refreshResPlayerList();
	
	$scope.date = new Date();
	$scope.hostTeam = [];
	$scope.guestTeam = [];
	$scope.addToHost = function(player) {
		var index = getAngularIndex(player.id);
		$scope.hostTeam.push($scope.playerList[index]);
		$scope.playerList.splice(index, 1);
		teamcheck();
	}
	$scope.removeFromHost = function(player) {
		var index = getAngularIndexHost(player.id);
		$scope.playerList.push($scope.hostTeam[index]);
		$scope.hostTeam.splice(index, 1);
		teamcheck();
	}
	$scope.addToGuest = function(player) {
		var index = getAngularIndex(player.id);
		$scope.guestTeam.push($scope.playerList[index]);
		$scope.playerList.splice(index, 1);
		teamcheck();
	}
	$scope.removeFromGuest = function(player) {
		var index = getAngularIndexGuest(player.id);
		$scope.playerList.push($scope.guestTeam[index]);
		$scope.guestTeam.splice(index, 1);
		teamcheck();
	}
	
	
	function teamcheck() {
		if($scope.hostTeam.length > 0 && $scope.guestTeam.length > 0) {
			$scope.teamcheck = true;} else {$scope.teamcheck = false;		
		}
		
	}
	
	// look up angular's list index
	function getAngularIndex(id) {
		for(var i = 0; i < $scope.playerList.length; i++) {
			if($scope.playerList[i].id === id) {
				return i;
			}
		}
		return -1;
	}
	
	function getAngularIndexHost(id) {
		for(var i = 0; i < $scope.hostTeam.length; i++) {
			if($scope.hostTeam[i].id === id) {
				return i;
			}
		}
		return -1;
	}
	
	function getAngularIndexGuest(id) {
		for(var i = 0; i < $scope.guestTeam.length; i++) {
			if($scope.guestTeam[i].id === id) {
				return i;
			}
		}
		return -1;
	}
	
	$scope.showFormFunc = function() {
		$scope.showForm = !$scope.showForm;
		$scope.showFormClass = ($scope.showFormClass == 'open') ? '' : 'open';
	}
	
	clearGameError = function() {$scope.error = '';}
	
	
});
	
myapp.controller('userCtrl', function($scope, $http) {
	
	// get user list
	refreshUserList = function() {
		$http({
			method: 'GET',
			url: 'api.php/user'
		}).then(function successCallback(data) {
			$scope.userList = data.data;
			$scope.count = $scope.userList.length;
		}, function errorCallback() {
			$scope.userList = [];
			$scope.error = 'Unable to load user list.';
		});	
	}
	refreshUserList();
	
	// add user
	$scope.add = function() {
		$http({
			method: 'POST',
			url: 'api.php/user',
			data: {name: $scope.name, role: $scope.role}
		}).then(function successCallback(data) {
			
			if(isNaN(data.data)) {
				switch(true) {
					case (data.data.substring(0, 6) == 'UNIQUE'):
						$scope.error = 'User already exists.'; break;
					case (data.data.substring(0, 5) == 'CHECK'):
						$scope.error = 'Invalid user name.'; break;
					case (data.data.substring(0, 8) == 'NOT NULL'):
						$scope.error = 'All fields are required.'; break;
					default:
						$scope.error = data.data;
				}
			} else {
				$scope.name = ''; $scope.id = ''; $scope.role = '';
				$scope.error = 'New user added.';
				refreshUserList();
			}
		}, function errorCallback(data) {
		});	
	}
	
	
	// delete user
	$scope.deleteUser = function(user) {
		var result = confirm('Are you sure you want to delete ' + user.name + '?');
		if(result === true) {
			$http({
				method: 'DELETE',
				url: 'api.php/user/' + user.id
			}).then(function successCallback(data) {
				$scope.error = user.name + ' was deleted.';
				$scope.name = ''; $scope.id = '';  $scope.role = '';
				refreshUserList();
			}, function errorCallback(data) {
			});
		}		
	};
	
	// save edited data
	$scope.saveChanges = function() {
		$http({
			method: 'PUT',
			url: 'api.php/user/' + $scope.id,
			data: {name: $scope.name, id: $scope.id, role: $scope.role}
		}).then(function successCallback(data) {			
			if(isNaN(data.data+0)) {
				switch(true) {
					case (data.data.substring(0, 6) == 'UNIQUE'):
						$scope.error = 'User already exists.';
						break;
					case (data.data.substring(0, 5) == 'CHECK'):
						$scope.error = 'Invalid user name.';
						break;
					case (data.data.substring(0, 8) == 'NOT NULL'):
						$scope.error = 'All fields are required.'; 
						break;
					default:
						$scope.error = data.data;
				}
			} else {
				$scope.error = data.data + ' User(s) edited.';
				if((data.data*1) != 0) {$scope.name = ''; $scope.id = ''; $scope.role = '';}
				refreshUserList();
			}
		}, function errorCallback(data) {
			alert();
		});	
	}
	
	// load data into edit-form
	$scope.loadDataIntoForm = function(user) {
		$scope.error = '';
		$scope.id = user.id;
		$scope.name = user.name;
		$scope.role = user.role;
		$scope.showForm = true;
		$scope.showFormClass = 'open';
	}
	
	// look up angular's list index
	function getAngularIndex(id) {
		for(var i = 0; i < $scope.userList.length; i++) {
			if($scope.userList[i].id === id) {
				return i;
			}
		}
		return -1;
	}
	
	$scope.showFormFunc = function() {
		$scope.showForm = !$scope.showForm;
		$scope.showFormClass = ($scope.showFormClass == 'open') ? '' : 'open';
	}
	
	clearUserError = function() {$scope.error = '';}
	
});



myapp.controller('cardCtrl', function($scope, $http) {
	
	// get card list
	refreshCardList = function() {
		$http({
			method: 'GET',
			url: 'api.php/card'
		}).then(function successCallback(data) {
			$scope.cardList = data.data;
			$scope.count = $scope.cardList.length;
		}, function errorCallback() {
			$scope.cardList = [];
			$scope.error = 'Unable to load card list.';
		});	
	}
	refreshCardList();
	
	// add card
	$scope.add = function() {
		$http({
			method: 'POST',
			url: 'api.php/card',
			data: {name: $scope.name, points: $scope.points, url: $scope.url}
		}).then(function successCallback(data) {
			
			if(isNaN(data.data)) {
				switch(true) {
					case (data.data.substring(0, 6) == 'UNIQUE'):
						$scope.error = 'Card already exists.'; break;
					case (data.data.substring(0, 5) == 'CHECK'):
						$scope.error = 'Invalid card name.'; break;
					case (data.data.substring(0, 8) == 'NOT NULL'):
						$scope.error = 'All fields are required.'; break;
					default:
						$scope.error = data.data;
				}
			} else {
				$scope.name = ''; $scope.id = ''; $scope.points = ''; $scope.url = '';
				$scope.error = 'New card added.';
				refreshCardList();
			}
		}, function errorCallback(data) {
		});	
	}
	
	
	// delete card
	$scope.deleteCard = function(card) {
		var result = confirm('Are you sure you want to delete ' + card.name + '?');
		if(result === true) {
			$http({
				method: 'DELETE',
				url: 'api.php/card/' + card.id
			}).then(function successCallback(data) {
				$scope.error = card.name + ' was deleted.';
				$scope.name = ''; $scope.id = '';  $scope.points = ''; $scope.url = '';
				refreshCardList();
			}, function errorCallback(data) {
			});
		}		
	};
	
	// save edited data
	$scope.saveChanges = function() {
		$http({
			method: 'PUT',
			url: 'api.php/card/' + $scope.id,
			data: {name: $scope.name, id: $scope.id, points: $scope.points, url: $scope.url}
		}).then(function successCallback(data) {			
			if(isNaN(data.data+0)) {
				switch(true) {
					case (data.data.substring(0, 6) == 'UNIQUE'):
						$scope.error = 'Card already exists.';
						break;
					case (data.data.substring(0, 5) == 'CHECK'):
						$scope.error = 'Invalid card name.';
						break;
					case (data.data.substring(0, 8) == 'NOT NULL'):
						$scope.error = 'All fields are required.'; 
						break;
					default:
						$scope.error = data.data;
				}
			} else {
				$scope.error = data.data + ' Cards(s) edited.';
				$scope.name = ''; $scope.id = ''; $scope.points = ''; $scope.url = '';
				refreshCardList();
			}
		}, function errorCallback(data) {
			alert();
		});	
	}
	
	// load data into edit-form
	$scope.loadDataIntoForm = function(card) {
		$scope.error = '';
		$scope.id = card.id;
		$scope.name = card.name;
		$scope.points = card.points;
		$scope.url = card.url;
		$scope.showForm = true;
		$scope.showFormClass = 'open';
	}
	
	// look up angular's list index
	function getAngularIndex(id) {
		for(var i = 0; i < $scope.cardList.length; i++) {
			if($scope.cardList[i].id === id) {
				return i;
			}
		}
		return -1;
	}
	
	$scope.showFormFunc = function() {
		$scope.showForm = !$scope.showForm;
		$scope.showFormClass = ($scope.showFormClass == 'open') ? '' : 'open';
	}
	
	clearCardError = function() {$scope.error = '';}
	
});






myapp.controller('playerCtrl', function($scope, $http) {
	
	// get player list
	refreshPlayerList = function() {
		$http({
			method: 'GET',
			url: 'api.php/player'
		}).then(function successCallback(data) {
			$scope.playerList = data.data;
			$scope.count = $scope.playerList.length;
		}, function errorCallback() {
			$scope.playerList = [];
			$scope.error = 'Unable to load player list.';
		});	
	}
	refreshPlayerList();
	
	// add player
	$scope.add = function() {
		$http({
			method: 'POST',
			url: 'api.php/player',
			data: {name: $scope.name, nationality: $scope.nationality, avatar: $scope.avatar}
		}).then(function successCallback(data) {
			
			if(isNaN(data.data)) {
				switch(true) {
					case (data.data.substring(0, 6) == 'UNIQUE'):
						$scope.error = 'Player already exists.'; break;
					case (data.data.substring(0, 5) == 'CHECK'):
						$scope.error = 'Invalid player name.'; break;
					case (data.data.substring(0, 8) == 'NOT NULL'):
						$scope.error = 'Player name must not be empty.'; break;
					default:
						$scope.error = data.data;
				}
			} else {
				$scope.name = ''; $scope.id = ''; $scope.nationality = ''; $scope.avatar = '';
				$scope.error = 'New player added.';
				refreshPlayerList();
			}
		}, function errorCallback(data) {
		});	
	}
	
	
	// delete player
	$scope.deletePlayer = function(player) {
		var result = confirm('Are you sure you want to delete ' + player.name + '?');
		if(result === true) {
			$http({
				method: 'DELETE',
				url: 'api.php/player/' + player.id
			}).then(function successCallback(data) {
				console.log(data.data);
				if(isNaN(data.data+0)) {
					$scope.error = data.data;
				} else {
					$scope.error = player.name + ' was deleted.';
					$scope.name = ''; $scope.id = ''; $scope.nationality = ''; $scope.avatar = '';
					refreshPlayerList();
					console.log('success');
				}
			}, function errorCallback(data) {
				
			});
		}		
	};
	
	// save edited data
	$scope.saveChanges = function() {
		$http({
			method: 'PUT',
			url: 'api.php/player/' + $scope.id,
			data: {name: $scope.name, id: $scope.id, nationality: $scope.nationality, avatar: $scope.avatar}
		}).then(function successCallback(data) {			
			$scope.name = ''; $scope.id = ''; $scope.nationality = ''; $scope.avatar = '';
			if(isNaN(data.data+0)) {
				switch(true) {
					case (data.data.substring(0, 6) == 'UNIQUE'):
						$scope.error = 'Player already exists.';
						break;
					case (data.data.substring(0, 5) == 'CHECK'):
						$scope.error = 'Invalid player name.';
						break;
					default:
						$scope.error = data.data;
				}
			} else {
				$scope.error = data.data + ' Player(s) edited.';
				refreshPlayerList();
			}
		}, function errorCallback(data) {
			alert();
		});	
	}
	
	// load data into edit-form
	$scope.loadDataIntoForm = function(player) {
		$scope.error = '';
		$scope.name = player.name;
		$scope.id = player.id;
		$scope.nationality = player.nationality;
		$scope.avatar = player.avatar;
		$scope.showForm = true;
		$scope.showFormClass = 'open';
	}
	
	// look up angular's list index
	function getAngularIndex(id) {
		for(var i = 0; i < $scope.playerList.length; i++) {
			if($scope.playerList[i].id === id) {
				return i;
			}
		}
		return -1;
	}
	
	$scope.showFormFunc = function() {
		$scope.showForm = !$scope.showForm;
		$scope.showFormClass = ($scope.showFormClass == 'open') ? '' : 'open';
	}
	
	clearPlayerError = function() {$scope.error = '';}
	
	
});

myapp.controller('navController', function($scope, $http) {
	
	
	
	
	$scope.toggleView = function(section) {
		refreshUserList(); clearUserError();
		refreshPlayerList(); clearPlayerError();
		refreshResPlayerList();
		refreshCardList(); clearCardError();
		refreshGameList(); clearGameError();
		
		
		//remove class 'show' from <section>
		var others = document.querySelectorAll('section:not(#' + section + ')');
		for(var i = 0; i < others.length; i++) { others[i].classList.remove('show'); }
		//remove class 'show' from <nav><li>
		var others = document.querySelectorAll('nav li:not(#' + section + 'Nav)');
		for(var i = 0; i < others.length; i++) { others[i].classList.remove('show'); }
		//toggle class 'show' on selected <section> and <nav><li>
		document.getElementById(section).classList.toggle('show');
		document.getElementById(section + 'Nav').classList.toggle('show');
			
		
	}
});