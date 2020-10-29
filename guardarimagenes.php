<?php
$codigosErrorSubida= [
    0 => 'Subida correcta',
    1 => 'El tamaño del archivo excede el admitido por el servidor',  // directiva upload_max_filesize en php.ini
    2 => 'El tamaño del archivo excede el admitido por el cliente',  // directiva MAX_FILE_SIZE en el formulario HTML
    3 => 'El archivo no se pudo subir completamente',
    4 => 'No se seleccionó ningún archivo para ser subido',
    6 => 'No existe un directorio temporal donde subir el archivo',
    7 => 'No se pudo guardar el archivo en disco',  // permisos
    8 => 'Una extensión PHP evito la subida del archivo'  // extensión PHP
];
//declaramos una constante para la ruta de la carpeta a la que hemos dado todos los permisos 
define ('dir_subida','C:\Users\Arantzazu\Desktop\imgusers');

if(isset($_FILES['archivo1']['name'])){
    $arrayFicheros=$_FILES['archivo1'];
    $numArchivos = count($_FILES['archivo1']['name']);
    $tamanio=array_sum( $_FILES['archivo1']['size']);
    $mensaje = '';
    //si el num archivos es 2 o mas y tiene mas 300kb o si es uno y es mayor que 200kb saltara el error
    if(($numArchivos>=2 && $tamanio >300000)||($numArchivos==1 && $tamanio>200000)){
        $mensaje= '<span style=" color :red;">'.$codigosErrorSubida[2];
    }else{
        //procesamos los archivos y almacenamos los datos en variables
        $mensaje = '<b>Procesando subida de archivos :  </b><br/>';
        for($i=0;$i<$numArchivos;$i++){
            $nombreFichero   =   $arrayFicheros['name'][$i];
            $errorFichero    =   $arrayFicheros['error'][$i];
            $temporalFichero =   $arrayFicheros['tmp_name'][$i];
            $mensaje .= "- Nombre: $nombreFichero" . ' <br/>';
            //comprobamos si hay errores
              if ($errorFichero > 0) {
                  $mensaje.='<span style=" color :red;">'.$codigosErrorSubida[$errorFichero].'<br>';
              }else{
                 if(comprobarSistArchivo($nombreFichero)){
                     $mensaje.=subirArchivo($nombreFichero,$temporalFichero);
                 }else{
                     $mensaje.='<span style=" color :red;">No se acepta ese sistema de archivo </span><br>';
                    }
                }
          }
     }
}
/*los sist. de archivos validos los almacenamos en un array
*if comprobamos si el nombre del archivo contiene los sist.archivos almacenados en el array
*strts equivale a lastindexOf en java 
*/
function comprobarSistArchivo($nombreFichero){
    $sistemaArchivos=[".jpg",".png"];
    for($i=0;$i<count($sistemaArchivos);$i++){
        if(strstr($nombreFichero, $sistemaArchivos[$i])){
            return true;
        }
    }
    return false;
}
/*comprobamos si la constate declarada es un directorio y tiene permisos de escritura
*si es asi, comprobamos si existe el fichero/imagen y lo movemos del fichero temp.
*/
function subirArchivo($nombreFichero,$temporalFichero) {
    if ( is_dir(dir_subida) && is_writable (dir_subida)) {
        if(!file_exists(dir_subida .'/'. $nombreFichero) && move_uploaded_file($temporalFichero,  dir_subida .'/'. $nombreFichero)){
            $mensaje = '<span style=" color :green;">Archivo ha sido subido con exito . </span><br/>';
        }else{
            $mensaje='<span style=" color :red;">El archivo '.$nombreFichero." ya existe</span> <br/>";
        }
    }else{
        $mensaje='<span style=" color :red;">Error de permisos en el directorio</span> <br/>';
    }
    return $mensaje;
}
?>
<html>
    <head>
    <title>Formulario de subida de archivos</title>
    <meta charset="UTF-8">
    <style>
        #cabecera{
            text-align:center;
            border: 1px black solid;
            background:#FBEFFB;
            
        }
        form{
            padding:30px;
           text-align:center;
        }
        #cuerpo{
            border: 1px black solid;
            background: #CED8F6; 
            padding-left: 10px;            
        }
        #main{
            width:25%;
            margin-left:20px;
        }
    </style>
    </head>
<body>
    <div id="main">
        <div id ="cabecera">
            <h2>Subida y alojamiento de archivo en el servidor</h2>
        </div>
        <div id="cuerpo">
       		 <form  enctype="multipart/form-data" action="<?=$_SERVER['PHP_SELF']?>"  method="post"  >
              	 <input type="hidden" name="MAX_FILE_SIZE" value="300000" /> 
                 <label>Elija el archivo a subir :</label> <input name="archivo1[]" type="file" multiple="multiple"/> <br />
                 <input type="submit" value="Subir archivo"/>
            </form>
        </div>
       		<div id="cuerpo">
                <?php   if(!empty($mensaje)){ //si la variable mensaje no esta vacia muestra mensaje
                            echo '<p style="text-align:center;">'.$mensaje."<br>";} ?>
            </div> 
    </div>
</body>
</html>
