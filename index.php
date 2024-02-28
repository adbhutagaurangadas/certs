<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Сертификаты</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <script src="jquery-3.7.0.min.js"></script>
        <script>
var links = [];
var id = null;
function downloadAll() {
  var link = document.createElement('a');
  link.style.display = 'none';
  document.body.appendChild(link);
  for (var i = 0; i < links.length; i++) {
    link.setAttribute('download', links[i][2]);
    link.setAttribute('href', 'results/' + id + '/' + links[i][2]);
    link.click();
  }
  document.body.removeChild(link);
}
function submit() {
    var r = false;
    if(!$('#template').val()) {
        !$('#template').addClass('is-invalid');
        r = true;
    }
    if(!$('#num').val() || isNaN($('#num').val())) {
        !$('#num').addClass('is-invalid');
        r = true;
    }
    if(!$('#teacher').val()) {
        !$('#teacher').addClass('is-invalid');
        r = true;
    }
    if(!$('#students').val()) {
        !$('#students').addClass('is-invalid');
        r = true;
    }
    if(r) return;
    $('#submit').prop('disabled', true);
    $('#submit-spinner').removeClass('d-none');
    $('#table').addClass('d-none');
    $('#table-body').html('');
    links = [];
    id = null;
    $.post('dl.php', {
        template: $('#template').val(),
        num: $('#num').val(),
        teacher: $('#teacher').val(),
        signature: $('#signature').prop('checked') ? 1 : 0,
        students: $('#students').val()
    }, function(data, status) {
        $('#submit').prop('disabled', false);
        $('#submit-spinner').addClass('d-none');
        $('#table').removeClass('d-none');
        d = JSON.parse(data);
        $('#zip').attr('href', 'zip.php?f=' + d.f);
        links = d.pdfs;
        id = d.f;
        links.forEach((v) => {
            $('#table-body').html(function(idx, cur) {
                return cur + '<tr><td>' + v[0] + '</td><td>' + v[1] + '</td><td class="dlpdf"><a href="results/' + id + '/' + v[2] + '" target="_blank">Открыть</a></td></tr>';
            });
        });
    })
}
        </script>
        <style>
            .dlpdf {
                text-align: right;
            }
            .header {
                font-family: "Times New Roman";
                padding-top: 30px;
                color: #856210;
            }
        </style>
    </head>
    <body class="bg-light">
    <div class="container">
        <div class="text-center mt-5 mb-5">
            <img src="logo.png" width="100" height="120">
            <h1 class="header">СЕРТИФИКАТЫ</h1>
        </div>
        <div class="row mb-5">
            <div class="col-lg-7 mx-auto">
                <div class="container">
                        <div class="row g-3 mb-3">
                            <div class="col-9">
                                <select
                                    class="form-select"
                                    name="template"
                                    id="template"
                                    onchange="$(this).removeClass('is-invalid');"
                                >
                                    <option selected value="">Шаблон</option>
                                    <?php
                                    foreach(glob("templates/*.docx") as $t) {
                                        print "<option value='".substr($t, 10)."'>".substr($t, 10, -5)."</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-3">
                                <div class="input-group">
                                    <input
                                        type="text"
                                        class="form-control"
                                        placeholder="№"
                                        id="num"
                                        onchange="$(this).removeClass('is-invalid');"
                                    >
                                </div>
                            </div>
                        </div>
                        <?php if(isset($_GET['new'])) { ?>
                            <div class="mb-3">
                                <label for="formFile" class="form-label">Загрузить новый шаблон</label>
                                <input class="form-control" type="file" id="formFile">
                            </div>
                        <?php } ?>
                        <div class="input-group mb-3">
                            <input
                                type="text"
                                class="form-control"
                                placeholder="Преподаватель"
                                id="teacher"
                                onchange="$(this).removeClass('is-invalid');"
                            >
                            <div class="input-group-text">
                                <input
                                    class="form-check-input mt-0 me-2"
                                    id="signature"
                                    type="checkbox"
                                    value="1"
                                    aria-label=""
                                    name="signature"
                                >
                                <label class="form-check-label" for="signature">Подпись</label>
                            </div>
                        </div>
                        <div class="input-group mb-3">
                            <textarea
                                class="form-control"
                                placeholder="Студенты"
                                rows="10"
                                id="students"
                                onchange="$(this).removeClass('is-invalid');"
                            ></textarea>
                        </div>
                        <div class="input-group mb-3">
                            <button
                                class="btn btn-primary w-100"
                                onclick="submit();"
                                id="submit"
                            >
                                <span id="submit-spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                Создать
                            </button>
                        </div>
                        <table id="table" class="table d-none mb-5">
                            <thead>
                            <tr>
                                <th scope="col">№</th>
                                <th scope="col">Имя</th>
                                <th scope="col" class="dlpdf"><a href="" id="zip">Скачать все</a></th>
                            </tr>
                            </thead>
                            <tbody id="table-body">
                            </tbody>
                        </table>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    </body>
</html>
