<?php
	$conn = new mysqli('localhost', 'erpintr1', '#k1u3T3f5', 'erpintr1_erp');

	$Division = $_POST['Division'];
	@$cd = $_POST['cd'];
	@$id_cd = $_POST['id_cd'];
 
	if($Division == 0){
        exit;
    }
	else{
		$QDivision = "WHERE FK_Division = ".$Division;
	}
    
	if(empty($cd)){
        $cd = 'SELECCIONA AREA';
    }
	else{
		@$cd = $_POST['cd'];	
		@$id_cd = $_POST['id_cd'];
	}

	$sq = mysqli_query($conn,"SELECT * FROM areas
    $QDivision
	ORDER BY area ASC"); 

    echo '
		<select name="Area" id="Area" class="form-control bg-light border-0 small" onchange="LoadCopesKPI();">
    		<option value="0">'.$cd.'</option>';
		    while($row = mysqli_fetch_array($sq)){
		        echo "<option value='".$row['idAreas']."'>".utf8_encode($row['area'])."</option>";
		    }

    echo "</select>";

?>	