(function ($) {
    var groupsCount = 0;
    var keys = [];
    var groups = {};
    var dataImport = {};
    $('#calendar-import-status').html('Loading...');

    $.get('/admin/config/content/calendar-import/report', function (data) {
        dataImport = data['import'];
        console.log(dataImport);
        groups = data['import']['groups'];
        $('#calendar-import-status').html('Ready!');
        $('#calendar-import-groups-count').html(Object.keys(groups).length);
        $('#calendar-import-reoccurring').html(data['import']['reoccurring']);
        $('#calendar-import-buildings-count').html(Object.keys(data['import']['buildings']).length);
        $('#calendar-import-keywords-parent-count').html(Object.keys(data['import']['keywordsParent']).length);
        $('#calendar-import-keywords-count').html(Object.keys(data['import']['keywords']).length);
    });

    $(document).ready(function () {
        $('#calendar-import-start').click(function () {
            $('#calendar-import-start').hide();
            $('#calendar-import-cancel').show();
            keys = Object.keys(groups);
            groupsCount = keys.length;
            importGroups();
        });
        $('#calendar-import-cancel').click(function () {
            $('#calendar-import-start').show();
            $('#calendar-import-cancel').hide();
        });
        $('#calendar-import-show-keywords-parent').click(function () {
            $('#calendar-import-output').html(JSON.stringify(dataImport['keywordsParent'], null, ' '));
        });
        $('#calendar-import-show-keywords').click(function () {
            $('#calendar-import-output').html(JSON.stringify(dataImport['keywords'], null, ' '));
        });
    });

    function importGroups() {
        if (keys.length === 0 || $('#calendar-import-cancel').is(':hidden')) {
            return done();
        }
        var id = keys.pop();
        $('#calendar-import-status').html('Importing ' + (groupsCount - keys.length) + ' of ' + groupsCount);
        $.post('/admin/config/content/calendar-import/import', {'group': groups[id]}, function (data) {
            importGroups();
        });
    }

    function done() {
        $('#calendar-import-status').html('Done');
        $('#calendar-import-start').show();
        $('#calendar-import-cancel').hide();
    }

})(jQuery);