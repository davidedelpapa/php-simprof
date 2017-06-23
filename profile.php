<?php
/*
 * PHP simple profiler
 * Ver 0.1 - (c) 2017, Davide Del Papa, Public Domain
 */
include_once 'simprof.php';

function listDirRecursively($dir, &$results = array()){
    $files = scandir($dir);

    foreach($files as $key => $value){
        $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
        if(!is_dir($path)) {
            $results[] = $path;
        } else if($value != "." && $value != "..") {
            listDirRecursively($path, $results);
            $results[] = $path;
        }
    }

    return $results;
}
//$file_tree = scandir(".");
$file_tree = listDirRecursively(".");

foreach($file_tree as $curr_file){
    $ext = pathinfo($curr_file, PATHINFO_EXTENSION);
    if (($ext == "php") or ($ext == "profiler")){
        $current_basename = basename($curr_file);
        if(($current_basename == "simprof.php") or ($current_basename == "profile.php")){
            // skip
        } else {
            // Execute only if 'sp_manual' is not present
            if( strpos(file_get_contents($curr_file),'sp_manual') == false) {
                // Execute and evaluate
                ob_start();
                include $curr_file;
                sp_prepare_report($curr_file);
                _sp_clean();
                ob_end_clean();
            }
        }
    }
}

sp_print_report();
