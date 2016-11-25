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

            

            // Toolbar
            'app.toolbar',

            // Quick panel
            /*'app.quick-panel',*/

            // LOGIN and DASHBOARD
            'app.login',


            //Admin settings
            'app.admin',


            // Client
            'app.client',
            
            //Order
            'app.order',

            //Order
            'app.invoices',
            
             //puchase order
            'app.purchaseOrder',
            
            // Receiving
            'app.receiving',

            
            'ngTasty',

            // image uploading module
            'naif.base64',

            //Company settings
            'app.settings',

            //Art
            'app.art',

            //Finishing
            'app.finishing',

            // Custom Product
            'app.customProduct',

            //shipping
            'app.shipping',
            
            // Production
            'app.production',

            // Finishing Scheduling
            'app.finishingQueue',

            // Navigation
            'app.navigation',

            // DRAG AND UPLOAD
            'dndLists',

            //Misc data
            'app.misc',
            'xeditable',
            'angular-clipboard',

            // GOOGLE ADDRESS API
            'vsGoogleAutocomplete'
        ]);
})();