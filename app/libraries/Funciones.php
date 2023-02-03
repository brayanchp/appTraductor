<?php
namespace App\libraries;

class Funciones{

	public function generarPaginacion($lista, $pagina, $filas){
		$cantidadTotal = count($lista); 
		if ($filas > $cantidadTotal) { 
			$filas = $cantidadTotal;
		} 
		$cantidad = $cantidadTotal * 1.0; 
		$division = $cantidad / $filas; 
		$div = ceil($division); 
		if ($pagina > $div) {
			$pagina = (int) $div;
		}

		$inicio = ($pagina - 1) * $filas; 
		$fin    = ($pagina * $filas); 
		// dd($fin, $pagina, $filas);
		// if ($fin > $cantidadTotal) {
		// 	$fin = $cantidadTotal;
		// } else {
		$fin = $cantidadTotal;
		// }

		$cadenaPagina  = [];
		$puntosDelante = "";
		$puntosDetras  = "";
		// $cadenaPagina .= "<ul class=\"pagination pagination-sm\">";
		// $cadenaPagina .= "<li class=\"active\"><a href=\"#\">TOTAL DE REGISTROS " . $cantidadTotal . "</a></li>";

		for ($i=1; $i <= $div ; $i++) { 
			if ($i == 1) {
				// if ($i == $pagina) {
					$cadenaPagina[] = array('opc' => $i); //"<li class=\"active\"><a>" . $i . "</a></li>";
				// } else {
					// $cadenaPagina[] = array('opc' => $i); //"<li><a onclick=\"buscarCompaginado(" . $i . ", '', '".$entidad."')\">" . $i . "</a></li>";
				// }
			}
			if ($i == $div && $i != 1) {
				// if ($i == $pagina) {
					$cadenaPagina[] =  array('opc' => $i); //"<li class=\"active\"><a>" . $i . "</a></li>";
				// } else {
				// 	$cadenaPagina .= "<li><a onclick=\"buscarCompaginado(" . $i . ",'', '".$entidad."')\">" . $i . "</a></li>";
				// }
			}
			if ($i != 1 && $i != $div) {
				if ($i == $pagina) {
					$cadenaPagina[] =  array('opc' => $i);
					// $cadenaPagina .= "<li class=\"active\"><a>" . $i . "</a></li>";
				} else {
					if ($i == ($pagina - 1) || $i == ($pagina - 2)) {
						$cadenaPagina[] =  array('opc' => $i);
						// $cadenaPagina .= "<li><a onclick=\"buscarCompaginado(" . $i . ",'', '".$entidad."')\">" . $i . "</a></li>";
					}
					if ($i == ($pagina + 1) || $i == ($pagina + 2)) {
						$cadenaPagina[] =  array('opc' => $i);
						// $cadenaPagina .= "<li><a onclick=\"buscarCompaginado(" . $i . ",'', '".$entidad."')\">" . $i . "</a></li>";
					}
				}
			}
			if ($i > 1 && $i < ($pagina - 2)) {
				if ($puntosDelante == '') {
					$cadenaPagina[] = array('opc' => '...');
					$puntosDelante = '...';
					// $puntosDelante =  "<li class=\"disabled\"><a href=\"#\">...</a></li>";
					// $cadenaPagina .= $puntosDelante;
				}
			}
			if ($i < $div && $i > ($pagina + 2)) {
				if ($puntosDetras == '') {
					$cadenaPagina[] = array('opc' => '...'); 
					$puntosDetras = '...';
					// $puntosDetras = "<li class=\"disabled\"><a href=\"#\">...</a></li>";
					// $cadenaPagina .= $puntosDetras;
				}
			}
		}
        
		// if (count($cadenaPagina) == 0) {
		// 	$cadenaPagina[] = array('opc' => '1'); //"<li class=\"active\"><a>" . $i . "</a></li>";		
		// }
		// $cadenaPagina .= "</ul>";
		// $fin = count($cadenaPagina) - 1;
		
		$min = 0;
		$max = count($cadenaPagina) - 1;
		
		$inicioArr = $cadenaPagina[$min]['opc'];
		$finArr    = $cadenaPagina[$max]['opc'];
		
		$paginacion = array(
			'cadenapaginacion' => $cadenaPagina,
			'inicio'           => $inicio,
			'fin'              => $fin,
			'nuevapagina'      => $pagina,
			'inicioArr'        => $inicioArr,
			'finArr'      	   => $finArr
		);
		//Input::replace(array('page' => $pagina));
		return $paginacion;
	}


}