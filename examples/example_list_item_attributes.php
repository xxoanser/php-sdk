<?php
session_start();

require '../Meli/meli.php';
require '../configApp.php';

$meli = new Meli($appId, $secretKey);

// Example: Attributes from your product in database
$productDetail = array(
    "codigo_de_barras" => "190198043566",
    "cor" => "Preto",
    "largura" => "188",
    "tela" => "4.7",
    "touch" => "sim",
    "camera" => "sim",
    "gps" => "sim",
    "mp3" => "sim",
    "so" => "iOS",
    "resolucao" => "1920 x 1080",
    "bateria" => "3980",
    "camera_frontal" => "7"
);


// Example Attributes match, simulating the data from your database
$attributesMap = array(
    //your ID           //API ID
    "codigo_de_barras" => "EAN",
    "largura"          => "WEIGHT",
    "tela"             => "SCREEN_SIZE",
    "so"               => "OPERATING_SYSTEM",
    "resolucao"        => "DISPLAY_RESOLUTION",
    "bateria"          => "BATTERY_CAPACITY",
    "camera_frontal"   => "FRONT_CAMERA_RESOLUTION"
);

// Example Attributes match, simulating the data from your database
$attributesBooleanType = array(
    "touch"            => "TOUCH_SCREEN",
    "camera"           => "DIGITAL_CAMERA",
    "gps"              => "GPS",
    "mp3"              => "MP3"
);

// Example Attributes match, simulating the data from your database
$attributesColor = array(
    "cor"              => "COLOR"
);

// Example Attributes match, simulating the data from your database
$attributesBoolean = array(
    "sim"     => "242085",
    "nao"     => "242084"
);

// Example Attributes match, simulating the data from your database
$colorMap = array(
    "Preto"             => "52049", // Preto
    "Azul"              => "52028", // Azul
    "Vermelho"          => "51993", // Vermelho
    "Violeta"           => "52035", // Violeta
    "Marrom"            => "52005", // Marrom
    "Verde"             => "52014", // Verde
    "Cinza-escuro"      => "52051", // Cinza-escuro
    "Laranja"           => "52000", // Laranja
    "Azul-celeste"      => "52021", // Azul-celeste
    "Rosa"              => "51994", // Rosa
    "Dourado"           => "283164", // Dourado
    "Prateado"          => "52053", // Prateado
    "Amarelo"           => "52007", // Amarelo
    "Cinza-claro"       => "283165", // Cinza-claro
    "Branco"            => "52055", // Branco
    "Azul-escuro"       => "52033", // Azul-escuro
    "Azul-marinho"      => "283161", // Azul-marinho
    "Azul-celeste"      => "52031", // Azul-celeste
    "Azul-claro"        => "52029", // Azul-claro
    "Bordô"             => "51998", // Bordô
    "Terracota"         => "51996", // Terracota
    "Coral"             => "283149", // Coral
    "Coral-claro"       => "283148", // Coral-claro
    "Violeta-escuro"    => "52047", // Violeta-escuro
    "Azul-violeta"      => "283162", // Azul-violeta
    "Lilás"             => "52038", // Lilás
    "Lavanda"           => "52036", // Lavanda
    "Marrom-escuro"     => "283155", // Marrom-escuro
    "Marrom-claro"      => "283154", // Marrom-claro
    "Palha"             => "283153", // Palha
    "Bege"              => "52001", // Bege
    "Verde-escuro"      => "52019", // Verde-escuro
    "Verde-musgo"       => "283158", // Verde-musgo
    "Verde-limão"       => "283157", // Verde-limão
    "Verde-claro"       => "52015", // Verde-claro
    "Chocolate"         => "283152", // Chocolate
    "Laranja-escuro"    => "283151", // Laranja-escuro
    "Laranja-claro"     => "283150", // Laranja-claro
    "Nude"              => "52003", // Nude
    "Azul-petróleo"     => "52024", // Azul-petróleo
    "Azul-turquesa"     => "283160", // Azul-turquesa
    "Cíano"             => "283159", // Cíano
    "Azul-piscina"      => "52022", // Azul-piscina
    "Magenta"           => "52042", // Magenta
    "Rosa-chiclete"     => "283163", // Rosa-chiclete
    "Rosa-escuro"       => "52045", // Rosa-escuro
    "Rosa-claro"        => "52043", // Rosa-claro
    "Dourado-escuro"    => "52012", // Dourado-escuro
    "Ocre"              => "52010", // Ocre
    "Cáqui"             => "283156", // Cáqui
    "Creme"             => "52008"  // Creme
);


if($_GET['code']) {

	// If the code was in get parameter we authorize
	$user = $meli->authorize($_GET['code'], $redirectURI);

	// Now we create the sessions with the authenticated user
	$_SESSION['access_token'] = $user['body']->access_token;
	$_SESSION['expires_in'] = $user['body']->expires_in;
	$_SESSION['refresh_token'] = $user['body']->refresh_token;

	// We can check if the access token in invalid checking the time
	if($_SESSION['expires_in'] + time() + 1 < time()) {
		try {
			print_r($meli->refreshAccessToken());
		} catch (Exception $e) {
			echo "Exception: ",  $e->getMessage(), "\n";
		}
	}

    //We construct the attributes list
    $attributes = array();
    foreach ($productDetail as $key => $val) {
        $narray = array();

        //Array custom value type
        if(array_key_exists($key, $attributesMap)){
            $narray = array(
                "id" => $attributesMap[$key],
                "value_name" => $val
            );

        //Array boolean value type
        }else if(array_key_exists($key, $attributesBooleanType)){
            $narray = array(
                "id" => $attributesBooleanType[$key],
                "value_id" => $attributesBoolean[$val]
            );
        }
        //Array color values
        else if($attributesColor[$key]){
            $narray = array(
                "id" => $attributesColor[$key],
                "value_id" => $colorMap[$val]
            );
        }

         array_push($attributes, $narray);
    }

	// We construct the item to POST
	$item = array(
		"title" => "Iphone 7 Item De Teste - Por Favor, Não Ofertar! --kc:off",
        "category_id" => "MLB257111",
        "price" => 10,
        "currency_id" => "BRL",
        "available_quantity" => 1,
        "buying_mode" => "buy_it_now",
        "listing_type_id" => "bronze",
        "condition" => "new",
        "description" => "Item de Teste. Mercado Livre's PHP SDK.",
        "video_id" => "Q6dsRpVyyWs",
        "warranty" => "12 month",
        "pictures" => array(
            array(
                "source" => "https://upload.wikimedia.org/wikipedia/commons/thumb/6/64/IPhone_7_Plus_Jet_Black.svg/440px-IPhone_7_Plus_Jet_Black.svg.png"
            ),
            array(
                "source" => "https://upload.wikimedia.org/wikipedia/commons/thumb/b/bc/IPhone7.jpg/440px-IPhone7.jpg"
            )
        ),
        "attributes" => $attributes
    );
	
	// We call the post request to list a item
	echo '<pre>';
	print_r($meli->post('/items', $item, array('access_token' => $_SESSION['access_token'])));
	echo '</pre>';

} else {

	echo '<a href="' . $meli->getAuthUrl($redirectURI, Meli::$AUTH_URL['MLB']) . '">Login using MercadoLibre oAuth 2.0</a>';
}

