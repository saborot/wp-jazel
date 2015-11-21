jQuery(function() {

    jQuery("body").addClass("container-only-on-mobile");

    var settings = {
        makeModelFlyoutVersion: 1, // Valid Make/Model flyout versions: 0,1
        colorScheme: "light", // valid color schemes: light, dark,
        menuClickEventName: "a5-srp-menu-click"
    };

    var srpAppElement = document.getElementById("srp-app");

    try {
        A5Srp(settings, srpAppElement);
    } catch(e) {
        console.log('SRP Error: ' + e);
    }

    jQuery(window).bind("a5-srp-menu-click", function() {
        window.hamburgerMenu.open();
    });    
});
