<?php

// Robert Sims - Web Dev Candidate - Example 3.
//
// PHP & MySQL (Loops, Classes, Form handling, Database querying)
//
// - Create a database with 2 tables, 1 for user data and the other for user email addresses. 
//
// - Create a way to relate the email table to the user data table. 
//
// - Create a standard html form that sends data to the user data table and email table. 
//
// - Create a method to validate the email address field before processing submission. 
//
// - Create a php class that handles the various database connectivity and queries. 
//
// - After successful submission of form, send user to a page that displays all current items in 
//		database and the associated email addresses. 

// DEV NOTES:  Would be best to tie the Users to a unique identifier like a username. 
// For this example, a first name/last name combo is used.


// *****************************************************************
// *** This page is self referencing.  
// *** Data is submitted and processed on the same page.
// *****************************************************************

$isValid = TRUE;
$errmsg = '';
$tableOut = '';

// Is this page running after a form submit?
if (strtoupper($_SERVER['REQUEST_METHOD']) == "POST") {
	
	require_once('classDbHelper.php');
	// $dbHelper = new classDbHelper('<YOUR_DB_SERVER>', '<YOUR_DB_USER>', '<YOUR_DB_PASSWORD>', 'rsims_example_3');
	$dbHelper = new classDbHelper('127.0.0.1', 'root', 'Snorlax36', 'rsims_example_3');
	

	// *****************************************************************
	// *** Validation **************************************************
	// *****************************************************************

	// Serverside email validation
	if ( !validateEmail(trim($_POST['email'])) ) {
		$isValid = FALSE;
		$errmsg .= "<div class=\"err\">Your email is invalid according to PHP's validator.</div>";
	} else {
		$email = $dbHelper->cleanString( trim($_POST['email']) );
	}

	// Does email already exist?
	$qry = "select email from email where email = '" . $email . "';";
	if ( $dbHelper->getCount($qry) > 0 ) {
		$isValid = FALSE;
		$errmsg .= "<div class=\"err\">Duplicate email found.  Please use a unique email address.</div>";
	} else {
		$first_name	= $dbHelper->cleanString( trim($_POST['first_name']) );
		$last_name	= $dbHelper->cleanString( trim($_POST['last_name']) );
	}

	// *****************************************************************
	// *** Processing **************************************************
	// *****************************************************************
	if ( $isValid == TRUE ) {
		
		$qry = "select users_id from users where first_name = '" . $first_name . "' and last_name = '" . $last_name . "';";
		if ( $dbHelper->getCount($qry) > 0 ) {
			$result = $dbHelper->selectData($qry);
			$row = $result->fetch_assoc();
			$user_id = $row['users_id'];

			$qry = "insert into email (users_id, email) values ('" . $user_id . "', '" . $email . "');";
			$dbHelper->insertData($qry);

		} else {
			$qry = "insert into users (first_name, last_name) values ('" . $first_name . "', '" . $last_name . "');";
			$last_id =  $dbHelper->insertData($qry);

			$qry = "insert into email (users_id, email) values ('" . $last_id . "', '" . $email . "');";
			$dbHelper->insertData($qry);

		}

	}

	// *****************************************************************
	// *** Build Previous Entries **************************************
	// *****************************************************************
	$qry = 'select u.users_id, e.email_id, u.first_name, u.last_name, e.email from users u, email e where u.users_id = e.users_id order by users_id, email;';
	$result = $dbHelper->selectData($qry);

	$tableOut = '<table><tr><th>User Id</th><th>First name</th><th>Last name</th><th>Email ID</th><th>Email</th></tr>';
	while($record = $result->fetch_assoc()) {
		$tableOut .= '<tr>';
		$tableOut .= '<td>' . $record["users_id"] . '</td>';
		$tableOut .= '<td>' . $record["first_name"] . '</td>';
		$tableOut .= '<td>' . $record["last_name"] . '</td>';
		$tableOut .= '<td>' . $record["email_id"] . '</td>';
		$tableOut .= '<td>' . $record["email"] . '</td>';
		$tableOut .= '</tr>';

	}
	$tableOut .= '<table>';

}


$strout = htmlTop() . $errmsg . htmlForm() . $tableOut . htmlBottom();

echo $strout;

function validateEmail( $_email ) {
	$_rtnValue = FALSE;

	if (filter_var($_email, FILTER_VALIDATE_EMAIL)) {
		$_rtnValue = TRUE;
	}

	return $_rtnValue;
}

function htmlTop() {
return <<< EOS
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
	<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
	<meta charset="utf-8">
	<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Expires" content="-1" />
	<meta http-equiv="X-UA-Compatible" content="IE=9">
	<title>R.Sims Example 3</title>

	<!--[if lt IE 9]>
	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

	
	<link rel="stylesheet" type="text/css" href="rsims_example_3.css" />
	
</head>
<body>
<div class="content">
<h3>Robert Sims - Web Dev Candidate - Example 3</h3>
<p><span>PHP & MySQL</span> (Loops, Classes, Form handling, Database querying)</p>
<p>Create a database with 2 tables, 1 for user data and the other for user email addresses. Create a way to relate the email table to the user data table. Create a standard html form that sends data to the user data table and email table. Create a method to validate the email address field before processing submission. Create a php class that handles the various database connectivity and queries. After successful submission of form, send user to a page that displays all current items in database and the associated email addresses. </p>


EOS;
}

function htmlForm() {
return <<< EOS
<div>
	<form id="user_form" name="user_form" method="post" action="rsims_example_3.php">
		<label for="first_name">
			*First name: 
			<input type="text" id="first_name" name="first_name" required />
		</label>

		<label for="last_name">
			*Last name: 
			<input type="text" id="last_name" name="last_name" required />
		</label>


		<label for="email">
			*Email: 
			<input type="email" id="email" name="email" pattern="[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?" required />
		</label>

		<button>Submit</button>
	</form>

</div>

EOS;
}

function htmlBottom() {
return <<< EOS


<div class="devnotes">
<p><span>DEV NOTES:</span></p>

<p>Database setup file is in the runtime directory: table_setup.sql</p>

<p>This page is self referencing. Data is submitted and processed on the same page.</p>

<p>Would be best to tie the Users to a unique identifier like a username. For this example, a first name/last name combo is used.</p>


</div>

</div>
</body>
</html>

EOS;
}

?>