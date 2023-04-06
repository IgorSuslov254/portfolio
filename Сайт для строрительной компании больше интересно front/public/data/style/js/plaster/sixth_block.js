/*************************************/
/************sixth block**************/
/*************************************/

function map(){
	$.ajax({
		url: 'https://api-maps.yandex.ru/2.1/?lang=ru_RU&amp;apikey=9c90ebb7-fef4-4c70-9a00-6fbd169ca487',
		method: 'GET',
		dataType: 'script',
		success: function( data ){
			let data_map = $('#map').data('map');

			ymaps.ready(function () {
				var mapCenter = [45.035470, 38.975313],
					map = new ymaps.Map('map', {
						center: mapCenter,
						zoom: 12,
						controls: ['zoomControl']
					}, {
						searchControlProvider: 'yandex#search'
					});

				var clusterer = new ymaps.Clusterer({
					clusterDisableClickZoom: false,
					clusterOpenBalloonOnClick: true,
					clusterBalloonContentLayout: 'cluster#balloonCarousel',
					clusterBalloonItemContentLayout: 'cluster#balloonCarouselItemContent',
					clusterBalloonPanelMaxMapArea: 0,
					clusterBalloonPagerSize: 5,
					clusterBalloonPagerType: 'marker',
					clusterIcons: [
					{
						href: './data/img/sixth_block/mark.png',
						size: [40, 40],
						offset: [-20, -40]
					}]
				});

				var placemarks = [];

				data_map.forEach(
					element => {
						var placemark = new ymaps.Placemark([element.coordinate_x, element.coordinate_y], {
							balloonContentHeader: 
								'<h1>'+element.title+'</h1>'+
								'<h2>'+element.address+'</h2>',
							balloonContentBody: 
								'<div class="lightbox">'+
									'<img'+
										' loading="lazy"'+
										' src="'+ './data/img/sixth_block/object/' + element.img +'_min.jpg"'+
										' class="w-100"'+
										' data-mdb-img="'+ './data/img/sixth_block/object/' + element.img +'.jpg"'+
									'">'+
								'</div>',
							/*balloonContentFooter: 
							'<div class="d-flex">'+
								'<p class="map_p"> '+ element.param_one +' </p>'+
								'<p class="map_p ms-auto"> '+ element.param_two +' </p>'+
							'</div>'+
							'<div class="d-flex">'+
								'<p class="map_p"> '+ element.param_three +' </p>'+
								'<p class="map_p ms-auto"> '+ element.param_four +' </p>'+
							'</div>'*/
						}, {
							iconLayout: 'default#image',
							iconImageHref: './data/img/sixth_block/mark.png',
							iconImageSize: [40, 40],
							iconImageOffset: [-20, -40]
						});
						placemarks.push(placemark);
					}
				);

				clusterer.add(placemarks);
				map.geoObjects.add(clusterer);
				map.behaviors.disable('scrollZoom');

				// clusterer.balloon.open(clusterer.getClusters()[0]);
				$('#sixth_block #map > div').remove();
				$('#sixth_block #map').css({
					'background' : 'none'
				});
			});
		},
		error: function (jqXHR, exception) {
			error_ajax(jqXHR, exception);
		}
	});
}