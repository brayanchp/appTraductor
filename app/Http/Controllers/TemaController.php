<?php

namespace App\Http\Controllers;

use App\Models\Tema;
use Illuminate\Http\Request;
use App\libraries\Funciones;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TemaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $filtro=$request->get('filtro');
 
        $descripcion=$request->get('descripcion');
        // $filas=$request->get('filas');
        $filas=6;
        $page=$request->get('page');
        $user=Auth::user();
        $temas=DB::table('tema');

        if($descripcion!='' && !is_null($descripcion)){
            $temas=$temas->where('nombre','like',"%$descripcion%");
        }
        $temas=$temas->where('user_id','=',$user->id_user);
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

    	return ['temas' => $lista, 'cantidad' => ($cantidad<10?'0'.$cantidad:$cantidad).($cantidad==1?'Tema':'Tema'), 'page' => $page, 'paginador' => $arrPag, 'inicio' => $inicio, 'fin' => $fin, 'paramInicio' => $paramInicio, 'paramFin' => $paramFin];
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
        $id=$request->id;
        DB::beginTransaction();
        try {
            $mensaje = "";
            $band = true;
            $nombre=$request->get('nombre');
            $validaNombre=Tema::where('nombre',$nombre);

            if($id!=0){
                $validaNombre=$validaNombre->where('id_tema','<>',$id);
            }
             
            $validaNombre=$validaNombre->first();

            if(is_null($validaNombre)){

                if($id==0){
                    $tema=new Tema();

                }else{
                    $tema=Tema::find($id);
                }

                $tema->nombre=$nombre;
                $tema->descripcion=$request->descripcion;
                $tema->user_id=Auth::user()->id_user;
                $tema->save();
                $cad='';
                
                if($id==0){
                    
                    $cad='Registrado';
                }else{
                    $cad='Actualizado';
                }

                $mensaje = 'Tema '.$cad.' Correctamente';

            }
            else{
                $band=false;
                $mensaje = 'Nombre de Tema ya Antes Registrado';

            }
        } catch (\Exception $ex) {
            $mensaje=$ex->getMessage();
            $band=false;
            DB::rollback();
        }
        DB::commit();
        return ['mensaje' => $mensaje, 'estado' => $band];
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Tema  $tema
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $estado=true;
        $tema=Tema::find($id);
        return ['estado'=>(is_null($tema)?false:true),'tema'=>$tema];
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Tema  $tema
     * @return \Illuminate\Http\Response
     */
    public function edit(Tema $tema)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tema  $tema
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tema $tema)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Tema  $tema
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $mensaje='';
        $exito=false;
        DB::beginTransaction();
        try {
            $tema=Tema::find($request->id);
            $tema->is_active=$request->param;
            $tema->save();
            $mensaje='Tema desactivado';
            if($request->param==1){
                $mensaje='Tema activado';
            }
            $exito=true;
        } catch (\Exception $ex) {
            $mensaje="Hubo un error: ".$ex->getMessage();
            $exito=false;
            DB::rollBack();
        }   
        DB::commit();
        return ['estado'=>$exito,'mensaje'=>$mensaje];
    }

    public function count()
    {
        $user=Auth::user();
        // return $user;
        $cantidadTemas=DB::table('tema')->where('user_id','=',$user->id_user)->where('is_active','=',1)->get();

        $cantidadTemas=count($cantidadTemas);
        return ['cantidadTemas'=>$cantidadTemas];
    }

    public function buscar(Request $request)
    {
        $user=Auth::user();
        $query=$request->querytemas;
        $temas=DB::table('tema')->where('user_id','=',$user->id_user)->where('is_active','=',1)->
        where('nombre','like','%'.$query.'%')->get();
        
        return ['temas'=>$temas];
    }
}
