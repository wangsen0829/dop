
require.config({
    paths: {
        'jquery-colorpicker': '../addons/customcharts/js/jquery.colorpicker.min',
    },
    shim: {
        'jquery-colorpicker': {
            deps: ['jquery'],
            exports: '$.fn.extend'
        }
    }
});