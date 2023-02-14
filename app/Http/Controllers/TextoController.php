<?php

namespace App\Http\Controllers;

use App\Models\Texto;
use Illuminate\Http\Request;
use App\libraries\Funciones;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TextoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $descripcion=$request->get('descripcion');
        // $filas=$request->get('filas');
        $filas=6;
        $page=$request->get('page');

        $temas=DB::table('texto');

        if($descripcion!='' && !is_null($descripcion)){
            $temas=$temas->where('titulo','like',"%$descripcion%");
        }

        $temas=$temas->orderby('titulo','ASC');

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

    	return ['textos' => $lista, 'cantidad' => ($cantidad<10?'0'.$cantidad:$cantidad).($cantidad==1?'Tema':'Tema'), 'page' => $page, 'paginador' => $arrPag, 'inicio' => $inicio, 'fin' => $fin, 'paramInicio' => $paramInicio, 'paramFin' => $paramFin];
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
            $validaNombre=Texto::where('titulo',$nombre);

            if($id!=0){
                $validaNombre=$validaNombre->where('id_texto','<>',$id);
            }
             
            $validaNombre=$validaNombre->first();

            if(is_null($validaNombre)){

                if($id==0){
                    $texto=new Texto();

                }else{
                    $texto=Texto::find($id);
                }

                $texto->titulo=$nombre;
                $texto->tema_id=$request->id_tema;
                $texto->user_id=Auth::user()->id_user;
                $texto->contenido=$request->contenido;
                $texto->save();
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
     * @param  \App\Models\Texto  $texto
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {   
        $texto=DB::table('texto')->join('tema','texto.tema_id','=','tema.id_tema')->where('texto.id_texto',$id)->first();

        return ['estado'=>(is_null($texto)?false:true),'texto'=>$texto];
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Texto  $texto
     * @return \Illuminate\Http\Response
     */
    public function edit(Texto $texto)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Texto  $texto
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Texto $texto)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Texto  $texto
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $mensaje='';
        $exito=false;
        DB::beginTransaction();
        try {
            $texto=Texto::find($request->id);
            $texto->is_active=$request->param;
            $texto->save();
            $mensaje='Texto desactivado';
            if($request->param==1){
                $mensaje='Texto activado';
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
        $cantidadTextos=DB::table('texto')->where('user_id','=',$user->id_user)->where('is_active','=',1)->get();

        $cantidadTextos=count($cantidadTextos);
        return ['cantidadTextos'=>$cantidadTextos];
    }
}
