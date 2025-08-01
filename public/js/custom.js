function count_chars(elm, limit) {
    var elm_val = document.getElementById(elm).value;
    if (elm_val.length <= limit) {
        limit -= elm_val.length;
    }
    document.getElementById('limit_chars_'+elm).innerHTML = limit;
    return;
}

function show_loading(fullscreen = false) {
    if (fullscreen) {
        $.busyLoadFull("show");
    } else {
        $('#modal-loading').modal();
    }
}

function hide_loading(fullscreen = false) {
    if (fullscreen) {
        $.busyLoadFull("hide");
    } else {
        $('#modal-loading').modal('hide');
    }
}