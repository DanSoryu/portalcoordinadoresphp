<?php

	$modalcargar = new Ops();

	function ListDivision() {
		global $modalcargar;
		$filas = $modalcargar->ListDivisiones();

		if (!empty($filas) && is_array($filas)) {
			foreach ($filas as $row) {
				$id = htmlspecialchars($row['idDivision']);
				$division = strtoupper(htmlspecialchars($row['Division']));
				echo "<option value='$id'>$division</option>";
			}
		}
	}

	function ListSeries() {
		global $modalcargar;
		$filas = $modalcargar->GetNumSerieSalidaDetTecnico();

		if (!empty($filas) && is_array($filas)) {
			foreach ($filas as $row) {
				$serie = strtoupper(htmlspecialchars($row['Num_Serie_Salida_Det']));
				echo "<option value='$serie'>$serie</option>";
			}
		}
	}

	function ListOrdenes() {
		global $modalcargar;
		$filas = $modalcargar->ListOrdenesFotografias();

		if (!empty($filas) && is_array($filas)) {
			foreach ($filas as $row) {
				$orden = strtoupper(htmlspecialchars($row['Folio_Pisa']));
				echo "<option value='$orden'>$orden</option>";
			}
		}
	}

	function ListCopes() {
		global $modalcargar;
		$filas = $modalcargar->ListCopes();

		if (!empty($filas) && is_array($filas)) {
			foreach ($filas as $row) {
				$id = htmlspecialchars($row['id']);
				$cope = strtoupper(htmlspecialchars($row['COPE']));
				echo "<option value='$id'>$cope</option>";
			}
		}
	}
?>