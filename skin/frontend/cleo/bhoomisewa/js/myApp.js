var myApp=angular.module('myApp',['ngRoute']);

myApp.controller('sellerRegCtrl',function($scope){
  $scope.isPersonal=true;
  $scope.isBusiness=false;
  $scope.isLogin=false;

  $scope.setInfoTab=function(type){
    $scope.isPersonal=false;
    $scope.isBusiness=false;
    $scope.isLogin=false;
    $scope.isVerification=false;
    switch(type){
      case 'personal':  $scope.isPersonal=true;break;
      case 'business':   $scope.isBusiness=true;break;
      case 'login':   $scope.isLogin=true;break;
      case 'verification' :   $scope.isVerification=true;break;
      default :    $scope.isPersonal=true;
    }
  };
});
