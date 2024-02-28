<?php
function zip_folder ($input_folder, $output_zip_file) {
    $zipClass = new ZipArchive();
    if($input_folder !== false && $output_zip_file !== false)
    {
        $res = $zipClass->open($output_zip_file, \ZipArchive::CREATE);
        if($res === TRUE)   {
            // Add a Dir with Files and Subdirs to the archive
            $foldername = basename($input_folder);
//            $zipClass->addEmptyDir($foldername);
            $foldername = '';
            $input_folder .= '/';
            // Read all Files in Dir
            $dir = opendir($input_folder);
            while ($file = readdir($dir))    {
                if ($file == '.' || $file == '..') continue;
                // Rekursiv, If dir: GoodZipArchive::addDir(), else ::File();
                $do = (filetype( $input_folder . $file) == 'dir') ? 'addDir' : 'addFile';
                $zipClass->$do($input_folder . $file, $foldername . $file);
            }
            $zipClass->close();
        }
        else   { exit ('Could not create a zip archive, migth be write permissions or other reason. Contact admin.'); }
    }
}
zip_folder('results/'.$_GET['f'], 'results/'.$_GET['f'].'.zip');
header('Content-disposition: attachment; filename='.$_GET['f'].'.zip');
header('Content-type: application/zip');
readfile('results/'.$_GET['f'].'.zip');
unlink('results/'.$_GET['f'].'.zip');