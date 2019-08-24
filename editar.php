<?php include 'includes/redirect.php';?>
<?php require_once("includes/header.php")?>
<?php
function mostrarError($error, $field){
  if(isset($error[$field]) && !empty($field)){
    $alerta='<div class="alert alert-danger">'.$error[$field].'</div>';
  }else{
    $alerta='';
  }
  return $alerta;
}
function setValueField($datos,$field, $textarea=false){
  if(isset($datos) && count($datos)>=1){
    if($textarea != false){
      echo $datos[$field];
    }else{
      echo "value='{$datos[$field]}'";
    }
  }
}
//Buscar Usuario
if(!isset($_GET["id"]) || empty($_GET["id"]) || !is_numeric($_GET["id"])){
  header("location:index.php");
  }
$id=$_GET["id"];
$user_query=mysqli_query($db, "SELECT * FROM estudiante WHERE idestudiante={$id}");
$user=mysqli_fetch_assoc($user_query);
if(!isset($user["idestudiante"]) || empty($user["idestudiante"])){
  header("location:index.php");
}
//Validar usuario
$error=array();
if(isset($_POST["submit"])){
	if(!empty($_POST["documento"])){
$documento_validador=true;
}else{
$documento_validador=false;
$error["documento"]="El documento no es válido";
}
 if(!empty($_POST["nombres"]) && strlen($_POST["nombres"]<=20) && !is_numeric($_POST["nombres"]) && !preg_match("/[0-9]/", $_POST["nombres"])){
$nombres_validador=true;
}else{
$nombres_validador=false;
$error["nombres"]="El nombre no es válido";
}
  if(!empty($_POST["apellidos"])&& !is_numeric($_POST["apellidos"]) && !preg_match("/[0-9]/", $_POST["apellidos"])){
      $apellidos_validador=true;
     }else{
     $apellidos_validador=false;
       $error["apellidos"]="Los apellidos no son válidos";
        }
     if(isset($_POST["instrumento"]) && is_numeric($_POST["instrumento"])){
       $instrumento_validador=true;
      }else{
      $instrumento_validador=false;
       $error["instrumento"]="Seleccione un instrumento ";
	 }
       if(!empty($_POST["direccion"])&& !is_numeric($_POST["direccion"]) && !preg_match("/[0-9]/", $_POST["direccion"])){
      $direcccion_validador=true;
     }else{
     $direccion_validador=false;
       $error["direccion"]="La direccion no es válido";
        }
     if(!empty($_POST["acudiente"])&& !is_numeric($_POST["acudiente"]) && !preg_match("/[0-9]/", $_POST["acudiente"])){
      $acudiente_validador=true;
     }else{
     $acudiente_validador=false;
       $error["acudiente"]="El acudiente no es válido";
        }
	if(!empty($_POST["telefono"])){
      $telefono_validador=true;
     }else{
     $telefono_validador=false;
       $error["telefono"]="El telefono no es válido";
        }
	if(!empty($_POST["email"]) && filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)){
       $email_validador=true;
      }else{
       $email_validador=false;
       $error["email"]="Introduzca un mail válido";
        }
        //nuevo código
        $image=null;
        if(isset($_FILES["image"]) && !empty($_FILES["image"]["tmp_name"])){
          if(!is_dir("uploads")){
            $dir = mkdir("uploads", 0777, true);
          }else{
            $dir=true;
          }
          if($dir){
            $filename= time()."-".$_FILES["image"]["name"]; //concatenar función tiempo con el nombre de imagen
            $muf=move_uploaded_file($_FILES["image"]["tmp_name"], "uploads/".$filename); //mover el fichero utilizando esta función
            $image=$filename;
            if($muf){
              $image_upload=true;
            }else{
              $image_upload=false;
              $error["image"]= "La imagen no se ha subido";
            }
          }
          //var_dump($_FILES["image"]);
          //die();
  	 	}
    //Actualizar Usuarios en la base de Datos
    if(count($error)==0){
      $sql= "UPDATE estudiante set documento='{$_POST["documento"]}',"
	  . "nombres='{$_POST["nombres"]}',"
      . "apellidos= '{$_POST["apellidos"]}',"
      . "direccion= '{$_POST["direccion"]}',"
	  . "acudiente= '{$_POST["acudiente"]}',"
	  . "telefono= '{$_POST["telefono"]}',"  
      . "email= '{$_POST["email"]}',";
      if(isset($_POST["password"]) && !empty($_POST["password"])){
        $sql.= "password='".sha1($_POST["password"])."', ";
     }
     //Código nuevo
     if(isset($_FILES["image"]) && !empty($_FILES["image"]["tmp_name"])){
       $sql.= "image='{$image}', ";
    }
      $sql.= "instrumento= '{$_POST["instrumento"]}'WHERE idestudiante={$user["idestudiante"]};";
      $update_user=mysqli_query($db, $sql);
      if($update_user){
        $user_query=mysqli_query($db, "SELECT * FROM estudiante WHERE idestudiante={$id}");
        $user=mysqli_fetch_assoc($user_query);
      }
    }else{
      $update_user=false;
    }
}
?>
<h2>Editar Usuario <?php echo $user["idestudiante"]."-".$user["nombres"]." ".$user["apellidos"];?></h2>
<?php if(isset($_POST["submit"]) && count($error)==0 && $update_user !=false){?>
  <div class="alert alert-success">
    El usuario se ha actualizado correctamente !!
  </div>
<?php }elseif(isset($_POST["submit"])){?>
  <div class="alert alert-danger">
    El usuario NO se ha actualizado correctamente !!
  </div>
<?php } ?>
<form action="" method="POST" enctype="multipart/form-data">
	<label for="documento">Nombre:
    <input type="text" name="documento" class="form-control" <?php setValueField($user, "documento");?>/>
    <?php echo mostrarError($error, "documento");?>
    </label>
    </br></br>
    <label for="nombres">Nombres:
    <input type="text" name="nombres" class="form-control" <?php setValueField($user, "nombres");?>/>
    <?php echo mostrarError($error, "nombres");?>
    </label>
    </br></br>
    <label for="apellidos">Apellidos:
        <input type="text" name="apellidos" class="form-control" <?php setValueField($user, "apellidos");?>/>
        <?php echo mostrarError($error, "apellidos");?>
    </label>
    </br></br>
    <label for="instrumento" class="form-control">Instrumento:
        <select name="instrumento">
        <option value="0" <?php if($user["instrumento"]==0){echo "selected='selected'";}?>>Trompeta</option>
            <option value="1" <?php if($user["instrumento"]==1){echo "selected='selected'";}?>>Clarinete</option>
			<option value="2" <?php if($user["instrumento"]==2){echo "selected='selected'";}?>>Saxofon</option>
			<option value="3" <?php if($user["instrumento"]==3){echo "selected='selected'";}?>>Obeo</option>
			<option value="4" <?php if($user["instrumento"]==4){echo "selected='selected'";}?>>Violin</option>
			<option value="5" <?php if($user["instrumento"]==5){echo "selected='selected'";}?>>Bombo</option>
			<option value="6" <?php if($user["instrumento"]==6){echo "selected='selected'";}?>>Corno</option>
			<option value="7" <?php if($user["instrumento"]==7){echo "selected='selected'";}?>>Frances</option>
        </select>
        <?php echo mostrarError($error, "instrumento");?>
    </label>
    </br></br>
<label for="direccion">Direccion:
        <input type="text" name="direccion" class="form-control" <?php setValueField($user, "direccion");?>/>
        <?php echo mostrarError($error, "direccion");?>
    </label>
    </br></br>
<label for="acudiente">Acudiente:
        <input type="text" name="acudiente" class="form-control" <?php setValueField($user, "acudiente");?>/>
        <?php echo mostrarError($error, "acudiente");?>
    </label>
    </br></br>
<label for="telefono">Telefono:
        <input type="text" name="telefono" class="form-control" <?php setValueField($user, "telefono");?>/>
        <?php echo mostrarError($error, "telefono");?>
    </label>
    </br></br>
    <label for="email">Email:
        <input type="email" name="email" class="form-control" <?php setValueField($user, "email");?>/>
        <?php echo mostrarError($error, "email");?>
    </label>
    </br></br>
    <label for="image">
      <?php if($user["image"] != null){?>
        Imagen de Perfil: <img src="uploads/<?php echo $user["image"] ?>" width="100"/><br/>
      <?php } ?>
        Actualizar Imagen de Perfil:
        <input type="file" name="image" class="form-control"/>
        <!--Nuevo Código-->

    </label>
    </br></br>
    <label for="password">Contraseña:
        <input type="password" name="password" class="form-control"/>
        <?php echo mostrarError($error, "password");?>
    </label>
    </br></br>
    <input type="submit" value="Enviar" name="submit" class="btn btn-success"/>
</form>
<?php require_once("includes/footer.php")?>
