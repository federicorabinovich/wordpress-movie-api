var app = angular.module('app', ['ngRoute','routeResolverServices', 'ngAnimate', 'ngSanitize'])
.config(['$routeProvider', '$controllerProvider', 'routeResolverProvider', function($routeProvider, $controllerProvider, routeResolverProvider) {

	app.controllerProvider = $controllerProvider;
	var route = routeResolverProvider.route;


  $routeProvider
	.when('/', route.resolve('Home'))
	.otherwise({
	  redirectTo: '/'
  });

}]);
