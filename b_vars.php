// URL String
rootURL     = "http://csis.svsu.edu/";
userNameURL = "~askubicz";
appURL      = "/b/";
URL         = rootURL + userNameURL + appURL;

// Get ID from URL
function getID() {
    id = window.location.search.substring(1);
    id = id.split("=");
    return id[1];
}