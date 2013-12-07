//var n = noty({text: 'noty - a jquery notification library!'});
/*$.noty.defaults = {
 layout: 'top',
 theme: 'defaultTheme',
 type: 'alert',
 text: '',
 dismissQueue: true, // If you want to use queue feature set this true
 template: '<div class="noty_message"><span class="noty_text"></span><div class="noty_close"></div></div>',
 animation: {
 open: {height: 'toggle'},
 close: {height: 'toggle'},
 easing: 'swing',
 speed: 500 // opening & closing animation speed
 },
 timeout: false, // delay for closing event. Set false for sticky notifications
 force: false, // adds notification to the beginning of queue when set to true
 modal: false,
 maxVisible: 5, // you can set max visible notification for dismissQueue true option
 closeWith: ['click'], // ['click', 'button', 'hover']
 callback: {
 onShow: function() {},
 afterShow: function() {},
 onClose: function() {},
 afterClose: function() {}
 },
 buttons: false // an array of buttons
 };*/

$(document).ready(function() {
    $("#int_modulo_id").change(function() {
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "listamenu",
            data: "id_modulo=" + $(this).val(),
            success: function(datos) {
                $("#int_menu_id").empty();
                $.each(datos.data, function(id, value) {
                    $("<option />", {
                        val: id,
                        text: value
                    }).appendTo($("#int_menu_id"));
                });
            }
        });
    });
    //buscador de usuarios en el submenu accesos
    $("#form-search-user").submit(function(){
        var formData = $("#form-search-user").serializeArray();
        $.ajax({
            type: "POST",
            dataType: "html",
            url: "acceso/search-user-ajax",
            data: formData,
            success: function(respuesta) {
                $("#divResultado").html(respuesta);
            }
        });
        return false;
    });
});

//notificaciones
/**
 * Método para eliminar un modulo
 * @author Johnny Huamani<johnny1402@gmail.com>
 * @param int module_id
 * @returns string
 */
function delete_module(module_id) {
    var n = noty({
        text: 'Realmente deseas continuar?',
        type: 'confirm',
        layout: 'top',
        modal: true,
        animation: {
            open: {height: 'toggle'},
            close: {height: 'toggle'},
            easing: 'swing',
            speed: 500 // opening & closing animation speed
        },
        buttons: [
            {addClass: 'btn btn-primary', text: 'Ok', onClick: function($noty) {
                    delete_module_in_server(module_id);
                    $noty.close();
                    //noty({force: true, text: 'You clicked "Ok" button', type: 'success', layout: 'top'});
                }
            },
            {addClass: 'btn btn-danger', text: 'Cancel', onClick: function($noty) {
                    $noty.close();
                    //noty({force: true, text: 'You clicked "Cancel" button', type: 'error', layout: 'top'});
                }
            }
        ],
        /*timeout: true, */
        //closeWith: ['hover'],
        callback: {
            /*afterClose: function() {
             noty({
             text: '<strong>Hehe!</strong> <br /> Sorry, you can catch me now.',
             type: 'alert',
             layout: 'topRight',
             closeWith: ['click'],
             });
             }*/
        }
    });
}

/**
 * Método para eliminar un modulo en el servidor
 * @author Johnny Huamani<johnny1402@gmail.com>
 * @param int module_id
 * @returns string
 */
function delete_module_in_server(module_id) {
    $.ajax({
        type: "POST",
        dataType: "json",
        url: "modulos/delete",
        data: "module_id=" + module_id,
        success: function(datos) {
            if (datos.result) {
                //limpiamos el registro del modulo a eliminar
                $("#module_" + module_id).empty();
                //mostramos el mensaje a mostrar
                noty({force: true, text: 'El modulo se eliminó en forma satisfactoria', type: 'success', layout: 'top'});
            } else {
                noty({force: true, text: 'Este modulo no está configurado para eliminar', type: 'information', layout: 'top'});
            }
        }
    });
}

/**
 * Método para eliminar un modulo
 * @author Johnny Huamani<johnny1402@gmail.com>
 * @param int module_id
 * @returns string
 */
function delete_menu(menu_id) {
    var n = noty({
        text: 'Realmente deseas continuar, al eliminar un menú posiblemente se eliminen sus accesos?',
        type: 'confirm',
        layout: 'top',
        modal: true,
        animation: {
            open: {height: 'toggle'},
            close: {height: 'toggle'},
            easing: 'swing',
            speed: 500 // opening & closing animation speed
        },
        buttons: [
            {addClass: 'btn btn-primary', text: 'Ok', onClick: function($noty) {
                    delete_menu_in_server(menu_id);
                    $noty.close();
                    //noty({force: true, text: 'You clicked "Ok" button', type: 'success', layout: 'top'});
                }
            },
            {addClass: 'btn btn-danger', text: 'Cancel', onClick: function($noty) {
                    $noty.close();
                    //noty({force: true, text: 'You clicked "Cancel" button', type: 'error', layout: 'top'});
                }
            }
        ],
        /*timeout: true, */
        //closeWith: ['hover'],
        callback: {
            /*afterClose: function() {
             noty({
             text: '<strong>Hehe!</strong> <br /> Sorry, you can catch me now.',
             type: 'alert',
             layout: 'topRight',
             closeWith: ['click'],
             });
             }*/
        }
    });
}

/**
 * Método para eliminar un modulo en el servidor
 * @author Johnny Huamani<johnny1402@gmail.com>
 * @param int module_id
 * @returns string
 */
function delete_menu_in_server(menu_id) {
    $.ajax({
        type: "POST",
        dataType: "json",
        url: "menus/delete",
        data: "menu_id=" + menu_id,
        success: function(datos) {
            if (datos.result) {
                //limpiamos el registro del modulo a eliminar
                $("#menu_" + menu_id).empty();
                //mostramos el mensaje a mostrar
                noty({force: true, text: 'El menú se eliminó en forma satisfactoria', type: 'success', layout: 'top'});
            } else {
                noty({force: true, text: 'Este menú no está configurado para eliminar', type: 'information', layout: 'top'});
            }
        }
    });
}

/**
 * Método para eliminar un modulo
 * @author Johnny Huamani<johnny1402@gmail.com>
 * @param int module_id
 * @returns string
 */
function delete_submenu(submenu_id) {
    var n = noty({
        text: 'Realmente deseas continuar, al eliminar un submenú posiblemente se eliminen sus accesos?',
        type: 'confirm',
        layout: 'top',
        modal: true,
        animation: {
            open: {height: 'toggle'},
            close: {height: 'toggle'},
            easing: 'swing',
            speed: 500 // opening & closing animation speed
        },
        buttons: [
            {addClass: 'btn btn-primary', text: 'Ok', onClick: function($noty) {
                    delete_submenu_in_server(submenu_id);
                    $noty.close();
                    //noty({force: true, text: 'You clicked "Ok" button', type: 'success', layout: 'top'});
                }
            },
            {addClass: 'btn btn-danger', text: 'Cancel', onClick: function($noty) {
                    $noty.close();
                    //noty({force: true, text: 'You clicked "Cancel" button', type: 'error', layout: 'top'});
                }
            }
        ],
        /*timeout: true, */
        //closeWith: ['hover'],
        callback: {
            /*afterClose: function() {
             noty({
             text: '<strong>Hehe!</strong> <br /> Sorry, you can catch me now.',
             type: 'alert',
             layout: 'topRight',
             closeWith: ['click'],
             });
             }*/
        }
    });
}
/**
 * Método para eliminar un modulo en el servidor
 * @author Johnny Huamani<johnny1402@gmail.com>
 * @param int module_id
 * @returns string
 */
function delete_submenu_in_server(submenu_id) {
    $.ajax({
        type: "POST",
        dataType: "json",
        url: "submenus/delete",
        data: "submenu_id=" + submenu_id,
        success: function(datos) {
            if (datos.result) {
                //limpiamos el registro del modulo a eliminar
                $("#submenu_" + submenu_id).empty();
                //mostramos el mensaje a mostrar
                noty({force: true, text: 'El submenú se eliminó en forma satisfactoria', type: 'success', layout: 'top'});
            } else {
                noty({force: true, text: 'Este submenú no está configurado para eliminar', type: 'information', layout: 'top'});
            }
        }
    });
}


/**
 * Método para eliminar un grupo
 * @author Johnny Huamani<johnny1402@gmail.com>
 * @param int grupo_id
 * @returns string
 */
function delete_grupo(grupo_id) {
    var n = noty({
        text: 'Realmente deseas continuar, al eliminar un grupo posiblemente se eliminen sus accesos?',
        type: 'confirm',
        layout: 'top',
        modal: true,
        animation: {
            open: {height: 'toggle'},
            close: {height: 'toggle'},
            easing: 'swing',
            speed: 500 // opening & closing animation speed
        },
        buttons: [
            {addClass: 'btn btn-primary', text: 'Ok', onClick: function($noty) {
                    delete_grupo_in_server(grupo_id);
                    $noty.close();
                    //noty({force: true, text: 'You clicked "Ok" button', type: 'success', layout: 'top'});
                }
            },
            {addClass: 'btn btn-danger', text: 'Cancel', onClick: function($noty) {
                    $noty.close();
                    //noty({force: true, text: 'You clicked "Cancel" button', type: 'error', layout: 'top'});
                }
            }
        ],
        /*timeout: true, */
        //closeWith: ['hover'],
        callback: {
            /*afterClose: function() {
             noty({
             text: '<strong>Hehe!</strong> <br /> Sorry, you can catch me now.',
             type: 'alert',
             layout: 'topRight',
             closeWith: ['click'],
             });
             }*/
        }
    });
}
/**
 * Método para eliminar un grupo en el servidor
 * @author Johnny Huamani<johnny1402@gmail.com>
 * @param int grupo_id
 * @returns string
 */
function delete_grupo_in_server(grupo_id) {
    $.ajax({
        type: "POST",
        dataType: "json",
        url: "grupo/delete",
        data: "grupo_id=" + grupo_id,
        success: function(datos) {
            if (datos.result) {
                //limpiamos el registro del modulo a eliminar
                $("#grupo_" + grupo_id).empty();
                //mostramos el mensaje a mostrar
                noty({force: true, text: 'El grupo se eliminó en forma satisfactoria', type: 'success', layout: 'top'});
            } else {
                noty({force: true, text: 'Este grupo no está configurado para eliminar', type: 'information', layout: 'top'});
            }
        }
    });
}
/**
 * Método para eliminar un usuario
 * @author Johnny Huamani<johnny1402@gmail.com>
 * @param int grupo_id
 * @returns string
 */
function delete_user(grupo_id) {
    var n = noty({
        text: 'Realmente deseas continuar, al eliminar un usuario posiblemente se eliminen sus accesos?',
        type: 'confirm',
        layout: 'top',
        modal: true,
        animation: {
            open: {height: 'toggle'},
            close: {height: 'toggle'},
            easing: 'swing',
            speed: 500 // opening & closing animation speed
        },
        buttons: [
            {addClass: 'btn btn-primary', text: 'Ok', onClick: function($noty) {
                    delete_user_in_server(grupo_id);
                    $noty.close();
                    //noty({force: true, text: 'You clicked "Ok" button', type: 'success', layout: 'top'});
                }
            },
            {addClass: 'btn btn-danger', text: 'Cancel', onClick: function($noty) {
                    $noty.close();
                    //noty({force: true, text: 'You clicked "Cancel" button', type: 'error', layout: 'top'});
                }
            }
        ],
        /*timeout: true, */
        //closeWith: ['hover'],
        callback: {
            /*afterClose: function() {
             noty({
             text: '<strong>Hehe!</strong> <br /> Sorry, you can catch me now.',
             type: 'alert',
             layout: 'topRight',
             closeWith: ['click'],
             });
             }*/
        }
    });
}
/**
 * Método para eliminar un usuario en el servidor
 * @author Johnny Huamani<johnny1402@gmail.com>
 * @param int user_id
 * @returns string
 */
function delete_user_in_server(user_id) {
    $.ajax({
        type: "POST",
        dataType: "json",
        url: "usuario/delete",
        data: "user_id=" + user_id,
        success: function(datos) {
            if (datos.result) {
                //limpiamos el registro del modulo a eliminar
                $("#user_" + user_id).empty();
                //mostramos el mensaje a mostrar
                noty({force: true, text: 'El usuario se eliminó en forma satisfactoria', type: 'success', layout: 'top'});
            } else {
                noty({force: true, text: 'Este usuario no está configurado para eliminar', type: 'information', layout: 'top'});
            }
        }
    });
}