require(['hook'], function (hookQueue) {
    hookQueue.register(function (Core) {

        var done = false;

        Core.Mediator.subscribe('after:block-toolbar:shown', function () {
            if (!done) {
                $('#select-categories-blocks-contrib')
                    .val('favoris')
                    .trigger('change');

                done = true;
            }
        });
    });
});