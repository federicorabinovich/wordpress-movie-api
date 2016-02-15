app.directive("movieFile", function() {
  return {
    restrict: "E",
    require: 'ngModel',
    scope: true,
	templateUrl: movies_api_path+'app/views/movieFileTemplate.html'
  };
});