function redirect(data) {
    "use strict";
    if (data.banned === undefined)
    {
        return;
    }
    else{
        if(data.banned==1)
        {
            window.location = "../ban.html";
        }
    }
}
function toban(myuid) {
    "use strict";
    var myuid = getLocalStorageStuff("id");
    if (myuid == undefined)
    {
        return;
    }
    else{
        $.ajax({
                type:"GET",
                url:"bans.php",
                data:{user:myuid}
            }).done(redirect);
    }
}