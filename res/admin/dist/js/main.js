$(function () {

    /* CKEDITOR */
    CKEDITOR.replace('description', { height: 150 });

    /* Plugin iCheck */
    $(function () {
        $('input[type=checkbox]').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });
    });

    /* Customiza os inputs do bootstrap */
    $('#image,:file').filestyle({
        text: 'Carregar',
        btnClass: 'btn-primary',
        htmlIcon: '<span class="glyphicon glyphicon-file"></span> ',
    });

    $('[data-toggle="tooltip"]').tooltip();

    /* Trata entrada de valores para o input slug */
    $('#slug').keydown(e => {
        e.target.value = string_to_slug(e.target.value);
    });

    /* Modal for create new name album */

    $('#btnSaveNewAlbum').click(() => {

        const album = $('#name_album').val();

        $.ajax({
            type: "POST",
            url: "/admin/albums/create-name",
            data: { album },
            dataType: "json",
            beforeSend: () => {
                load('open');
            },
            success: function (r) {

                if (r.success == true) {
                    var html = `<option value="">SELECIONE</option>`;
                    r.data.forEach((e) => {
                        html += `<option value="${e.id}">${e.album}</option>`;
                    });
                    
                    $('#id_photos_albums').html(html);
                    $('#modalNewAlbum').modal('hide');
                    $('#id_photos_albums option:contains("' + album + '")').attr('selected', true);
                    $('#name_album').val('');
                } else {
                    $('.show-error').html(r.msg).addClass('text-danger');
                }
            }
            ,
            complete: () => {
                load('close');
            }

        });
    });

});

/*===============================*/
/*=======ALL FUNCTIONS===========*/
/*===============================*/

/* Function of create slug */
function string_to_slug(str) {
    str = str.replace(/^\s+|\s+$/g, '');
    str = str.toLowerCase();

    var from = "àáãäâèéëêìíïîòóöôùúüûñç·/_,:;";
    var to = "aaaaaeeeeiiiioooouuuunc------";

    for (var i = 0, l = from.length; i < l; i++) {
        str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
    }

    str = str.replace(/[^a-z0-9 -]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-');

    return str;
}

/*Function of loading*/
function load(action) {
    var load_div = $('.ajax_load');
    if (action === 'open') {
        load_div.fadeIn().css('display', 'flex');
    } else {
        load_div.fadeOut();
    }
}

