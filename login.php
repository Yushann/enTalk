<?php
  require_once('module.php');

  htmlHead('login');
?>
	<script type="text/javascript">
  var uiConfig = {
        signInSuccessUrl: 'index.php',
        signInOptions: [
          firebase.auth.GoogleAuthProvider.PROVIDER_ID,
          firebase.auth.EmailAuthProvider.PROVIDER_ID
        ],
      };

      // Initialize the FirebaseUI Widget using Firebase.
      var ui = new firebaseui.auth.AuthUI(firebase.auth());
      // The start method will wait until the DOM is loaded.
      ui.start('#firebaseui-auth-container', uiConfig);
	</script>
	<div id="firebaseui-auth-container"></div>
</body>
</html>

    

