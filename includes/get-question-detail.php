<?php

//
//	Question2Answer API
//	Author : Arun Anson
//	Copyright (c) 2017 Hello Infinity Business Solutions Pvt. Ltd.
//	17th June 2017
// 	GET QUESTIONS API
// 	Gets all of the questions and its details in order they are posted.

// 	Sample Input
// { "requestHeader": { "serviceId":"111", "interactionCode":"GETQUESTIONDETAIL"}, "requestBody" : { "questionid" : "1", "user_id" : "user" }}

// 	Sample Output
//	{"responseHeader":{"serviceId":"111","status":"200"},"responseBody":{"question":[{"title":"update post title","acount":"7","userid":"1","views":"1","tags":"tag1 update, tag2 update1","content":"update content","selchildid":"16","netvotes":"-1","updated":null,"created":"1497455725","answered":"yes"}],"answers":[{"title":null,"postid":"20","userid":"1","acount":"0","views":"0","tags":null,"content":"?one ?one ?one ?one ?one ?one ?one ?one","netvotes":"0","updated":null,"created":"1498057104"},{"title":null,"postid":"19","userid":"17","acount":"0","views":"0","tags":null,"content":"here goes an another another answer","netvotes":"0","updated":null,"created":"1498056816"},{"title":null,"postid":"18","userid":"17","acount":"0","views":"0","tags":null,"content":"here goes an another answer","netvotes":"0","updated":null,"created":"1498056770"},{"title":null,"postid":"17","userid":"17","acount":"0","views":"0","tags":null,"content":"here goes an answer","netvotes":"0","updated":null,"created":"1498056746"},{"title":null,"postid":"16","userid":"17","acount":"0","views":"0","tags":null,"content":"answer test content","netvotes":"0","updated":"1500285887","created":"1498056517"},{"title":null,"postid":"9","userid":"1","acount":"0","views":"0","tags":null,"content":"Answer2 on the test question","netvotes":"0","updated":null,"created":"1497697943"},{"title":null,"postid":"2","userid":null,"acount":"0","views":"0","tags":null,"content":"answer test test","netvotes":"-1","updated":null,"created":"1497455825"}]}}

function get_question_detail($json_request){

	include '../connection.php';

	$serviceId	= $json_request['requestHeader']['serviceId'];
	$questionid = $json_request['requestBody']['questionid'];
	$current_user_id = $json_request['requestBody']['user_id'];

	//Set answered flag to no
	$answered = 'no';

	$sql_get_question = "SELECT title, acount, userid, views, tags, content, selchildid, netvotes, UNIX_TIMESTAMP(updated) as updated, UNIX_TIMESTAMP(created) as created FROM ".TABLEPREFIX."posts WHERE type='Q' and postid='".$questionid."' ORDER BY created DESC LIMIT 20;";
	$result_get_question = $conn->query($sql_get_question);

	while($row_get_question = $result_get_question->fetch_assoc()) {
		$data_get_question[] = $row_get_question;
	}


	$sql_get_question_detail = "SELECT title, postid, userid, acount, views, tags, content, netvotes, UNIX_TIMESTAMP(updated) as updated, UNIX_TIMESTAMP(created) as created FROM ".TABLEPREFIX."posts WHERE type='A' and parentid='".$questionid."' ORDER BY netvotes DESC;";

	$result_get_question_detail = $conn->query($sql_get_question_detail);

	while($row_get_question_detail = $result_get_question_detail->fetch_assoc()) {

		$data_get_question_detail[] = $row_get_question_detail;

		//If the user has answered the question set answered to yes.
		if ($row_get_question_detail['userid'] == $current_user_id && $row_get_question_detail['userid'] != '') {

			$answered = 'yes';
		}
	}

	$num_rows = mysqli_num_rows($result_get_question_detail);

	if ($num_rows > 0) {

		//success
		$res['responseHeader']['serviceId'] = $serviceId;
		$res['responseHeader']['status'] = "200";
		$res['responseBody']['question'] = $data_get_question;
		$res['responseBody']['question'][0]['answered'] = $answered;
		$res['responseBody']['answers'] = $data_get_question_detail;
	}else{

		//error
		$res['responseHeader']['serviceId'] = $serviceId;
		$res['responseHeader']['status'] = "401";
		$res['responseBody']['question'] = $data_get_question;
		$res['responseHeader']['message'] = "There are no answers to the specified question";
	}


	$json_response = json_encode($res, JSON_UNESCAPED_SLASHES);
	echo $json_response;

}
?>