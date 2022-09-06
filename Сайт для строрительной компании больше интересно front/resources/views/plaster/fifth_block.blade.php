<section id="fifth_block">
	<div class="container">

		<!-- title -->
		<div class="row mt-0 mt-sm-3">
            <div class="col-lg-5">
                <h1>{!!__('plaster/fifth_block.h1')!!}</h1>
                <h2 class="mt-4">{!!__('plaster/fifth_block.h2')!!}</h2>
            </div>
            <div class="col-lg-7 title-right align-self-end title-button">
                <div class="row">
                    <div class="col-6">
                        <button type="button" class="btn btn-light" data-type="video" data-mdb-ripple-color="dark">{!!__('plaster/fifth_block.video')!!}</button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-light" data-type="text" data-mdb-ripple-color="dark">{!!__('plaster/fifth_block.text')!!}</button>
                    </div>
                </div>
            </div>
		</div>

        <!-- youtube -->
        <div class="row video">
            <div id="youtube_carousel" class="multi-carousel" data-mdb-items="2">
                <div class="multi-carousel-inner">
                    @foreach ($params['DB']['reviews'] as $key => $review)
                        @if ($review->type == 'video')
                            <div class="multi-carousel-item">
                                <div>
                                    <div class="spinner-border text-warning" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <iframe
                                        loading="lazy"
                                        src=""
                                        title="YouTube video player"
                                        frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen>
                                    </iframe>
                                    <h3 class="mt-3">{!!$review->h3!!}</h3>
                                    <p class="mt-3">{!!$review->p!!}</p>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                <button class="carousel-control-prev" type="button" tabindex="0" data-mdb-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                </button>
                <button class="carousel-control-next" type="button" tabindex="0" data-mdb-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                </button>
            </div>
        </div>

		<!-- review -->
		<div class="row text">
			<div id="review_carousel" class="multi-carousel" data-mdb-items="2">
				<div class="multi-carousel-inner">
					@foreach ($params['DB']['reviews'] as $key => $review)
						@if ($review->type == 'text')
							<div class="multi-carousel-item">
								<div>
									<div class="review_carousel_title d-flex align-items-center">
										@if (false)
											<img
												loading="lazy"
												src="{!!url('/data/img/fifth_block/').'/'.$review->src.'.png'!!}"
												alt="{!!$review->src!!}">
										@endif

										{!!$review->src!!}

										<p class="ms-3">
											{!!$review->title_when!!}
											<br>
											<span>{!!$review->title_company!!}</span>
										</p>

										<ul class="rating ms-auto" data-mdb-toggle="rating" data-mdb-readonly="true" data-mdb-value="5">
											@for ($i = 0; $i < $review->rating; $i++)
												<li>
													<i class="far fa-star fa-sm text-warning"></i>
												</li>
											@endfor
										</ul>
									</div>
									<h3 class="mt-3">{!!$review->h3!!}</h3>
									<p class="mt-3">{!!$review->p!!}</p>
								</div>
							</div>
						@endif
					@endforeach
				</div>

				<button class="carousel-control-prev" type="button" tabindex="0" data-mdb-slide="prev">
					<span class="carousel-control-prev-icon" aria-hidden="true"></span>
				</button>
				<button class="carousel-control-next" type="button" tabindex="0" data-mdb-slide="next">
					<span class="carousel-control-next-icon" aria-hidden="true"></span>
				</button>
			</div>
		</div>

		<!-- title -->
		<div class="row fb_title_bottom">
			<div class="col-lg-6">
				<h1 class="text-center text-lg-start">{!!__('plaster/fifth_block.h1_bottom')!!}</h1>
				<h2 class="mt-4 text-center text-lg-start">{!!__('plaster/fifth_block.h2_bottom')!!}</h2>
			</div>
			<div class="col-lg-6 text-center">
				<p id="counter" class="default_color" data-count="{!! $params['countObject'] !!}">0</p>
				<p>{!!__('plaster/fifth_block.text_count')!!}</p>
			</div>
		</div>
	</div>
</section>
