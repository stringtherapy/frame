<?php 

//php-to-txt
$test = file_get_contents("sql_code.php");
file_put_contents("code.txt", $test);

//getting input: mysql code
$code =  file_get_contents('code.txt');
$code_array = explode(";",$code);

//variables to be extracted
$connection_variable = '';
$first_half = '';
$placeholders = '';
$data_types = '';
$values = '';

//running through each line of code
foreach ($code_array as $string){ 

	//get connection variable
	if (strpos($string, 'mysqli_query(') !== false) {

		$line_start      = strpos($string,'mysqli_query');
		$line_end        = strpos($string,')',$line_start);
		$line_length     = $line_end - $line_start;
		$line_of_interest = substr($string,$line_start,($line_length+1));

	    
		$start = strpos($line_of_interest, 'mysqli_query('[-1]);
		$end = strpos($line_of_interest,',');
		$length = $end - $start;
		$connection_variable = substr($line_of_interest,($start+1),($length-1)); 
	}

	//get prepared statement

	//get table name
	if (strpos($string, 'INSERT INTO') !== false) {

		$table_start1   = strpos($string,'INSERT INTO');
		$table_start 	= strpos($string,'INSERT INTO'[-1]);
		$table_end 	= strpos($string,'(');
		$table_length 	= $table_end - $table_start;
		$table_name 	= substr($string,($table_start+1),($table_length-1));


		$line_start = strpos($string,'INSERT INTO $table_name (');
		$line_end = strrpos($string,')',$line_start);
		$line_length = $line_end - $line_start;
		$line_of_interest2 = substr($string,$line_start,($line_length+1));

		
		$open = strrpos($line_of_interest2,'(');
		$close = strrpos($line_of_interest2,')');

		//trimming first half of query
		$len = $open - $table_start1;
		$first_half = substr($line_of_interest2,$table_start1,($len-1));

		//extracting values to be inserted
		$values = substr($line_of_interest2,($open+1),($close+1));
		$values_stripped = substr($values,0,-1);
		$values_array = explode(",",$values_stripped);
		$values_count = count($values_array);

		//generating placeholders
		$ph = "(";
		for ($i = 1; $i < $values_count; $i++) {
	  		$ph.= "?,";
		}
		$ph.= "?)";
		$placeholders .= $ph;

		//getting value types
		$types ='';
		for ($i = 0; $i < $values_count; $i++){
	 	$str = gettype($values_array[0]);
	 	$types .= $str[0];
		}
		$data_types .= $types;

	}
}

//getting output: safer code
$ok= "<?php 
	  \$stmt   =  mysqli_stmt_init($connection_variable);   
	  \$query  =  $first_half $placeholders;  

	  if(mysqli_stmt_prepare(\$stmt,\$query)){ 
	  	 mysqli_stmt_bind_param(\$stmt,\"$data_types\",$values;		
	     mysqli_stmt_execute(\$stmt); 
	  } 
?>"; 


file_put_contents("sql_safer_code.php", $ok);

?>
