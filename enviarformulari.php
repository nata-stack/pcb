<?php
function form_mail($sPara, $sAsunto, $sTexto, $sDe) {
    $bHayFicheros = 0;
    $sCabeceraTexto = "";
    $sAdjuntos = "";

    if ($sDe)$sCabeceras = "From:".$sDe."\n";
    else $sCabeceras = "";
    $sCabeceras .= "MIME-version: 1.0\n";
    $sCabeceras .= "Content-Type: text/plain; charset=UTF-8"; 
    foreach ($_POST as $sNombre => $sValor)
        $sTexto = $sTexto."\n".$sNombre." = ".$sValor;

        foreach ($_FILES as $vAdjunto) {
            if ($bHayFicheros == 0) {
                $bHayFicheros = 1;
				$cabeceras = "MIME-Version: 1.0\r\n";
				$sCabeceras .= "Content-Type: text/plain; charset=UTF-8\r\n";
				$cabeceras .= "Content-Transfer-Encoding: 8bit\r\n";
                $sCabeceras .= "Content-type: multipart/mixed;";
                $sCabeceras .= "boundary=\"--_Separador-de-mensajes_--\"\n";

                $sCabeceraTexto = "----_Separador-de-mensajes_--\n";
                $sCabeceraTexto .= "Content-Type: text/plain; charset=UTF-8\r\n";
                $sCabeceraTexto .= "Content-transfer-encoding: 7BIT\n";

                $sTexto = $sCabeceraTexto.$sTexto;
            }

            if ($vAdjunto["size"] > 0) {
                $sAdjuntos .= "\n\n----_Separador-de-mensajes_--\n";
                $sAdjuntos .= "Content-type: ".$vAdjunto["type"].";name=\"".$vAdjunto["name"]."\"\n";;
                $sAdjuntos .= "Content-Transfer-Encoding: BASE64\n";
                $sAdjuntos .= "Content-disposition: attachment;filename=\"".$vAdjunto["name"]."\"\n\n";

                $oFichero = fopen($vAdjunto["tmp_name"], 'r');
                $sContenido = fread($oFichero, filesize($vAdjunto["tmp_name"]));
                $sAdjuntos .= chunk_split(base64_encode($sContenido));
                fclose($oFichero);
            }
        }

    if ($bHayFicheros)
        $sTexto .= $sAdjuntos."\n\n----_Separador-de-mensajes_----\n";
    return(mail($sPara, mb_encode_mimeheader($sAsunto), $sTexto, $sCabeceras));
}

// Cambiar aqui el email donde se quieren recibir los mensajes.

if (form_mail("proves@placomunitaribarceloneta.org", $_POST[asunto],
    "Nou missatge del formulari web:\n", $_POST[email]))
    header("Location: enviamentok.html");
?>







<?php
//Recaptcha de google per a evitar spam del costat del servidor
 
  if($_SERVER["REQUEST_METHOD"] === "POST")
    {
 
    // Colocamos la clave secreta de reCAPTCHA v3 
    define("SECRET_KEY", '6LdzV54fAAAAAAQd2eeSJy06Q1np1OsrvltD2ZYE'); 
 
    $token = $_POST['token'];
    $action = $_POST['action'];
     
    // Mediante CURL hago un Post a la api de reCaptcha 
    $datos = curl_init();
    curl_setopt($datos, CURLOPT_URL,"https://www.google.com/recaptcha/api/siteverify");
    curl_setopt($datos, CURLOPT_POST, 1);
    
    // En el Post a la api de reCaptcha envio la Secret Key y el Token generado en la vista HTML
    curl_setopt($datos, CURLOPT_POSTFIELDS, http_build_query(
      array(
        'secret' => SECRET_KEY, 
        'response' => $token
      )
    ));
 
    // Obtengo una respuesta de reCaptcha y los datos obtenidos los decodifico para poder verificarlos 
    curl_setopt($datos, CURLOPT_RETURNTRANSFER, true); 
    $respuesta = curl_exec($datos);    
    curl_close($datos);
    $datos_respuesta = json_decode($respuesta, true);
    
     
    // Verificamos los datos 
    if($datos_respuesta["success"] == '1' && $datos_respuesta["action"] == $action && $datos_respuesta["score"] >= 0.4) {
 
      // Si no es un robot hago una redirección con un mensaje 
      $puntaje = "<p><span style=color:green;font-weight:bold;>Puntaje: </span>".json_encode($datos_respuesta["score"])."</p>";
      $mensaje = "<p><span style=color:green;font-weight:bold;>Resultado: </span>No eres un robot. </p>";
      header("Location: index.php?mensaje=$mensaje&puntaje=$puntaje");
 
    } else {
 
      // Si es un robot hago una redirección con un mensaje 
      $puntaje = "<p> <span style=color:red;font-weight:bold;>Puntaje: </span>".json_encode($datos_respuesta["score"])."</p>";
      $mensaje = "<p> <span style=color:red;font-weight:bold;>Resultado: </span>Tú eres un robot. </p>";
      header("Location: index.php?mensaje=$mensaje&puntaje=$puntaje");
 
    }
 
  }