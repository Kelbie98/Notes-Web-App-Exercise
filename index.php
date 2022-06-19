<?php
// ##############################################################################
// ###
// ### Quick Notes Recording Page
// ### Author: Owen Kelbie - June 2022
// ### Usage: Displays a form listing a set of Notes and allows the Addition of a 
// ###        New Note and Select/Amendment of an Existing one.
// ##############################################################################
error_reporting(E_ALL);

// === Check for POST variables from form - default value otherwise

$action=(array_key_exists('action',$_POST)?htmlspecialchars($_POST['action']):'List');
$noteid=(array_key_exists('noteid',$_POST)?intval($_POST['noteid']):-1);
$notetext=(array_key_exists('notetext',$_POST)?trim(htmlspecialchars($_POST['notetext'])):'');

// var_dump($_POST);
// echo "<br>";
// === Set Date Time and Notes Record array

$currdtm=date("d/m/Y H:i:s");
$notes=array();
$rewrite_file = false;
$notefile='./mynotes.txt';
$errormess='';

// === Get Notes from file, if it exits
if (file_exists($notefile)) {
   $json_notes=file_get_contents($notefile);
   $notes=json_decode($json_notes);
}

// === Process the given action from the form (default = list)
switch ($action) {
  case 'List':
      if ($noteid >= 0) {
          $notedtm=$notes[$noteid][0];
          $notetitle=$notes[$noteid][1];
          $notetext=$notes[$noteid][2];
      }
//       echo "nt=($notetext)<br>";
      break;
  case 'Amend':
  case 'Add':
      $title='';
      $textAr = explode("\n", $notetext);
      foreach ($textAr as $textLn) {
          $textLn=trim($textLn);
          if (strcmp($textLn,'')){
              $title=$textLn;
              break;
          }
      }
      if (!strcmp($title,'')) {
          $errormess='Error: Note must contain some text';
          break;
      }
      $record=array($currdtm,$title,$notetext);
      if (!strcmp($action,'Add')) {
          $notes[]=$record;
          $errormess="OK: Added Note ($title).";
      } else {
          $notes[$noteid]=$record;
          $errormess="OK: Amended Note ($title).";
      }
      $noteid=-1;
      $notetext= '';
      $rewrite_file=true;
      break;
}
if ($rewrite_file) {
  $json_notes=json_encode($notes);
  file_put_contents($notefile,$json_notes);
}
// var_dump($notes);
// echo "<br>";
// echo "($notetext)<br>";
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Notes Recorder</title>
    <style>
body {
    background-color: blue;
    color: black;
    padding: 10px;
    font: icon;
    font-size: 12px;
} 
.page_head {
    background-color: darkblue;
    color: white;
    padding: 10px;
    font: Arial;
    font-size: 20px;
} 
.section_head {
    border-radius: 25px;
    border: 2px solid blue;
    color: black;
    padding: 10px;
    font: Arial;
    font-size: 18px;
    font-style: bold;
} 
.item_head {
    background-color: lightgrey;
    color: black;
    padding: 10px;
    font: Arial;
    font-size: 18px;
    font-style: bold;
} 
.label {
    background-color: lightgrey;
    color: black;
    padding: 10px;
    font: Arial;
    font-size: 12px;
    font-style: bold;
} 
.para {
    margin-left: 30px;
    margin-right: 30px;
    margin-top: 30px;
    margin-bottom: 30px;
    font: Arial;
    font-size: 14px;
    font-style: normal;
} 
.form_item {
    margin-left: 40px;
    margin-right: 40px;
    font: Arial;
    font-size: 12px;
    font-style: bold;
} 
.formbox {
    margin: 20px;
    padding: 20px;
    background-color: lightgrey;
} 
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
  font-family: Helvetica, sans-serif;
}
.vertical-scrollable {
    left: 20px;
    width: 90%;
    height:200px;
    overflow-y: scroll;
}
.selected {
    background-color: goldenrod;
}
    </style>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
  
  <script>
  function checkerror() {
    errormess='<?php echo "$errormess";?>';
    if (errormess != '') {
      alert(errormess);
    }
  }
  function selectnote(noteid) {
    document.noteform.action.value='List';
    document.noteform.noteid.value=noteid;
    document.noteform.submit();
  }
  function refreshform() {
    document.noteform.action.value='List';
    document.noteform.noteid.value=-1;
    document.noteform.notetext.value='';
    document.noteform.submit();
  }
  </script>
  
</head>
<!--Every time the page is loaded we check for error/operation result message -->
<body onload="return checkerror();">
<div class="page_head">Notes Recorder</div>
<br>
<div class="para">
<h2>Add and Amend Notes</H2><br>
</div>
<div class="row">
  <div class="col-md-6 card">
    <div class="row card-header">
      <div class="section_head ">List of Notes:</div>
    </div><br>
    <div class="vertical-scrollable card-body">
<?php 
$i=0;
// === Loop thorugh each object in the notes list and populate the selection area
foreach ($notes as $record) {
   $thisnoteid=$i;
   $sel= (($thisnoteid == $noteid)?" selected":"");
   $thisdtm=$record[0];
   $thistitle=$record[1];
   echo "      <div class=\"btn-info row${sel}\" style=\"border-radius: 5px;\" onclick=\"selectnote($i);\">\n";
   echo "        <div class=\"col-sm-1\" style=\"text-align: right;\">${thisnoteid}</div>\n";
   echo "        <div class=\"col-sm-4\">${thisdtm}</div>\n";
   echo "        <div class=\"col-sm-7\">${thistitle}</div>\n";
   echo "      </div><br>\n";
   $i++;
}
?>
    </div>
  </div>
  <div class="col-md-6 card">
    <div class="row card-header">
      <div class="section_head"><?php echo ($noteid == -1?'Add':'Amend');?> Note:</div>
    </div><br>
    <div class="row card-body">
      <div class="formbox">
        <form name="noteform" id="noteform" method="POST" target="<?php echo $_SERVER['PHP_SELF'];?>">
<?php 
if ($noteid >= 0) {
?>
          <div class="row">
            <div class="col-md-2"><div class="label">ID: <?php echo "${noteid}";?></div></div>
            <div class="col-md-4"><div class="label">Date: <?php echo "${notedtm}";?></div></div>
            <div class="col-md-6"><div class="label">Title: <?php echo "${notetitle}";?></div></div>
          </div>
<?php 
}
?>
          <div class="row">
            <div class="label">Note Details:</div>
          </div>
          <div class="row">
            <div class="form_item">
               <textarea name="notetext" id="notetext" cols="80" rows="10" wrap="hard"><?php echo "${notetext}";?></textarea>
            </div>
          </div>
          <div class="row">
            <div class="col-md-2">&nbsp;</div>
            <div class="col-md-3">
              <button type="button" class="btn btn-primary btn-sm" onclick="document.noteform.submit();"><?php echo ($noteid == -1?'Add':'Amend');?> Note</button>
            </div>
<?php 
if ($noteid >= 0) {
?>
            <div class="col-md-3">
              <button type="button" class="btn btn-primary btn-sm" onclick="refreshform();">New Note</button>
            </div>
<?php 
}
?>
            <div class="col-md-7">
              <input type="hidden" name="noteid" value="<?php echo $noteid;?>">
              <input type="hidden" name="action" value="<?php echo ($noteid == -1?'Add':'Amend');?>">
            </div>
          </div>
        </form>
      </div>
    </div><br>
  </div>
</div>
</body>
</html>