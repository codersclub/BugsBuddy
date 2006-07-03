<?php

/*
  If somebody wants to view a bug
*/

function getdownload() {
  $returnValue = '';
  $files = Array();
  $allowedFileTypes = Array(".zip", ".rar", ".tar.gz");
  $hdir = @opendir('downloads/');
  if($hdir) {
    while($file = readdir($hdir)) {
      if((substr($file,-2) != '/.') && (substr($file,-3) != '/..')) {
        if(!is_dir($file)) {
          foreach($allowedFileTypes as $allowedFileType) {
            if(substr($file, 0-strlen($allowedFileType)) == $allowedFileType) {
              array_push($files, $file);
            }
          }
        }
      }
    }
    @closedir($hdir);
    sort($files);
    for ($i=0; $i<count($files); $i++) {
      if ($i == count($files)-1) {
        if (is_file('CHANGELOG')) {
          $changelog = '<a href="CHANGELOG">CHANGELOG</a>';
        } else {
          $changelog = '<a class="inactive" href="#">CHANGELOG</a>';
        }
        if (is_file('TODO')) {
          $todo = '<a href="TODO">TODO</a>';
        } else {
          $todo = '<a class="inactive" href="#" style="">TODO</a>';
        }
        $returnValue .= '<a href="downloads/'.$files[$i].'">'.$files[$i].'</a>&nbsp;'.$changelog.'&nbsp;'.$todo.'<br />';

//        $returnValue .= '<a href="downloads/'.$file.'">'.$file.'</a><br />';
      } else {
        $returnValue .= '<a href="downloads/'.$files[$i].'">'.$files[$i].'</a><br />';
      }
    }
  } else {
    $returnValue .= 'Er zijn geen bestanden beschikbaar om te downloaden<br />';
  }

  return $returnValue;
  //$returnValue .= '<a href="downloads/bugsbunny v0.0.1.0.zip">bugsbunny v0.0.1.0.zip</a><br />';
  
/*
  return ''.
    '<a href="bugsbunny v0.0.1.0.zip">bugsbunny v0.0.1.0.zip</a><br />'.
    '<a href="bugsbunny v0.0.2.0.zip">bugsbunny v0.0.2.0.zip</a><br />'.
    '<a href="bugsbunny v0.0.2.1.zip">bugsbunny v0.0.2.1.zip</a>';
*/
}

?>