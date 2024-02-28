<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
require 'vendor/autoload.php';

$dir = 'results';
$objects = scandir($dir);
foreach ($objects as $object) {
    if($object != "." && $object != ".." && filemtime($dir . "/" . $object) <= time() - 60*60*24*7) {
        rrmdir($dir . "/" . $object);
    }
}
function rrmdir($dir) {
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (is_dir($dir. DIRECTORY_SEPARATOR .$object) && !is_link($dir."/".$object))
                    rrmdir($dir. DIRECTORY_SEPARATOR .$object);
                else
                    unlink($dir. DIRECTORY_SEPARATOR .$object);
            }
        }
        rmdir($dir);
    }
}

mkdir($dir.'/'.($d = time()));

$i = 0;
$pdfs = [];
foreach(explode("\n", $_POST['students']) as $s) {
    $s = trim($s);
    if($s) {
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('templates/'.$_POST['template']);
        $templateProcessor->setValue('student', $s);
        $templateProcessor->setValue('teacher', $_POST['teacher']);
        $templateProcessor->setValue('signature', $_POST['signature'] ? $_POST['teacher'] : null);
        $templateProcessor->setValue('num', $_POST['num'] + $i);
        $filepath = $templateProcessor->saveAs($dir.'/'.$d.'/'.($_POST['num']+$i).' - '.$s.'.docx');
        $pdfpath = ($_POST['num']+$i).' - '.$s.'.pdf';

        exec('export HOME=/tmp && /usr/bin/soffice --convert-to pdf:"writer_pdf_Export:SelectPdfVersion=1" \''.$dir.'/'.$d.'/'.($_POST['num']+$i).' - '.$s.'.docx'.'\' --outdir '.$dir.'/'.$d);
        unlink($dir.'/'.$d.'/'.($_POST['num']+$i).' - '.$s.'.docx');
        $pdfs[] = [$_POST['num']+$i, $s, $pdfpath];
        $i++;
    }
}
print json_encode((object) ['f' => $d, 'pdfs' => $pdfs]);
?>
