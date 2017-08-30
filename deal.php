<?php 
require_once 'db.php';
$id = $_POST['id'];
$name = $_POST['name'];
$event = $_POST['event'];
$time = isset($_POST['time']) ? $_POST['time'] : [];

$mysqli->set_charset("utf8");
$mysqli->query("SET NAMES 'utf8'");
$mysqli->query("SET CHARACTER SET UTF8");

if ($mysqli->connect_errno) {
    echo "db에 연결하지 못했습니다. (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	die;
}

$id = $mysqli->real_escape_string($id);
$name = $mysqli->real_escape_string($name);
$event = $mysqli->real_escape_string($event);

if ($res = $mysqli->query("SELECT ID FROM students WHERE stu_id = '" . $id . "'")) {
	if ($res->num_rows) {
		$row = $res->fetch_assoc();
		$stuid = $row['ID'];
		if($res = $mysqli->query("SELECT NAME FROM students where stu_id = '" . $id . "'")) {
			if ($res->num_rows){
				$row = $res->fetch_assoc();
				$dbname = $row['NAME'];
				if (strcmp($dbname, $name)){
					echo "db에 등록된 이름[$dbname]과 다릅니다";
					die;
				}
			}
		}
	} else {
		if (!$mysqli->query("INSERT INTO students(stu_id, name) VALUES ('" . $id . "', '" . $name . "')")) {
			echo "쿼리 입력에 실패했습니다.";
			die;
		}
		$stuid = $mysqli->insert_id;
	}
} else {
	echo "db 조회에 실패했습니다.";
	die;
}

if (!$mysqli->query("DELETE FROM time WHERE student_id = $stuid")) {
	echo "수업 시간표 초기화에 실패했습니다." . $mysqli->error;
	die;
}

if (!($stmt = $mysqli->prepare("INSERT INTO time(student_id, day, item) VALUES ($stuid, ?, ?)"))) {
	echo "쿼리 입력에 실패했습니다2." . $mysqli->error;
	die;
}

foreach($time as $t) {
	list($item, $day) = split(',', $t);
	$stmt->bind_param('ss', $day, $item);
	if (!$stmt->execute()) {
		echo "시간표 저장에 실패했습니다." . $mysqli->error;
		die;
	}
}
$stmt->close();
echo "완료.";
$mysqli->close();
