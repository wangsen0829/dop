define(['jquery', 'bootstrap', 'frontend', 'form', 'template','jSignature'], function ($, undefined, Frontend, Form, Template) {
    var validatoroptions = {
        invalid: function (form, errors) {
            $.each(errors, function (i, j) {
                Layer.msg(j);
            });
        }
    };
    var Controller = {
        photos: function () {
            Form.api.bindevent($("#photos"));
        },
    };
    return Controller;
});
