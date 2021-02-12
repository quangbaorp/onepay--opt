$(document).ready(() => {
    // For Firebase JS SDK v7.20.0 and later, measurementId is optional
    const firebaseConfig = {
        apiKey: "AIzaSyBgvhu2mFGmz8dEJ1f8EBPb6vATn0qm4kA",
        authDomain: "optapp-4e569.firebaseapp.com",
        projectId: "optapp-4e569",
        storageBucket: "optapp-4e569.appspot.com",
        messagingSenderId: "706477031349",
        appId: "1:706477031349:web:c299cd38b61d64adee63fc",
        measurementId: "G-MT9L9BQR4Q"
      };
      // Initialize Firebase
      firebase.initializeApp(firebaseConfig);

      window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
          'size': 'invisible',
          'callback': function (response) {
              // reCAPTCHA solved, allow signInWithPhoneNumber.
              console.log('recaptcha resolved');
          }
      });
      onSignInSubmit();   
})  
function onSignInSubmit() {
    $('#verifPhNum').on('click', function() {
        let phoneNo = '';
        var code = $('#codeToVerify').val();
        console.log(code);
        $(this).attr('disabled', 'disabled');
        $(this).text('Processing..');
        confirmationResult.confirm(code).then(function (result) {
                    alert('Succecss verify phone');
                window.location.href="/"
            var user = result.user;
            console.log(user);
    
    
            // ...
        }.bind($(this))).catch(function (error) {
        
            // User couldn't sign in (bad verification code?)
            // ...
            $(this).removeAttr('disabled');
            $(this).text('Invalid Code');
            setTimeout(() => {
                $(this).text('Verify Phone No');
            }, 2000);
        }.bind($(this)));
    
    });
    
    
    $('#getcode').on('click', function () {
        var phoneNo = $('#number').val();
        console.log(phoneNo);
        getCode(phoneNo);
        var appVerifier = window.recaptchaVerifier;
        firebase.auth().signInWithPhoneNumber(phoneNo, appVerifier)
        .then(function (confirmationResult) {
    
            window.confirmationResult=confirmationResult;
            coderesult=confirmationResult;
            console.log(coderesult);
        }).catch(function (error) {
            console.log(error.message);
    
        });
    });
}



function getCode(phoneNumber) {
    var appVerifier = window.recaptchaVerifier;
    firebase.auth().signInWithPhoneNumber(phoneNumber, appVerifier)
        .then(function (confirmationResult) {
            console.log(confirmationResult);
            // SMS sent. Prompt user to type the code from the message, then sign the
            // user in with confirmationResult.confirm(code).
            window.confirmationResult = confirmationResult;
            $('#getcode').removeAttr('disabled');
            $('#getcode').text('RESEND');
        }).catch(function (error) {
            
            console.log(error);
            console.log(error.code);
            // Error; SMS not sent
            // ...
        });
  }  