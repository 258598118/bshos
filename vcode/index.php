<?php
/*
// - ����˵�� : ����һ����֤��ͼƬ
// - �������� : zhuwenya (zhuwenya@126.com)
// - ����ʱ�� : 2007-11-23 09:39
*/
include dirname(__FILE__)."/function.php";

/*
	����˵����
	s: һ���������
	w: width����֤��ͼƬ���;
	h: height����֤��ͼƬ�߶�;
*/
/*
// ��ȡ��ǰĿ¼�µ����������ļ�:
$aFonts = array();
$handle = opendir(dirname(__FILE__));
while (false !== ($file = readdir($handle))) {
	if (strrchr($file, ".") == ".ttf") {
		$aFonts[] = $file;
	}
}
closedir($handle);

// �����ѡһ������ʹ��:
if (($nFonts = count($aFonts)) > 0) {
	$rand = rand() % $nFonts;
	$fontname = $aFonts[$rand];
} else {
	exit("error: no font file found!");
}

// ��֤��Ĵ�С:
$w = $_GET["w"] ? max(40, intval($_GET["w"])) : 60;
$h = $_GET["h"] ? max(16, intval($_GET["h"])) : 20;

// ��������:
$offsetx = 6;
$offsety = 6;

$Out = get_code_from_hash($_GET["s"]);


// ������Ҫ��ͼƬ:
$im = imagecreate($w, $h);
$bg = imagecolorallocate($im, 235, 235, 245);
$bd = imagecolorallocate($im, 102, 102, 102);

// ���������:
for ($ni = 0; $ni < 8; $ni ++) {
	$lc = imagecolorallocate($im, rand() % 55 + 200, rand() % 55 + 200, rand() % 55 + 200);
	imageline($im, rand() % $w, rand() % $h, rand() % $w, rand() % $h, $lc);
}

// ��������ص�:
for ($ni = 0; $ni < 100; $ni++) {
	$pc = imagecolorallocate($im, rand() % 255, rand() % 255, rand() % 255);
	imagesetpixel($im, rand() % $w, rand() % $h, $pc);
}

// �߿���:
imagepolygon($im, array(0,0, $w-1,0, $w-1,$h-1, 0,$h-1), 4, $bd);

// ��:
$charlens = strlen($Out);
$fontsize = floor(min(($w - $offsetx) / $charlens, $h - $offsety));
for ($ni = 0; $ni < $charlens; $ni++) {
	$fc = imagecolorallocate($im, rand() % 100, rand() % 100, rand() % 100);
	$txt = substr($Out, $ni, 1);
	$x = $ni * (($w - $offsetx) / $charlens) + $offsetx / 2 + (($w - $offsetx) / $charlens - $fontsize) / 2;
	$y = rand() % 3 - 1 + $h / 2 + $fontsize / 2;
	imagettftext($im, $fontsize, rand() % 40 - 15, $x, $y, $fc, $fontname, $txt);
}

// ���:
header("Content-type: image/jpeg");
imagejpeg($im);
imagedestroy($im);
*/

$num = 4;
$size = 15;
$width = 0;
$height = 0;

! $width && $width = $num * $size * 4 / 5 + 5;
! $height && $height = $size + 10;
// ȥ���� 0 1 O l ��
//$str = get_code_from_hash($_GET["s"]);
//$str = "23456789abcdefghijkmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVW";
$code = '';
$code=get_code_from_hash($_GET["s"]);
// ��ͼ��
$im = imagecreatetruecolor ( $width, $height );
// ����Ҫ�õ�����ɫ
$back_color = imagecolorallocate ( $im, 235, 236, 237 );
$boer_color = imagecolorallocate ( $im, 118, 151, 199 );
$text_color = imagecolorallocate ( $im, mt_rand ( 0, 200 ), mt_rand ( 0, 120 ), mt_rand ( 0, 120 ) );
// ������
imagefilledrectangle ( $im, 0, 0, $width, $height, $back_color );
// ���߿�
imagerectangle ( $im, 0, 0, $width - 1, $height - 1, $boer_color );
// ��������
for($i = 0; $i < 5; $i ++)
{
	$font_color = imagecolorallocate ( $im, mt_rand ( 0, 255 ), mt_rand ( 0, 255 ), mt_rand ( 0, 255 ) );
	imagearc ( $im, mt_rand ( - $width, $width ), mt_rand ( - $height, $height ), mt_rand ( 30, $width * 2 ), mt_rand ( 20, $height * 2 ), mt_rand ( 0, 360 ), mt_rand ( 0, 360 ), $font_color );
}
// �����ŵ�
for($i = 0; $i < 50; $i ++)
{
	$font_color = imagecolorallocate ( $im, mt_rand ( 0, 255 ), mt_rand ( 0, 255 ), mt_rand ( 0, 255 ) );
	imagesetpixel ( $im, mt_rand ( 0, $width ), mt_rand ( 0, $height ), $font_color );
}
// ����֤��
@imagefttext ( $im, $size, 0, 5, $size + 3, $text_color, 'c:\\WINDOWS\\Fonts\\simsun.ttc', $code );
header ( "Cache-Control: max-age=1, s-maxage=1, no-cache, must-revalidate" );
header ( "Content-type: image/png;charset=gb2312" );
imagepng ( $im );
imagedestroy ( $im );

// �����ȡһ����֤�����:
function GetVCode($CharSet = 0) {
	$aUseBit = array(2, 12, 15, 31);
	switch ($CharSet) {
		case 1: $UseChars = "abcdefghijkmnpqrstuvwxy"; break;
		case 2: $UseChars = "0123456789abcdefghijkmnpqrstuvwxy"; break;
		default: $UseChars = "0123456789";
	}
	$UseCharLen = strlen($UseChars);
	$Out = "";
	foreach ($aUseBit as $pos) {
		$rand = rand() % $UseCharLen;
		$Out .= substr($UseChars, $rand, 1);
	}

	return $Out;
}
?>