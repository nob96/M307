$(function () {
    get();

    //Update/Set
    $('#formular').on('submit', function (e) {
        //Absenden verhindern
        e.preventDefault();
        //GET-URL bilden
        var strParam = $(this).serialize();
        console.log(strParam);
        //Unterscheidung set/update
        ($.isNumeric($('#Id-input').val()) === true) ? update(strParam) : set(strParam);
    });

});

/** Setzt value in jqueryObj
 * 
 * @param {*} jqueryObj 
 * @param {*} value 
 * @param {*} blnIsRadio
 */
function setValue(jqueryObj, value, blnIsRadio) {
    $(jqueryObj).val(value);
}

/** Leert formular
 * 
 * @param {*} strFormular 
 */
function clearForm(strFormular) {
    $(`#${strFormular} input`).not(':input[type=submit]').not(':input[type=radio]').not(':input[type=checkbox]').val("");
    $(`#${strFormular} select`).val('Default');
    $(`#${strFormular} input`).prop('checked', false);
}

/** Holt id von tr
 * 
 * @param {*} thisObj 
 */
function getRowId(thisObj) {
    return $(thisObj).closest('tr').attr('id');
}



/** Erstellt Table anhand json string
 * 
 * @param {*} strJSON 
 */
function createTable(strJSON) {
    //Clear table
    $('#table').find('tr:gt(0)').remove('tr');

    $('#template_table').show();
    //Template speichern
    var template = $('#template_table').html();
    $('#template_table').hide();

    //Template füllen
    $.each(strJSON, function (index, value) {
        var datensatz = Mustache.to_html(template, value);
        $('#table tbody').append(datensatz);
    });
}

/** Löscht datensatz serverseitig und baut table neu auf
 * 
 * @param {*} intId 
 */
function deleteItem(thisObj) {
    var intId = getRowId(thisObj);
    console.log(intId);
    if (confirm("Datensatz: " + intId + " wirklich löschen?")) {
        $.ajax({
            url: `php/index.php?action=delete&Id-input=${intId}`,
            dataType: 'json',
            success: function (response) {
                fillMsg(response);
                get();
            }
        });
    }
}

/** Füllt formular aus jsonResponse
 * 
 * @param {*} thisObj 
 */
function fillForm(jsonResponse) {
    console.log(jsonResponse);
    setValue('#inventar_Geraetename-input', jsonResponse['inventar_Geraetename']);
    setValue('#inventar_Inventarnummer-input', jsonResponse['inventar_Inventarnummer']);
    setValue('#inventar_Kategorie-input', jsonResponse['inventar_Kategorie']);
    setValue('#inventar_Kaufdatum-input', jsonResponse['inventar_Kaufdatum']);
    setValue('#inventar_Bemerkung-input', jsonResponse['inventar_Bemerkung']);
    setValue('#Id-input', jsonResponse['Id']);
    setValue('#save', 'Update');
}

/**
 * 
 * @param {*} strResponse 
 */
function fillMsg(strResponse) {
    $('.response-msg-form').fadeIn(1000);
    $('.response-msg-form p').css('background-color', strResponse['bcolor']);
    $('.response-msg-form p').text(strResponse['msg']);

    setTimeout(() => {
        $('.response-msg-form').fadeOut(1000);
    }, 2000)
}

/** Holt Datensätze per ajax und baut Tabelle zusammen. Im Fehlerfall wird der serverseitige Error ausgegeben. 
 * 
 * @returns json-string
 */
function get() {
    $.ajax({
        url: 'php/index.php?action=get',
        dataType: 'json',
        success: function (strResponse) {
            if (strResponse !== null && strResponse['return'] === false) {
                fillMsg(strResponse);
            } else {
                createTable(strResponse);
            }
        }
    });
}

/** Holt 1 Datensatz anhand Id
 * 
 * @param {*} thisObj 
 */
function getById(thisObj) {
    var intId = getRowId(thisObj);
    $.ajax({
        url: `php/index.php?action=get&Id-input=${intId}`,
        dataType: 'json',
        success: function (response) {
            fillForm(response[0]);
        }
    })
}

/** Führt update serverseitig aus und baut tabelle neu
 * 
 */
function update(strParam) {
    $.ajax({
        url: `php/index.php?action=update`,
        dataType: 'json',
        data: strParam,
        success: function (response) {
            console.log(response);
            fillMsg(response);

            if (response['return']) {
                clearForm('formular');
                setValue('#save', 'Speichern');
                get();
            }
        }
    });
}

/** Führt set serverseitig aus und baut tabelle neu
 * 
 * @param {*} strParam 
 */
function set(strParam) {
    $.ajax({
        url: `php/index.php?action=set`,
        dataType: 'json',
        data: strParam,
        success: function (response) {
            fillMsg(response);
            if (response['return']) {
                clearForm('formular');
                get();

            }
        }
    });
}

/** Framework W3 responsive Sandwich-navbar
 * 
 */
function toggleNavbar() {
    var x = document.getElementById("navDemo");
    if (x.className.indexOf("w3-show") == -1) {
        x.className += " w3-show";
    } else {
        x.className = x.className.replace(" w3-show", "");
    }
}