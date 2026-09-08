$(document).ready(function() {
    // select : clear all
    $('body').on('click', '#clearAll', function() {
        $("input[type='checkbox']:checked").click();
    });
    // select : select all
    $('body').on('click', '#selectAll', function() {
        $("input[type='checkbox']:not(:checked)").click();
    });
    // select : add starred projects to selection
    $('body').on('click', '#addStar', function() {
        $("input[type='checkbox'][class='fav']:not(:checked)").click();
    });
    // select : elect only starred projects
    $('body').on('click', '#onlyStar', function() {
        $("input[type='checkbox']:checked").click();
        $("input[type='checkbox'][class='fav']:not(:checked)").click();
    });

    // project list drag-and-drop reordering
    if ($("#bigboard-project-list").length) {
        $("#bigboard-project-list").sortable({
            items: ".selitem",
            handle: ".drag-handle",
            placeholder: "sortable-placeholder",
            tolerance: "pointer",
            stop: function(event, ui) {
                var projectId = ui.item.attr('data-project-id');
                var position = ui.item.index() + 1;

                $.ajax({
                    cache: false,
                    url: '?controller=BoardAjaxController&action=moveProject&plugin=Bigboard',
                    contentType: "application/json",
                    type: "POST",
                    processData: false,
                    data: JSON.stringify({
                        "project_id": projectId,
                        "position": position
                    })
                });
            }
        });
    }
});
