function payment()
{
    let a= document.getElementById("method").value;
    if(a == "Card")
    {
        document.getElementById("card").disabled = false;
        document.getElementById("carda").disabled = false;
        document.getElementById("cardb").disabled = false;
    }
    else 
    {
        document.getElementById("card").disabled = true;
        document.getElementById("carda").disabled = true;
        document.getElementById("cardb").disabled = true;

    }



}


function change()
{
    window.open("home.php");
}