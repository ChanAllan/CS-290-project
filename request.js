/*Sends request and prints error to screen*/
var requestData = function(address) {
  var request = new XMLHttpRequest();
  if (!request) {
    throw 'HttpRequest cannot be created';
    }
  request.onreadystatechange = function() {
    if (this.readyState === 4 && this.status === 200) {
      var msg = request.responseText;
      var errormsg = document.getElementById('errormsg');
      errormsg.innerHTML=msg;
    }
  };
  request.open('POST', 'newlogin.php?' + address, true);
  request.send(null);
};

/*Gets values from form and passes to request function to check for errors*/
function request_login() {
  var data = document.getElementById('login_form');
  var address = 'action=Userlogin' + '&username=' + data.elements['username'].value + '&password=' + data.elements['password'].value;
  requestData(address);
}

/*Gets values from form and passes to request function to check for errors*/
function register_user() {
	var data = document.getElementById('register_form');
  var address = 'action=Register_account' + '&username=' + data.elements['reg_username'].value + '&password=' + data.elements['reg_password'].value;
  requestData(address);
}

