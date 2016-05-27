(function ()
{
    'use strict';

    /**
     * Main module of the Fuse
     */
    angular
        .module('fuse', [

            // Core
            'app.core',

            //ui event
            'ui.event',

            // Navigation
            'app.navigation',

            // Toolbar
            'app.toolbar',

            // Quick panel
            /*'app.quick-panel',*/

            'app.login',
            // Client
            'app.client',
            
            //Order
            'app.order',
            
             //puchase order
            'app.purchaseOrder',
            
            // Receiving
            'app.receiving',
            
            'ngTasty',

            // image uploading module
            'naif.base64',
<<<<<<< HEAD
            //Admin settings
            'app.settings'
=======
            // Custom Product
            'app.customProduct'
>>>>>>> 9c45d127e492e6234ba0f58b144928a4cf0f804f
        ]);
})();