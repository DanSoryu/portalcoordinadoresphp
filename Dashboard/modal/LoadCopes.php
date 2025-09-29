<?php
	$conn = new mysqli('localhost', 'erpintr1', '#k1u3T3f5', 'erpintr1_erp');

	$Area = $_POST['Area'];
	@$cd = $_POST['cd'];
	@$id_cd = $_POST['id_cd'];
 
	if($Area == 0){
        exit;
    }
	else{
		$QArea = "WHERE FK_Area = ".$Area;
	}
    
	if(empty($cd)){
        $cd = 'SELECCIONA COPE';
    }
	else{
		@$cd = $_POST['cd'];
		@$id_cd = $_POST['id_cd'];
	}

	$sq = mysqli_query($conn,"SELECT * FROM copes
    $QArea
	ORDER BY CT ASC"); 

    echo '
		<select name="Cope" id="Cope" class="form-control bg-light border-0 small" onchange="LoadKPIS();">
    		<option value="0">'.$cd.'</option>';
		    while($row = mysqli_fetch_array($sq)){
		        echo "<option value='".$row['id']."'>".utf8_encode($row['COPE'])."</option>";
		    }

    echo "</select>";
?>	