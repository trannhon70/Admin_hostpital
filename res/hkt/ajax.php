<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

require __DIR__ .  "/../../core/core.php";
// require __DIR__ . "/../../core/function.php";
// Lấy dữ liệu từ yêu cầu POST
$id = isset($_POST['id']) ? $_POST['id'] : null;
$link = isset($_POST['link']) ? $_POST['link'] : null;

// xử lý xóa


// Thực hiện xử lý và tạo nội dung
$result = "Processing data with ID: $id, link: $link";

// lay tất cả link theo id
$link_list = $db->query("select * from file where file_id = '{$id}'");
$links =  $link_list[0]['file_link']; // json string
//comvert to array
$link_arr = (json_decode($links));

$viTri = array_search($link, $link_arr);

// Nếu phần tử tồn tại trong mảng, thì xóa nó
if ($viTri !== false) {
    unset($link_arr[$viTri]);
}

// update database
$link_update = json_encode($link_arr);
$update = $db->query("update file set file_link = '{$link_update}' where file_id = '{$id}'");

// in ra ajax content
$output = "";

if (count($link_arr)) {
    foreach ($link_arr as $data) {
        $output .= '<div class="col-md-2">';
            $output.= '<div class="card">';
                $output.= '<img src="/res/img/hkt-upload.png" class="card-img-top" alt="...">';
                $output.= '<div class="p-2">';
                    $output.= '<p class="card-text">'. $data .'</p>';
                    $output.= '<a target="_blank" href="https://view.officeapps.live.com/op/view.aspx?src=https://'.$_SERVER['SERVER_NAME'].'/uploads/'. $data .'" class="btn btn-primary btn-sm mr-1">View</a>';
                    $output.= '<button class="btn btn-success btn-sm hkt-delete" data-id="'. $id .'" data-link="'. $data .'">Delete</button>';
                $output.='</div>';
            $output.='</div>';
        $output.='</div>';
    }
}

echo $output;
 ?>