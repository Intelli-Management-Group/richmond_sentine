$(function () {
    var
        $table = $('#category_list'),
        rows = $table.find('tr');

    rows.each(function (index, row) {
        var
            $row = $(row),
            level = $row.data('level'),
            id = $row.data('id'),
            $columnName = $row.find('td[data-column="name"]'),
            children = $table.find('tr[data-parent="' + id + '"]');

        if (children.length) {
            var expander = $columnName.prepend('' +
                '<span class="fa fa-plus-square-o fa-lg"></span>' +
                '');

            children.hide();

            expander.on('click', function (e) {
                var $target = $(e.target);
                if ($target.hasClass('fa-plus-square-o')) {
                    $target
                        .removeClass('fa-plus-square-o')
                        .addClass('fa-minus-square-o');

                    children.show();
                } else {
                    $target
                        .removeClass('fa-minus-square-o')
                        .addClass('fa-plus-square-o');

                    reverseHide($table, $row);
                }
            });
        }

        // $columnName.prepend('' +
        //     '<span class="treegrid-indent" style="width:' + 15 * level + 'px"></span>' +
        //     '');
    });

    // Reverse hide all elements
    reverseHide = function (table, element) {
        var
            $element = $(element),
            id = $element.data('id'),
            children = table.find('tr[data-parent="' + id + '"]');

        if (children.length) {
            children.each(function (i, e) {
                reverseHide(table, e);
            });

            $element
                .find('.fa-minus-square-o')
                .removeClass('fa-minus-square-o')
                .addClass('fa-plus-square-o');

            children.hide();
        }
    };
});
