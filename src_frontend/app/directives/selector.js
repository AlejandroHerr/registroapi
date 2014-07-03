angular.module('libroApp.directives', [])
    .directive('selector', function () {
        return {
            restrict: 'A',
            replace: true,
            templateUrl: 'directives/selector.tpl.html',
            scope: {
                elements: '=',
                selected: '=',
                title: '@',
                after: '&'
            },
            link: function (scope, elem, attrs) {
                scope.setSelectedElement = function () {
                    var name = this.element.name;
                    if (_.contains(scope.selected, name)) {
                        scope.selected = _.without(scope.selected, name);
                    } else {
                        scope.selected.push(name);
                        //watch does not see the push...
                        scope.after();
                    }
                    return false;
                };
                scope.isChecked = function (name) {
                    if (_.contains(scope.selected, name)) {
                        return 'glyphicon glyphicon-ok pull-right';
                    }
                    return false;
                };
                scope.checkAllElements = function () {
                    scope.selected = _.pluck(scope.elements, 'name');
                    scope.after();
                };
                scope.$watch("selected", function(newValue, oldValue) {
                    scope.after();
                });
                scope.status = {
                    isopen: false
                };
                scope.selected = _.pluck(scope.elements, 'name');
            }
        }
    });