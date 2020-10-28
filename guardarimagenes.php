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

define ('dir_subida','C:\Users\Arantzazu\Desktop\imgusers');

if(isset($_FILES['archivo1']['name'])&&!empty($_FILES['archivo1']['name'][0])){
    $arrayFicheros=$_FILES['archivo1'];
    $numArchivos = $_FILES['archivo1']['name'];
    $tamanio=array_sum( $_FILES['archivo1']['size']);
    $mensaje = '';
    if(($numArchivos>=2 && $tamanio >300000)||($numArchivos==1 && $tamanio>200000)){
        $mensaje= '<span style=" color :red;">'.$codigosErrorSubida[2];
    }else{
        $mensaje .= '<b>Procesando subida de archivos :  </b><br/>';
        for($i=0;$i<count($numArchivos);$i++){
            if(!empty($arrayFicheros['name'][$i])&& isset($arrayFicheros['name'][$i])){
                $nombreFichero   =   $arrayFicheros['name'][$i];
                $errorFichero    =   $arrayFicheros['error'][$i];
                $temporalFichero =   $arrayFicheros['tmp_name'][$i];
                $mensaje .= "- Nombre: $nombreFichero" . ' <br/>';
                if ($errorFichero > 0) {
                    $mensaje.=$codigosErrorSubida[$errorFichero].'<br>';
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
}
function comprobarSistArchivo($nombreFichero){
    $sistemaArchivos=[".jpg",".png"];
    for($i=0;$i<count($sistemaArchivos);$i++){
        if(strstr($nombreFichero, $sistemaArchivos[$i])){
            return true;
        }
    }
    return false;
}
function subirArchivo($nombreFichero,$temporalFichero) {
    if ( is_dir(dir_subida) && is_writable (dir_subida)) {
        if(!file_exists(dir_subida .'/'. $nombreFichero) && move_uploaded_file($temporalFichero,  dir_subida .'/'. $nombreFichero)){
            $mensaje = '<span style=" color :green;">Archivo ha sido subido con exito: </span><br/>';
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
                <?php   if(!empty($mensaje)){
                            echo '<p style="text-align:center;">'.$mensaje."<br>";} ?>
            </div> 
    </div>
</body>
</html>