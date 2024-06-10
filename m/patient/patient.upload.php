<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// get id
$id = $_GET['id'];
/*

echo $user_hospital_id;
$id = $_GET['id'];
echo $id;

$user_hospital_id -> có 2 loại bệnh ở đây là ads và facebook
mỗi loại lại dc lưu trữ ở 1 Db khác nhau
ví dụ như ads thì lưu trữ tại patient_12
trong khi facebook thì lại là patient_14
*/

// lay file dat hẹn theo id dat hẹn và id hospital
$sql_file = "select * from file where file_IDdathen = '{$id}' and file_hospital = '{$user_hospital_id}'";
$hkt_files = $db->query($sql_file); //array



// $target_dir = "uploads/";
$target_dir = $_SERVER["DOCUMENT_ROOT"] . 'uploads/';

// file sẽ được ghi

// ban đầu tính toán thế này nhưng sau phải lưu dạng khác
$target_file1 = $target_dir . basename($_FILES["fileToUpload"]["name"]);

// lay dinh dang file
$imageFileType = strtolower(pathinfo($target_file1, PATHINFO_EXTENSION));
$fileName = $id . "-" . $_SESSION[$cfgSessionName]["username"] . "-" . time() . "." . $imageFileType;
// $fileName = $_SESSION[$cfgSessionName]["username"] . "--" . $id . "--" . date('Y-m-d--H-i-s', time()) . "." . $imageFileType;

$target_file = $target_dir . $fileName;
$uploadOk = 1;
$hkt_err = "";

// Kiểm tra nếu là yêu cầu POST và có file được gửi lên
if (isset($_POST["hkt-upload"])) {

  // echo $_FILES["fileToUpload"]["tmp_name"];exit();

  // Kiểm tra xem file có phải là hình ảnh thực sự hay không
  // $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
  // if ($check === false) {
  //   $hkt_err = "File is not an image.";
  //   $uploadOk = 0;
  // }

  // Kiểm tra nếu file đã tồn tại
  if (file_exists($target_file)) {
    $hkt_err =  "Sorry, file already exists.";
    $uploadOk = 0;
  }

  // Kiểm tra kích thước file
  if ($_FILES["fileToUpload"]["size"] > 5000000) {
    $hkt_err =  "Sorry, your file is too large.";
    $uploadOk = 0;
  }

  // Cho phép chỉ upload các loại file cụ thể (JPG, JPEG, PNG, GIF)
  $allowedFormats = ["jpg", "jpeg", "png", "gif", "docx", "doc"];
  if (!in_array($imageFileType, $allowedFormats)) {
    $hkt_err =  "Sorry, file format not allowed.";
    $uploadOk = 0;
  }

  // Kiểm tra nếu không có lỗi, thực hiện upload file
  if ($uploadOk == 1) {

    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
      // sau khi upload file lên thành công, thì insert vào db luôn

      // truong hop 1 -- neu nhu đặt hẹn đã có file tồn tại
      if (count($hkt_files) > 0 && $hkt_files[0]['file_link'] != 'null') {
        $arr_temp = json_decode($hkt_files[0]['file_link'], true);
        array_push($arr_temp, $fileName);
        $link_json = json_encode($arr_temp);
        if ($link_json) {
          $sql = "update file set file_link = '{$link_json}' where file_IDdathen = '{$id}' and file_hospital = '{$user_hospital_id}'";
        }
        // $link_json = json_encode($arr_temp, JSON_UNESCAPED_UNICODE);

        # code...
      } else {
        $link_arr = array();
        $link_arr[] = $fileName;
        $link_json = json_encode($link_arr);

        // $link_json = json_encode($link_arr, JSON_UNESCAPED_UNICODE);
        $sql = "INSERT INTO `file` (`file_id`, `file_IDdathen`, `file_hospital`, `file_link`, `file_status`) VALUES (NULL, '{$id}', '{$user_hospital_id}', '{$link_json}', '1')";
      }

      $db->query($sql);
      msg_box("File has been uploaded", history(2, $id), 1);
      // echo "The file " . htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) . " has been uploaded.";
    } else {
      msg_box("Sorry, there was an error uploading your file。", "back", 1, 5);
    }
  } else {
    msg_box($hkt_err, "back", 1, 5);
  }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="Content-Type" content="text/html;charset=gb2312">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>File Upload</title>
  <!-- <link href="/res/base.css" rel="stylesheet" type="text/css"> -->
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <link href="/res/hkt/hkt.css" rel="stylesheet" type="text/css">
</head>

<body>

  <div class="p-3 hkt-file-list">
    <div id="hkt-ajax" class="row">

      <?php foreach ($hkt_files as $dt) {
        $link_array = json_decode($dt['file_link'], true);
        foreach ($link_array as $data) { ?>
          <div class="col-md-2">
            <div class="card">
              <img src="/res/img/hkt-upload.png" class="card-img-top" alt="...">
              <div class="p-2">
                <p class="card-text"><?php echo $data ?></p>
                <a target="_blank" href="https://view.officeapps.live.com/op/view.aspx?src=https://<?php echo $_SERVER['SERVER_NAME'] ?>/uploads/<?php echo $data ?>" class="btn btn-primary btn-sm">View</a>
                <button class="btn btn-success btn-sm hkt-delete" data-id="<?php echo $dt['file_id'] ?>" data-link="<?php echo $data; ?>">Delete</button>
              </div>
            </div>
          </div>

      <?php }
      } ?>

    </div>
  </div>


  <div class="hkt-file-upload p-4">
    <!-- Form for file upload -->
    <form method="post" id="uploadForm" enctype="multipart/form-data">
      <!-- <form id="uploadForm" enctype="multipart/form-data"> -->
      <div class="form-group">
        <label for="file">Choose File:</label>
        <input type="file" class="form-control-file" id="fileToUpload" name="fileToUpload" required>
      </div>

      <button type="submit" name="hkt-upload" class="btn btn-primary btn-sm">Upload</button>
      <button onClick="history.back()" class="btn btn-danger btn-sm">Back</button>
    </form>

  </div>

  <!-- Bootstrap JS and dependencies (jQuery, Popper.js) -->
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      // var elements = document.querySelectorAll(".hkt-delete");
      var hkt_ajax = document.getElementById("hkt-ajax");

      // elements.forEach(function(element) {
      //   // element.addEventListener("click", function() {
      //   //   var fileID = this.getAttribute("data-id");
      //   //   var fileLink = this.getAttribute("data-link");
      //   //   var xhr = new XMLHttpRequest();
      //   //   xhr.open("POST", "/res/hkt/ajax.php", true);
      //   //   xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      //   //   xhr.onreadystatechange = function() {
      //   //     if (xhr.readyState == 4 && xhr.status == 200) {
      //   //       var response = xhr.responseText;
      //   //       hkt_ajax.innerHTML  = response;
      //   //     }
      //   //   };
      //   //   xhr.send("id=" + encodeURIComponent(fileID) + "&link=" + fileLink);
      //   // });
      // });



      // Thay thế document bằng một phần tử cha hoặc phần tử gần
      document.addEventListener("click", function(event) {
        var targetElement = event.target;

        // Kiểm tra xem phần tử được click có phải là .hkt-delete hay không
        if (targetElement.classList.contains("hkt-delete")) {
          var fileID = targetElement.getAttribute("data-id");
          var fileLink = targetElement.getAttribute("data-link");

          var xhr = new XMLHttpRequest();
          xhr.open("POST", "/res/hkt/ajax.php", true);
          xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
          xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
              var response = xhr.responseText;
              hkt_ajax.innerHTML = response;
            }
          };
          xhr.send("id=" + encodeURIComponent(fileID) + "&link=" + fileLink);
        }
      });




    });
  </script>

</body>

</html>