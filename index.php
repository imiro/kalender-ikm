<?php

require_once "./kalenderIKM.php";

if( !empty($_POST) )
{
  $kalender = new kalenderIKM();
  if( $es = $kalender
      ->createEvent($_POST['penyelenggara'], $_POST['judul'],
                    $_POST['start'], $_POST['end'], $_POST['tipe']) )
  {
    $pesan = new StdClass;
    $pesan->pesan = "Acara \"<a href='{$es->htmlLink}' >" . $_POST['judul'] . '</a>\" berhasil ditambahkan.';
    $pesan->class = 'bg-success';
  } else {
    $pesan = new StdClass;
    $pesan->pesan = $kalender->error;
    $pesan->class = 'bg-danger';
  }
}

// TODO: https://profromgo.com/blog/google-calendar-responsive/ {priority: minor}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title><?php echo $title; ?></title>

    <!-- Bootstrap -->
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/bootstrap-datetimepicker.min.css" >

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
    <script src="assets/moment.min.js"></script>
    <script src="assets/bootstrap-datetimepicker.min.js"></script>

  </head>
  <body>
    <div class="container">
      <?php if (isset($pesan)): ?>
      <div class="row" style='max-width: 800px; margin: auto;'>
        <blockquote class='<?php echo $pesan->class; ?>'>
          <?php echo $pesan->pesan; ?>
        </blockquote>
      </div>
      <?php endif; ?>
      <div class="row" class='text-center'>
        <iframe src="https://calendar.google.com/calendar/embed?src=r54pt8hqhv8l05hm31pk7vrsfk%40group.calendar.google.com&ctz=Asia/Jakarta" style="border: 0; margin: auto; display: block;" width="800" height="600" frameborder="0" scrolling="no"></iframe>
      </div>
      <form class="form" action="index.php" method="POST" style="margin: auto; max-width: 800px;">
        <div class="form-group">
          <label for="penyelenggara" class='control-label'>Penyelenggara</label>
          <select name="penyelenggara" value="" class='form-control'>
            <option value="BEM">BEM</option>
            <option value="BPM">BPM</option>
            <option value="MA">MA</option>
            <option value="LPP">LPP</option>
            <option value="TBM">TBM</option>
            <option value="AMSA">AMSA</option>
            <option value="BFM">BFM</option>
            <option value="CIMSA">CIMSA</option>
            <option value="FSI">FSI</option>
            <option value="PO">PO</option>
            <option value="KMK">KMK</option>
            <option value="KMB">KMB</option>
            <option value="KMHD">KMHD</option>
            <option value="Bursa">Bursa Kedokteran</option>
            <option value="Kafetaria">Kafetaria</option>
            <option value="STUNICA">STUNICA</option>
            <option value="ST1">Senat Tingkat 1 (2016)</option>
            <option value="ST2">Senat Tingkat 2 (2015)</option>
            <option value="ST3">Senat Tingkat 3 (2014)</option>
            <option value="ST4">Senat Tingkat 4 (2013)</option>
            <option value="ST5">Senat Tingkat 5 (2012)</option>
          </select>
        </div>
        <div class="form-group">
          <label for="judul" class='control-label'>Nama acara</label>
          <input type="text" name="judul" value="<?php if (isset($_POST['judul'])) echo $_POST['judul']; ?>" class='form-control'>
        </div>
        <div class="form-group">
          <label for="start" class='control-label'>Waktu mulai</label>
          <div id="datetimepicker1" class="input-group date">
           <input class="form-control" data-format="DD MMM YYYY hh:mm [GMT+7]" type="text" name="start" value="<?php if (isset($_POST['start'])) echo $_POST['start']; ?>"></input>
           <span class="input-group-addon glyphicon glyphicon-calendar">
             <i data-time-icon="glyphicon-time" data-date-icon="glyphicon-calendar">
             </i>
           </span>
         </div>
        </div>
        <div class="form-group">
          <label for="end" class='control-label'>Waktu selesai</label>
          <div id="datetimepicker2" class="input-group date">
           <input class="form-control" data-format="DD MMM YYYY hh:mm [GMT+7]" type="text" name="end" value="<?php if (isset($_POST['end'])) echo $_POST['end']; ?>"></input>
           <span class="input-group-addon glyphicon glyphicon-calendar">
             <i data-time-icon="glyphicon-time" data-date-icon="glyphicon-calendar">
             </i>
           </span>
         </div>
        </div>
        <div class="form-group">
          <label for="tipe" class='control-label'>Target Acara</label>
          <select class="form-control" name="tipe">
            <option value='1'>Umum</option>
            <option value='2'>IKM</option>
            <option value='3'>Internal badan/himpunan</option>
            <option value='4'>Lainnya</option>
          </select>
        </div>
        <div class="form-group text-right">
          <input type="submit" name="submit" value="Tambah" class='btn btn-default'>
        </div>
      </form>
    </div>

    <script type="text/javascript">
      $("#datetimepicker1").datetimepicker();
      $("#datetimepicker2").datetimepicker();
    </script>
  </body>
</html>
