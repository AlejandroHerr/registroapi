angular.module('libroApp.filters', [])
    .filter('selectorFilter', [
        function () {
            return function (entries, key1, selectedChannel, key2) {
                if (!angular.isUndefined(entries) && !angular.isUndefined(selectedChannel) && selectedChannel.length > 0) {
                    var tempEntries = [];
                    angular.forEach(selectedChannel, function (key2) {
                        angular.forEach(entries, function (entry) {
                            if (angular.equals(entry[key1], key2)) {
                                tempEntries.push(entry);
                            }
                        });
                    });
                    return tempEntries;
                }
                return entries;
            };
        }
    ]);