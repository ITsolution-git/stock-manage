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
            // Custom Product
            'app.customProduct'
        ]);
})();