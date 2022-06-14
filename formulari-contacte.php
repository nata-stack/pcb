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

if (form_mail("monzn.luis@gmail.com", $_POST[asunto],
    "Nou missatge del formulari web:\n", $_POST[email]))
    header("Location: enviamentok.html");
?>
