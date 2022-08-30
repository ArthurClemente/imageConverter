<?php
// =====================CONVERTER JPEG PARA WEBP==========================
function convertToWEBP($image)
{
   $dir = dirname(__FILE__);
   print_r(scandir($dir));
   die();
   $imageJpeg = $dir . "$image";
   $im = @imagecreatefromjpeg($imageJpeg);

   if (!$im) {
      $im = imagecreatetruecolor(150, 30);
      $bgc = imagecolorallocate($im, 255, 255, 255);
      $tc = imagecolorallocate($im, 0, 0, 0);

      imagefilledrectangle($im, 0, 0, 150, 30, $bgc);

      imagestring($im, 1, 5, 5, 'Error loading ' . $image, $tc);
   }


   imagewebp($im, $dir . '/carina' . image_type_to_extension(IMAGETYPE_WEBP));
   $imwebp = imagecreatefromwebp($dir . '/carina.webp');

   //  unlink($imageJpeg);
   //  return $imwebp;
}
//=======================================================================
// =====================CRIAR JPEG=======================================
function createImgJPEGFromBase()
{
   $dir = dirname(__FILE__);
   $json = file_get_contents('./img.Json');
   $json =  json_decode($json);
   $base = $json->img;

   $imagem = base64_decode($base);
   $arquivo = fopen($dir . "/ednaldo" . image_type_to_extension(IMAGETYPE_JPEG), 'w');
   fwrite($arquivo, $imagem);
   fclose($arquivo);
}
//=======================================================================
// =====================SALVAR WEBP NO BANCO==========================
function saveImg()
{
   $server = '';
   $database = '';
   $user = '';
   $password = '';

   $imagem = dirname(__FILE__) . '/ednaldo.webp';

   $handle = fopen($imagem, 'rb');
   $conteudo = fread($handle, filesize($imagem));
   fclose($handle);
   $img = base64_encode($conteudo);


   try {
      $conn = new PDO("sqlsrv:Server=$server; Database=$database", $user, $password);
   } catch (PDOException $e) {
      echo "Connection failed: " . $e->getMessage();
   }

   // Get all the submitted data from the form
   $sql = "INSERT INTO SystemAppUserImage VALUES(CONVERT(varbinary(max), :img), 1);";

   // Execute query
   $query = $conn->prepare($sql);
   $query->bindValue(':img', $img, PDO::PARAM_STR);
   $query->execute();

   unlink('ednaldo.webp');
}
//=======================================================================

// =====================MOSTRAR DADOS SALVOS NO BANCO==========================
function showSavedData()
{
   $server = '';
   $database = '';
   $user = '';
   $password = '';

   try {
      $conn = new PDO("sqlsrv:Server=$server; Database=$database", $user, $password);
   } catch (PDOException $e) {
      echo "Connection failed: " . $e->getMessage();
   }
   $sql = "SELECT CONVERT(varchar(max), img) FROM  SystemAppUserImage;";
   $query = $conn->prepare($sql);
   $query->execute();
   $result = $query->fetchAll(PDO::FETCH_ASSOC);
   $dados = $result;
   print_r($dados);
}
//==============================================================================

// =====================PEGAR IMAGEM DE ACORDO COM O userid==========================
function createWEBPFromDataBase()
{
   $server = '';
   $database = '';
   $user = '';
   $password = '';

   $dir = dirname(__FILE__);

   try {
      $conn = new PDO("sqlsrv:Server=$server; Database=$database", $user, $password);
   } catch (PDOException $e) {
      echo "Connection failed: " . $e->getMessage();
   }
   $sql = "SELECT CONVERT(varchar(max), img) as img, userid FROM SystemAppUserImage WHERE userid = 1;";
   $query = $conn->prepare($sql);
   $query->execute();
   $result = $query->fetch(PDO::FETCH_ASSOC);
   $imagem = base64_decode($result['img']);
   $userId = $result['userid'];

   $handle = fopen($dir . "/ednaldo" . $userId . image_type_to_extension(IMAGETYPE_WEBP), 'w');
   fwrite($handle, $imagem);
   fclose($handle);
}
//=======================================================================

function createImgJPEG()
{
   $dir = dirname(__FILE__);
   $imgWebp = $dir . '/carina.webp';
   $img = @imagecreatefromwebp($imgWebp);
   imagejpeg($img, $dir . '/carina' .  image_type_to_extension(IMAGETYPE_JPEG));

   $imjpeg = imagecreatefromjpeg($dir . 'carina.jpeg');
   return $imjpeg;
}

convertToWEBP('/carina.jpeg');
