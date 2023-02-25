<?php

namespace App\Http\Controllers;

use App\Models\Examen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\libraries\Funciones;
use Illuminate\Support\Facades\Auth;

class ExamenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $descripcion=$request->get('descripcion');
        $id_texto=$request->get('idtexto');
        // $filas=$request->get('filas');
        $filas=6;
        $page=$request->get('page');
        $temas=DB::table('examen as ex')->join('texto as tex','ex.texto_id','=','tex.id_texto');
        if($descripcion!='' && !is_null($descripcion)){
            $temas=$temas->where('nombre','like',"%$descripcion%");
        }
        $temas=$temas->where('texto_id','=',$id_texto);

        $temas=$temas->orderby('nombre','ASC');

        $lista=$temas->get();

        $cantidad = count($lista);
 
        if ($cantidad > 0) {
			$paginador = new Funciones();
			// dd($filas);
			$paramPaginador = $paginador->generarPaginacion($lista, $page, $filas);
			$arrPag = $paramPaginador['cadenapaginacion'];
			$page = $paramPaginador['nuevapagina'];
			$inicio = $paramPaginador['inicio'];
            $fin = $paramPaginador['fin'];
            $paramInicio = $paramPaginador['inicioArr'];
            $paramFin = $paramPaginador['finArr'];
            
		} else {
			$arrPag = [['opc' => '1']];
			$page = '1';
			$inicio = '1';
            $fin = '1';
            $paramInicio = '1';
            $paramFin = '1';
        }
        
        $lista = $temas->offset(($page-1)*$filas)
				   ->limit($filas)
                   ->get();

    	return ['examenes' => $lista, 'cantidad' => ($cantidad<10?'0'.$cantidad:$cantidad).($cantidad==1?'Tema':'Tema'), 'page' => $page, 'paginador' => $arrPag, 'inicio' => $inicio, 'fin' => $fin, 'paramInicio' => $paramInicio, 'paramFin' => $paramFin];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Examen  $examen
     * @return \Illuminate\Http\Response
     */
    public function show(Examen $examen)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Examen  $examen
     * @return \Illuminate\Http\Response
     */
    public function edit(Examen $examen)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Examen  $examen
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Examen $examen)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Examen  $examen
     * @return \Illuminate\Http\Response
     */
    public function destroy(Examen $examen)
    {
        //
    }

    public function count()
    {
        $user=Auth::user();
        // return $user;
        $cantidadExamenes=DB::table('examen')->where('user_id','=',$user->id_user)->where('is_active','=',1)->get();

        $cantidadExamenes=count($cantidadExamenes);
        return ['cantidadExamenes'=>$cantidadExamenes];
    }

    public function countpendientes()
    {
        $user=Auth::user();
        // return $user;
        $cantidadExamenes=DB::table('examen')->where('user_id','=',$user->id_user)->where('is_active','=',1)->where('estado','=','P')->get();

        $cantidadExamenes=count($cantidadExamenes);
        return ['cantidadExamenes'=>$cantidadExamenes];
    }
}
