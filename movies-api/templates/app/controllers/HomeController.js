angular.module('app').controllerProvider.register('HomeController', ["$scope", "FetchJson", function($scope, FetchJson){

	
	FetchJson.success(function(data) {
		
		// bring data from json
		
		$scope.data = data.data; 


		// iniialize orderby options

		$scope.orderBy = 'rating';


		// Empty obj constructor for looping ng-repeat 
		
		$scope.makeObj = function(num) {
			arr =  new Array(num);
			for(i=0; i<arr.length; i++){
				arr[i]=i;
			}
			return arr;
		}


		// checks items to show
		
		if ($scope.data != null) {

			$scope.itemsShowing = $scope.data.length;

			$scope.areThereMovies = true;

			// filtering movies by search text (title)

			$scope.filter = function(){
				
				var arr = $scope.data.slice(0);
								
				//searchText by title
				if (typeof $scope.searchText !='undefined' && $scope.searchText!=""){
					var tempTitle;
					for(i=0; i<arr.length; i++){
						tempTitle = arr[i]['title'].toLowerCase();
						if(tempTitle.search($scope.searchText.toLowerCase())==-1){
							arr.splice(i, 1);
							i--;
						}
					}
				}

				//total items to paginate

				$scope.itemsShowing = arr.length;
				return arr;
			}

		} else{

			$scope.areThereMovies = false;
		
		}
				

		//pagination (show-more type)

		var pagesShown = 1;

		var pageSize = 6;

		$scope.paginationLimit = function() {
			return pageSize * pagesShown;
		};

		$scope.hasMoreItemsToShow = function() {
			return pagesShown < ($scope.itemsShowing / pageSize);
		};

		$scope.showMoreItems = function() {
			pagesShown++;       
		}; 


	});	

}]);