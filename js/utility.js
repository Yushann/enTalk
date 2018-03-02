  var userData;
  var config = {
    apiKey: "AIzaSyCXdOO9r6oBOtlZoahlB7XXRe56EWSuqFQ",
    authDomain: "engspeak-1990a.firebaseapp.com",
    databaseURL: "https://engspeak-1990a.firebaseio.com",
    projectId: "engspeak-1990a",
    storageBucket: "engspeak-1990a.appspot.com",
    messagingSenderId: "1086233938278"
  };
  firebase.initializeApp(config);
  firebase.auth().onAuthStateChanged(function(user) {
    if (user) {
      userData = user;
      $('#loginTag').text(userData.displayName + ' 登出');
      
    } else {
	  $('#loginTag').text('登入');
    }
  });

  const db = firebase.firestore();

function signUp(){
	var uiConfig = {
        signInSuccessUrl: 'index.php',
        signInOptions: [
          firebase.auth.GoogleAuthProvider.PROVIDER_ID,
          // firebase.auth.EmailAuthProvider.PROVIDER_ID
        ],
        'signInFlow': 'popup'
      };

      // Initialize the FirebaseUI Widget using Firebase.
      var ui = new firebaseui.auth.AuthUI(firebase.auth());
      // The start method will wait until the DOM is loaded.
      ui.start('#firebaseui-auth-container', uiConfig);
}



//新增列表
$(document).ready(function(){
	$('#datetimepicker').datetimepicker({
	    inline: true,
	    sideBySide: true
	}); 

	if(localStorage.mylist == undefined) {
		List.getAttendedLists(false);
	}

	List.getLists();
});

Login = (function($, window){
	return {
		forceLogin: function(action) {
			if(!Login.isLogin()) {
				Login.login();
				return
			}

			action();
		},
		isLogin: function() {
			return userData == undefined ? false : true;
		},
		login: function() {
			// $('#signUpModal').modal('show');
			location.href = 'login.php';
		},
		logout: function() {
			firebase.auth().signOut().then(function() {
			  location.href = "index.php";
			  localStorage.removeItem("mylist");
			}, function(error) {
			  consoloe.log('logout fail');
			});
		}
    };
}(jQuery, window));


Utility = (function($, window){
	return {
		randomCharacter: function(joined) {
       		var emptyJoined = Utility.joinedVacancy(joined);
			emptyJoined = Object.keys(emptyJoined);
       		return emptyJoined[Math.floor(Math.random()*emptyJoined.length)];
       },

       joinedVacancy: function(joined){
			var emptyJoined = Object.assign({}, joined);
			Object.keys(joined).forEach(function(key) {
				if(emptyJoined[key] != "") {
					delete emptyJoined[key];
				}
			});
			return emptyJoined;
       },

       joinedRemoveMyself: function(joined){
       		var emptyJoined = Object.assign({}, joined);
			Object.keys(joined).forEach(function(key) {
				if(emptyJoined[key] == userData.uid) {
					emptyJoined[key] = "";
				}
			});
			return emptyJoined;
       }
    };
}(jQuery, window));