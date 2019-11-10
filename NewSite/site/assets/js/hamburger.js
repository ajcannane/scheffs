/* Toggle between adding and removing the "responsive" class to topnav when the user clicks on the icon */
function myFunction() {
    var x = document.getElementById("myTopnav");
    if (x.className === "topnav") {
        x.className += " responsive";
    } else {
        x.className = "topnav";
    }

    var y = document.getElementById("mybanner");
    if (y != null) {
        if (y.className === "inner") {
            y.className += " responsive";
        } else {
            y.className = "inner";
        }
    }
}