<? // default system controller

function main_index($par = "test"){
	$data['param'] = $par;
	load_view('main', $data);
}

?>
