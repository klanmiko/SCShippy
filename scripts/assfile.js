function getLocalStorageStuff(a) {
    var b = localStorage.getItem(a);
    if (b == null){
        if (a == "id"){
            localStorage.setItem(a, makeNewUserID());
        }
        else{
            localStorage.setItem(a, 0);
        }
    }
    return localStorage.getItem(a);
}

function makeNewUserID() {
	return new Date().getTime().toString();
}
function userCheckIn() {
	var myuid = getLocalStorageStuff("id");
	ga('set', '&uid', myuid);
}

userCheckIn();
  
var TrackCode_Last_Modify_Date = "Sun Jan  4 13:31:09 SGT 2015";
