<?php
include("./databaseClassMySQLi.php");

//for paging query
$LIMITION = 10;

function LogIN($email, $password){

	if($email == "" || $password == ""){
		return -1;
	}
	$db = new database();
	$db->connect();
	$pass = htmlspecialchars($password);
	$email = htmlspecialchars($email);

	$query = "SELECT * 
			FROM `User` 
			where `email` = ?
			and `passwd` = ?";


	if ($stmt = $db->prepare($query)) {
		$stmt->bind_param("ss", $email, sha1($password));
		//echo $query;
		if($stmt->execute()){

			//$stmt->store_result();
			$results = $stmt->get_result();
			$reuslt = array();
			if(count($results) == 1){

				foreach ($results as $key => $value) {
					 $userinfo['login'] = 1;
					 foreach ($value as $key_ => $value_) {
					 	// $result[$key_] = $value_[$key_];
					 	$userinfo[$key_] = $value_;
					 	//echo $key_;
					 }
				}//

				$db->disconnect();
				return $userinfo;
			}
		}
	}
	$userinfo['login'] = -1;
	
	$db->disconnect();
	return $userinfo;
}
//htmlentities//

//return 1 if not exsit and validate
//return -1 if exist or invalidate
function checkEmailExist($email){
	$db = new database();
	$db->connect();
	
	if(emailValidate($email) != false)
	{
		$mail = htmlspecialchars($email);
		$query = "SELECT
					* FROM `User`
					 where `email` = ? ";
		//echo $query;
		if ($stmt = $db->prepare($query)) {
			$stmt->bind_param("s", $mail);
			if($stmt->execute()){
	
				$stmt->store_result();
				$affectrows = $stmt->affected_rows;
				
				if($affectrows == 0){
					$db->disconnect();
					return 1;
				}
			}
		}
	}
	$db->disconnect();
	return -1;

}
function regist($email, $username, $password){
	
	$db = new database();
	$db->connect();
	
	if(checkEmailExist($email) == -1){
		return -1;
	}
/*
	$query = "Insert into `User`(`email`, `Name`, `passwd`, `group`, `credits`)
				Values (\"$email\", \"$username\",\"". SHA1($password) ."\",1,0)";
	*/
	$mail = htmlspecialchars($email);
	$name = htmlspecialchars($username);
	$pass = htmlspecialchars($password);
	$query = "Insert into `User`(`email`, `Name`, `passwd`, `group`, `credits`)
	Values ( ? , ? , ? , 1 , 0 )";
	
	if ($stmt = $db->prepare($query)) {
		$stmt->bind_param("sss", $mail, $name, sha1($pass));
		//here
		if($stmt->execute()){
	
			$stmt->store_result();
			$affectrows = $stmt->affected_rows;
				
			if($affectrows == 1){
				$id = $stmt -> insert_id;
				$db->disconnect();
				return $id;
			}
		}
	}
	
	$db->disconnect();
	return -1;
}

//user could upgrade when they have enough credits
function Upgrade($userID){

	$db = new database();
	$db->connect();
	$query = "Update `group` = `group` +1
				from `User` 
				where `UID` = ?";
	

	if ($stmt = $db->prepare($query)) {
		$stmt->bind_param("i", $userID);
		if($stmt->execute()){
	
			$stmt->store_result();
			$affectrows = $stmt->affected_rows;
				
			if($affectrows != 0){
				$db->disconnect();
				return 1;
			}
		}
	}
	return  -1;
	//$db->connect();
	
}

//validate email;
function emailValidate($email){
	$res = filter_var($email, FILTER_VALIDATE_EMAIL);
	return $res;
}

function postquestion($userID, $title, $content){
	if(IDValidate($userID) == -1){
		return -1;
	}
	$ProcceedContent = htmlentities(nl2br($content));
	$ProcceedTitle = htmlspecialchars($title);
	$db = new database();
	$db->connect();
	date_default_timezone_set('UTC');
	
	$query = "INSERT INTO `Questions`(`UID`, `Title`, `Content`, `Time`) 
			VALUES ( ? , ? , ? , ? )";
	
	if ($stmt = $db->prepare($query)) {
		$stmt->bind_param("isss", $userID, $ProcceedTitle, $ProcceedContent, date("Y-m-d H:i:s"));
		if($stmt->execute()){
	
			$affectrows = $stmt->affected_rows;
	
			if($affectrows != 0){
				return $stmt->insert_id;
			}
		}
	}
	return -1;
}

function IDValidate($userID){
	$reg = "/[0-9]+/";
	if(!preg_match($reg, $userID)){
		echo "Not match<br/>";
		return -1;
	}
	$db = new database();
	$db->connect();
	$query = "Select * from `User` where `UID` = ? ";

	if ($stmt = $db->prepare($query)) {
		$stmt->bind_param("i", $userID);
		if($stmt->execute()){
			$stmt->store_result();
			$affectrows = $stmt->affected_rows;
			
			if($affectrows != 0){
				return 1;
			}
		}
	}
	return -1;
}
//TODO:
function GetProfile($userID){
	
	if(IDValidate($userID) == -1){
		echo "User Invalidate<br/>";
		return -1;
		}
		$db = new database();
		$db->connect();
		$query = "Select `Location`, `Name`,`Habit`, `BOD`, `Email`, `group`, `credits`, `PID`
				 from `Profiles`, `User` 
				where `User`.`UID` = ? 
				and `Profiles`.`UID` = `User`.`UID`";

		if ($stmt = $db->prepare($query)) {
			$stmt->bind_param("i", $userID);

			if($stmt->execute()){
			
				$result = $stmt->get_result();
			
				$results = array();

				foreach ($result as $keys => $values) {
					$element;
					foreach ($values as $key => $value) {
						$element[$key] = $value;
					}
					$element['getProfile'] = 1;
					array_push($results, $element);
				}
				//print_r($reuslts);
				if (count($results) == 0) {
					
					$resultsss['getProfile'] = -1;
					return $resultsss;
				}
				return $results[0];
			}
		}
		$resultss['getProfile'] = -1;
		return $resultss;
}
function GetQuestion_Answer($Qid){
	$db = new database();
	$db->connect();

	$query = "SELECT `Answers`.`Content`, `Time`, `Name` 
			FROM `Answers`, `User` 
			where `User`.`UID` = `Answers`.`UID`
			and `QID` = ?";

	if ($stmt = $db->prepare($query)) {
		$stmt->bind_param("i", $Qid);
		if($stmt->execute()){
		$results = array();
		$result = $stmt->get_result();
		return $result;
		}
	}
	return -1;
}
function GetQuestion_ByID($QID){

	$db = new database();
	$db->connect();
	$query = "SELECT `Questions`.`Content`, `Title`, `Name`, `Time`
			FROM `Questions`, `User` 
			where `User`.`UID` = `Questions`.`UID`
			and `QID` = ?";

	if ($stmt = $db->prepare($query)) {
		$stmt->bind_param("i", $QID);

		if($stmt->execute()){

			$result = $stmt->get_result();

			$results = array();
			foreach ($result as $keys => $values) {
				$element;
				foreach ($values as $key => $value) {
					$element[$key] = $value;
				}
				array_push($results, $element);
			}
			return $results;
		}
	}
	return -1;
}

/*
 * return 1 when success, otherwise failed
 * */
function UpdateProfile($newly){
	$db = new database();
	$db->connect();

	// if ((CheckProfileExist($newly['UID']) == false)|| (CheckProfileExist($newly['UID']) == -1)) {
	// 	echo "here";
	// 	return InsertProfile($newly);

	// }

	$query = "UPDATE `Profiles` 
			SET
			`Habit`= ? ,
			`Location`= ? ,
			`BOD`= ?  
			WHERE `PID` =?" ;
			
	if ($stmt = $db->prepare($query)) {
		$stmt->bind_param("sssi", htmlspecialchars($newly['Habit']),
			htmlspecialchars($newly['Location']),htmlspecialchars($newly['BOD']),$newly['PID']);
	
		if($stmt->execute()){
			$stmt->store_result();
			$result = $stmt->affected_rows;
	
			return $result;
		}
	}
	
	return -1;
}

function CheckProfileExist($UID){

	$db = new database();
	$db->connect();
	
	$query = "SELECT * from `Profiles` where `UID` = ?";
	
	if ($stmt = $db->prepare($query)) {
		$stmt->bind_param("i",$UID);
	
		if($stmt->execute()){
	
			//$stmt->store_result();
			$result = $stmt->affected_rows;
			echo "ha<br/>";
			echo $result;
			return $results;
		}
	}
	return false;
}

function InsertProfile($newly){

	$db = new database();
	$db->connect();
	print_r($newly);
	$query = "INSERT INTO `Profiles`(`UID`, `Habit`, `Location`, `BOD`)
			VALUES (? , ? , ? , ? )";
	foreach ($newly as $key => $value) {
		# code...
		//echo "$key => $value <br/>";
	}
	if ($stmt = $db->prepare($query)) {
		$stmt->bind_param("isss", $newly['UID'],htmlspecialchars($newly['Habit']),
			htmlspecialchars($newly['Location']),htmlspecialchars($newly['BOD']));
	
		if($stmt->execute()){
	
			$result = $stmt->insert_id;
			return $results;
		}
	}
}

function GetArticlesByUID($UID){
	
	$db = new database();
	$db->connect();
	$query = "SELECT `Title`, `Content`, `Time`, `Up_Vote`, `Down_Vote`,`Name` ,`ArtID`
			FROM `Article`, `User` WHERE `User`.`UID` = `Article`.`UID` and `User`.`UID` = ? ";

	if ($stmt = $db->prepare($query)) {
		$stmt->bind_param("i", $UID);
	
		if($stmt->execute()){
	
			$result = $stmt->get_result();
	
			$results = array();
			foreach ($result as $keys => $values) {
				$element;
				foreach ($values as $key => $value) {
					$element[$key] = $value;
				}
				array_push($results, $element);
			}
			return $results;
		}
	}
	return -1;
}

function GetArticle($Artid){
	

	$db = new database();
	$db->connect();
	$query = "SELECT `Title`, `Content`, `Time`, `Up_Vote`, `Down_Vote`,`Name`,`User`.`UID`
			FROM `Article`, `User` WHERE `User`.`UID` = `Article`.`UID` and `ArtID` = ? ";
	
	
	if ($stmt = $db->prepare($query)) {
		$stmt->bind_param("i", $Artid);
	
		if($stmt->execute()){
	
			$result = $stmt->get_result();
	
			$results = array();
			foreach ($result as $keys => $values) {
				$element;
				foreach ($values as $key => $value) {
					$element[$key] = $value;
				}
				array_push($results, $element);
			}
			return $results;
		}
	}
	return -1;
	
	
}

function GetUpCount($AID){

	$db = new database();
	$db->connect();
	$query = "SELECT count(*) as Count from `UP_Table` 
			 where `AID` = ?";
	
	if ($stmt = $db->prepare($query)) {
		$stmt->bind_param("i", $AID);
	
		if($stmt->execute()){
			$result = $stmt->get_result();
			//return $result;

			$results = array();
			foreach ($result as $keys => $values) {
				$element;
				foreach ($values as $key => $value) {
					$element[$key] = $value;
				}
				array_push($results, $element);
			}
			return $results;
		}
	}
	return -1;
}


function VoteUp($AID, $UID){

	WithDrawVoteDown($AID,$UID);
	$db = new database();
	$db->connect();
	$query = "INSERT INTO `UP_Table`(`AID`, `UID`) 
			VALUES ( ? , ? )";
	
	if ($stmt = $db->prepare($query)) {
		$stmt->bind_param("ii", $AID, $UID);
	
		if($stmt->execute()){
			$result = $stmt->store_result();
			return $result;
		}
	}
	return -1;
}



function WithdrawVoteUp($AID, $UID){
	
	$db = new database();
	$db->connect();
	$query = "DELETE FROM `UP_Table` 
				WHERE `AID`= ? and `UID`= ?";
	
	if ($stmt = $db->prepare($query)) {
		$stmt->bind_param("ii", $AID, $UID);
	
		if($stmt->execute()){

			$result = $stmt->store_result();
			return $result;
		}
	}
	return -1;
}

function VoteDown($AID, $UID){

	WithDrawVoteUp($AID,$UID);
	$db = new database();
	$db->connect();
	$query = "INSERT INTO `DOWN_Table`(`AID`, `UID`) 
			VALUES ( ? , ? )";
	
	if ($stmt = $db->prepare($query)) {
		$stmt->bind_param("ii", $AID, $UID);
	
		if($stmt->execute()){

			$result = $stmt->store_result();
			return $result;
		}
	}
	return -1;
}

function GetDownCount($AID){

	$db = new database();
	$db->connect();
	$query = "SELECT count(*) as Count from `DOWN_Table` 
			 where `AID` = ?";
	
	if ($stmt = $db->prepare($query)) {
		$stmt->bind_param("i", $AID);
	
		if($stmt->execute()){
			$result = $stmt->get_result();
			//return $result;

			$results = array();
			foreach ($result as $keys => $values) {
				$element;
				foreach ($values as $key => $value) {
					$element[$key] = $value;
				}
				array_push($results, $element);
			}
			return $results;
		}
	}
	return -1;
}

function WithdrawVoteDown($AID, $UID){
	
	$db = new database();
	$db->connect();
	$query = "DELETE FROM `DOWN_Table` 
				WHERE `AID`= ? and `UID`= ?";
	
	if ($stmt = $db->prepare($query)) {
		$stmt->bind_param("ii", $AID, $UID);
	
		if($stmt->execute()){

			$result = $stmt->store_result();
			return $result;
		}
	}
	return -1;
}

//get questions by user ID
function GetQuestionsByUID($UID, $LIMITION){
	//$LIMITION = 10;
	$db = new database();
	$db->connect();
	$query = "SELECT `Questions`.`Content`, `Title`, `Name`, `Time`, `QID`
			FROM `Questions`, `User` 
			where `User`.`UID` = `Questions`.`UID`
			and `User`.`UID` = ?
			limit $LIMITION";
	
	if ($stmt = $db->prepare($query)) {
		$stmt->bind_param("i", $UID);
	
		if($stmt->execute()){
	
			$result = $stmt->get_result();
	
			$results = array();
			foreach ($result as $keys => $values) {
				$element;
				foreach ($values as $key => $value) {
					$element[$key] = $value;
				}
				array_push($results, $element);
			}
			return $results;
		}
	}
	return -1;
}

function GetQuestion(){
	$LIMITION = 10;
	$db = new database();
	$db->connect();
	//static SQL, no need to bind
	$query = "select * from Questions
			order by TIME
			limit $LIMITION
			";
	if(!$res = $db->send_sql($query)){
		$db->disconnect();
		echo "Get Questions failed!<br/>\n";
		return -1;
	}

	$num = mysqli_num_rows($res);
	
	for($cur; $cur < $num; $cur++){
		$temres = $db->next_row();
		foreach ($temres as $key => $var){
			$res[$cur][$key] = $var;
		}
	}
	
	return $res;
}
?>