SwiftMVC
========

Currently my portfolio site is using SwiftMVC: http://pristinemarketing.com/
Please email any feedback to: jessecascio@gmail.com

I am looking to get some feedback on my home built framework.  The framework was modeled after Zend Framework 1; it has the same URL routing structure, controller/action/var/value, it uses the same directory structure, and it uses the View object in a similar fashion in the controllers.  

The project goals were to build a light weight framework that had the essential framework components:

1) MVC directory structure
2) Routing
3) Caching

A couple things I know I missed on: offering custom routing and ORM support.  Please take the time to browse through the code, try and get it set up in your dev environment, and give me some feedback:

1) Was I able to encapsulate the minimum functionality for a MVC framework?
2) Was the code easy to follow, and set up in a logical manner?
3) What are some key framework concepts I missed?  Some that I used well?

Any feedback would be greatly appreciated.

NOTES on setting up:

You must be running PHP 5.3 or greater

All requests are routed through the index.php file

If you change the directory structure, constants will have to be changed in application/Swift.php -> ConfigureApplication()

The constant BASE_URL (ConfigureApplication) is defined on the assumption you are not using a host and that the framework is being accessed: localhost/swiftmvc/public (directory location does not matter just that the public part shows in the url)

The URL routes are: controller/action which is will automatically render the view: application/views/scripts/controller/action.phtml

There are a lot of notes in the Controllers and views
