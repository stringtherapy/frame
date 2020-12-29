<?php 

function prepared_mysqli_query($conn,$query) {

//initializing required variables
$first_half = '';
$placeholders = '';
$data_types = '';
$values = '';

//getting the first half of query 
$trim_end   = strrpos($query,'(');
$first_half = substr($query, 0, $trim_end); 

//getting the values to be inserted
$open = strrpos($query,'(');
$close = strrpos($query,')');	

$values = substr($query,($open+1),($close+1));
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


$stmt   =  mysqli_stmt_init($conn);   
$query  =  $first_half.$placeholders;  

if(mysqli_stmt_prepare($stmt,$query)){
	mysqli_stmt_bind_param($stmt, $data_types, ...$values_array);		
	mysqli_stmt_execute($stmt);
} else echo "failure";

}

?>
