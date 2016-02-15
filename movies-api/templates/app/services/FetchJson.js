angular.module('app').service('FetchJson', ['$http', function($http){
  
  return $http.get(movies_json_api_full_path).success(function(data){
    return data;
  }).error(function(err){
    return err;
  });

}]);