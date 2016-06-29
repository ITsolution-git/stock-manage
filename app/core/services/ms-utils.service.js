(function ()
{
    'use strict';

    var core = angular
        .module('app.core')
        .factory('msUtils', msUtils);

    /** @ngInject */
    function msUtils($window)
    {
        // Private variables
        var mobileDetect = new MobileDetect($window.navigator.userAgent),
            browserInfo = null;

        var service = {
            exists       : exists,
            detectBrowser: detectBrowser,
            guidGenerator: guidGenerator,
            isMobile     : isMobile,
            toggleInArray: toggleInArray
        };

        return service;

        //////////

        /**
         * Check if item exists in a list
         *
         * @param item
         * @param list
         * @returns {boolean}
         */
        function exists(item, list)
        {
            return list.indexOf(item) > -1;
        }

        /**
         * Returns browser information
         * from user agent data
         *
         * Found at http://www.quirksmode.org/js/detect.html
         * but modified and updated to fit for our needs
         */
        function detectBrowser()
        {
            // If we already tested, do not test again
            if ( browserInfo )
            {
                return browserInfo;
            }

            var browserData = [
                {
                    string       : $window.navigator.userAgent,
                    subString    : 'Edge',
                    versionSearch: 'Edge',
                    identity     : 'Edge'
                },
                {
                    string   : $window.navigator.userAgent,
                    subString: 'Chrome',
                    identity : 'Chrome'
                },
                {
                    string       : $window.navigator.userAgent,
                    subString    : 'OmniWeb',
                    versionSearch: 'OmniWeb/',
                    identity     : 'OmniWeb'
                },
                {
                    string       : $window.navigator.vendor,
                    subString    : 'Apple',
                    versionSearch: 'Version',
                    identity     : 'Safari'
                },
                {
                    prop    : $window.opera,
                    identity: 'Opera'
                },
                {
                    string   : $window.navigator.vendor,
                    subString: 'iCab',
                    identity : 'iCab'
                },
                {
                    string   : $window.navigator.vendor,
                    subString: 'KDE',
                    identity : 'Konqueror'
                },
                {
                    string   : $window.navigator.userAgent,
                    subString: 'Firefox',
                    identity : 'Firefox'
                },
                {
                    string   : $window.navigator.vendor,
                    subString: 'Camino',
                    identity : 'Camino'
                },
                {
                    string   : $window.navigator.userAgent,
                    subString: 'Netscape',
                    identity : 'Netscape'
                },
                {
                    string       : $window.navigator.userAgent,
                    subString    : 'MSIE',
                    identity     : 'Explorer',
                    versionSearch: 'MSIE'
                },
                {
                    string       : $window.navigator.userAgent,
                    subString    : 'Trident/7',
                    identity     : 'Explorer',
                    versionSearch: 'rv'
                },
                {
                    string       : $window.navigator.userAgent,
                    subString    : 'Gecko',
                    identity     : 'Mozilla',
                    versionSearch: 'rv'
                },
                {
                    string       : $window.navigator.userAgent,
                    subString    : 'Mozilla',
                    identity     : 'Netscape',
                    versionSearch: 'Mozilla'
                }
            ];

            var osData = [
                {
                    string   : $window.navigator.platform,
                    subString: 'Win',
                    identity : 'Windows'
                },
                {
                    string   : $window.navigator.platform,
                    subString: 'Mac',
                    identity : 'Mac'
                },
                {
                    string   : $window.navigator.platform,
                    subString: 'Linux',
                    identity : 'Linux'
                },
                {
                    string   : $window.navigator.platform,
                    subString: 'iPhone',
                    identity : 'iPhone'
                },
                {
                    string   : $window.navigator.platform,
                    subString: 'iPod',
                    identity : 'iPod'
                },
                {
                    string   : $window.navigator.platform,
                    subString: 'iPad',
                    identity : 'iPad'
                },
                {
                    string   : $window.navigator.platform,
                    subString: 'Android',
                    identity : 'Android'
                }
            ];

            var versionSearchString = '';

            function searchString(data)
            {
                for ( var i = 0; i < data.length; i++ )
                {
                    var dataString = data[i].string;
                    var dataProp = data[i].prop;

                    versionSearchString = data[i].versionSearch || data[i].identity;

                    if ( dataString )
                    {
                        if ( dataString.indexOf(data[i].subString) !== -1 )
                        {
                            return data[i].identity;

                        }
                    }
                    else if ( dataProp )
                    {
                        return data[i].identity;
                    }
                }
            }

            function searchVersion(dataString)
            {
                var index = dataString.indexOf(versionSearchString);

                if ( index === -1 )
                {
                    return;
                }

                return parseInt(dataString.substring(index + versionSearchString.length + 1));
            }

            var browser = searchString(browserData) || 'unknown-browser';
            var version = searchVersion($window.navigator.userAgent) || searchVersion($window.navigator.appVersion) || 'unknown-version';
            var os = searchString(osData) || 'unknown-os';

            // Prepare and store the object
            browser = browser.toLowerCase();
            version = browser + '-' + version;
            os = os.toLowerCase();

            browserInfo = {
                browser: browser,
                version: version,
                os     : os
            };

            return browserInfo;
        }

        /**
         * Generates a globally unique id
         *
         * @returns {*}
         */
        function guidGenerator()
        {
            var S4 = function ()
            {
                return (((1 + Math.random()) * 0x10000) || 0).toString(16).substring(1);
            };
            return (S4() + S4() + S4() + S4() + S4() + S4());
        }

        /**
         * Return if current device is a
         * mobile device or not
         */
        function isMobile()
        {
            return mobileDetect.mobile();
        }

        /**
         * Toggle in array (push or splice)
         *
         * @param item
         * @param array
         */
        function toggleInArray(item, array)
        {
            if ( array.indexOf(item) === -1 )
            {
                array.push(item);
            }
            else
            {
                array.splice(array.indexOf(item), 1);
            }
        }

    };

core.factory('notifyService', msNotify);
    /** @ngInject */
    function msNotify($mdToast, $document) {
        /**
        * List of service
        **/
        var service = {
            notify: notify
        };

        return service;

        /**
        * Used for displaying notification
        * 
        * @param string status (error, success),
        * @param string message message to display
        **/
        function notify(status, message) {               
            $mdToast.show({
                     template : getTemplate(status,message),   
                     position : "bottom right",
                     parent : $document.body,
                     hideDelay : 5000
                });
        };

        /**
        * Get template for displaying in notification
        *
        * @param string status (error, success),
        * @param string message message to display
        **/
        function getTemplate(status,message) {            
            var strTemplate = "<md-toast id='notify-"+status+"'>" +
                                "<div flex>" + message + "</div>"+
                                "</md-toast>";
            return strTemplate;
        };

    }

core.factory('AllConstant', AllConstant);
    function AllConstant($browser,$location,$filter)
        {
            var base_path = "http://"+$location.host()+$browser.baseHref();
            var constatnt = {
                  deleteMessage: 'Are you sure want to delete this record ?',
                  deleteImage: 'Are you sure want to delete this image ?',
                  deletePermanent: 'Are you sure want to delete this record ? Clicking Ok will delete record permanently.',
                  snsImport: 'Are you sure want to import S&S data? it will take more time.',
                  pagination: 15,
                  NoImage: base_path+'api/public/images/noimage.jpg',
                  NoDocument: base_path+'api/public/images/nodocument.png',
                  currentdate : $filter('date')(new Date(), 'yyyy-MM-dd'),
                  base_path:base_path
                }
            return constatnt;
        }

}());