<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<title>Система зворотного зв'язку "Відгук"</title>
	<link href="<?= base_url(); ?>css/bootstrap.min.css" rel="stylesheet">
	<link href="<?= base_url(); ?>css/mdbpro.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css">
	<link href="<?= base_url(); ?>css/font-awesome.min.css" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
	<link href="<?= base_url(); ?>css/style.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="<?= base_url(); ?>DataTables/datatables.min.css"/>
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
	<link rel="stylesheet" type="text/css" href="<?= base_url(); ?>css/sweet-alert.css">
	<script>
		function dowload(data){
			var formData = {id_app:data};
			$.post('Portal/download_file',formData,processData);
			function processData(data) {
				$('.download_parent').html(data);
			}
		}
	</script>
</head>
<body class="<?php if(isset($over)){echo $over;} ?>">
	<i id="load" class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>