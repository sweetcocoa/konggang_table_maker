<?php 
require_once 'db.php';
$id = $_POST['id'];
$name = $_POST['name'];
$event = $_POST['event'];

$mysqli->set_charset("utf8");
$mysqli->query("SET NAMES 'utf8'");
$mysqli->query("SET CHARACTER SET UTF8");

function finish($ret) {
	echo "{\"error\": \"$ret\"}";
	die;
}
if ($mysqli->connect_errno) {
     $ret = "데이터베이스에 연결하지 못했습니다. (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	 finish($ret);
}

$id = $mysqli->real_escape_string($id);
$name = $mysqli->real_escape_string($name);

if ($res = $mysqli->query("SELECT ID FROM students WHERE stu_id = '$id' AND name = '$name'")) {
	if ($res->num_rows) {
		$row = $res->fetch_assoc();
		$stuid = $row['ID'];
	} else {
		finish("학번, 이름을 확인해주세요.");
	}
} else {
	finish("쿼리에 실패했습니다.");
}

if ($res = $mysqli->query("SELECT * FROM time WHERE student_id = $stuid")) {
	echo "[";
	$i = false;
	while ($row = $res->fetch_assoc()) {
		if ($i) echo ",";
		$i = true;
        printf('{"day": "%s", "item": "%s"}', $row['day'], $row['item']);
    }
	echo "]";
} else {
	finish("쿼리에 실패했습니다.");
}


$mysqli->close();
