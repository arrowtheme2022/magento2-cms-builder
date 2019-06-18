define([
    'jquery',
    'magnificPopup'
], function ($, magnificPopup) {
    "use strict";
    return {
        displayContent: function (prodUrl) {
            if (!prodUrl.length) {
                return false;
            }
            var url = window.require.s.head.baseURI + 'mgs_quickview/index/updatecart';
            $.magnificPopup.open({
                items: {
                    src: prodUrl
                },
                type: 'iframe',
                removalDelay: 300,
                mainClass: 'mfp-fade mfp-mgs-quickview-frame',
                closeOnBgClick: true,
                preloader: true,
                tLoading: '',
                callbacks: {
                    open: function () {
                        $('.mfp-preloader').css('display', 'block');
                    },
                    beforeClose: function () {
                        $('[data-block="minicart"]').trigger('contentLoading');
                        $.ajax({
                            url: url,
                            method: "POST"
                        });
                    },
                    close: function () {
                        $('.mfp-preloader').css('display', 'none');
                    }
                }
            });
        }
    };
});
