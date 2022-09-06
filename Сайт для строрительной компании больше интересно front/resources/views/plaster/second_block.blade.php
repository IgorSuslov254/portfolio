<section id="second_block">
	<div class="container">

		<!-- title -->
		<div class="row">
			<h1>{!!__('plaster/second_block.h1')!!}</h1>
		</div>

		<div class="row mt-4 sbH2">
			<div class="col-12 col-md-8 col-lg-6">
				<h2>{!!__('plaster/second_block.h2')!!}</h2>
                <button type="button" class="btn btn-outline-warning btn-rounded d-none d-lg-block js-modal-video-show" data-mdb-ripple-color="dark">
                    <svg width="34" height="25" viewBox="0 0 34 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M16.5085 0C20.5452 0 23.3636 2.78173 23.3636 6.76819V18.2318C23.3636 22.2183 20.5452 25 16.5085 25H6.85507C2.81836 25 0 22.2183 0 18.2318V6.76819C0 2.78173 2.81836 0 6.85507 0H16.5085ZM29.93 3.96497C30.6616 3.59272 31.52 3.63164 32.2183 4.07157C32.9167 4.50981 33.3333 5.27124 33.3333 6.10372V18.8973C33.3333 19.7315 32.9167 20.4912 32.2183 20.9294C31.8367 21.168 31.41 21.2898 30.98 21.2898C30.6216 21.2898 30.2633 21.2052 29.9283 21.0343L27.4599 19.789C26.5466 19.3254 25.9799 18.3948 25.9799 17.3609V7.63841C25.9799 6.60288 26.5466 5.67225 27.4599 5.21201L29.93 3.96497Z" fill="#1C1C1D" fill-opacity="0.5"/>
                    </svg>
                    Смотреть видео
                </button>
			</div>
			<div class="col-sm-6 d-none d-lg-block right_second_block text-center pe-0">
                @desktop
                    @ios
                        <img loading="lazy" src="{!!url('data/img/second_block/man.png')!!}" class="float-end" alt="man">
                    @elseios
                        <img loading="lazy" src="{!!url('data/img/second_block/man.webp')!!}" class="float-end" alt="man">
                    @endios
                @enddesktop
{{--                <button type="button" class="btn btn-link p-0" data-mdb-ripple-color="dark">--}}
{{--                    <svg width="76" height="76" viewBox="0 0 76 76" fill="none" xmlns="http://www.w3.org/2000/svg">--}}
{{--                        <g filter="url(#filter0_d_103_3209)">--}}
{{--                            <path d="M38 14C49.0232 14 58 22.9725 58 34.0116C58 45.0275 49.0232 54 38 54C26.9768 54 18 45.0275 18 34.0116C18 22.9725 26.9768 14 38 14ZM35.722 26.0598C35.2973 26.0598 34.8919 26.1563 34.5058 26.3493C34.0232 26.6194 33.6371 27.0439 33.4247 27.5456C33.2896 27.8929 33.0772 28.9349 33.0772 28.9542C32.8649 30.0926 32.749 31.945 32.749 33.9904C32.749 35.9411 32.8649 37.7144 33.0386 38.8722C33.0579 38.8915 33.2703 40.1843 33.5019 40.6281C33.9266 41.4385 34.7568 41.9402 35.6448 41.9402H35.722C36.3012 41.9209 37.5174 41.4192 37.5174 41.3999C39.5637 40.5509 43.5985 37.9074 45.2201 36.1515L45.3359 36.0357C45.5483 35.8234 45.8185 35.4954 45.8764 35.4182C46.1853 35.013 46.3398 34.5113 46.3398 34.0116C46.3398 33.4501 46.166 32.9291 45.8378 32.5046C45.7606 32.4274 45.471 32.0994 45.2008 31.8292C43.6178 30.1312 39.4865 27.3526 37.3243 26.5036C36.9961 26.3705 36.166 26.0791 35.722 26.0598Z" fill="white"/>--}}
{{--                        </g>--}}
{{--                        <defs>--}}
{{--                            <filter id="filter0_d_103_3209" x="0" y="0" width="76" height="76" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">--}}
{{--                                <feFlood flood-opacity="0" result="BackgroundImageFix"/>--}}
{{--                                <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/>--}}
{{--                                <feOffset dy="4"/>--}}
{{--                                <feGaussianBlur stdDeviation="9"/>--}}
{{--                                <feComposite in2="hardAlpha" operator="out"/>--}}
{{--                                <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.41 0"/>--}}
{{--                                <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_103_3209"/>--}}
{{--                                <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_103_3209" result="shape"/>--}}
{{--                            </filter>--}}
{{--                        </defs>--}}
{{--                    </svg>--}}
{{--                    <span>--}}
{{--                        {!!__('plaster/second_block.video')!!}--}}
{{--                    </span>--}}
{{--                </button>--}}
			</div>
		</div>

        <div id="videoMobilePlanshet" class="mt-4 d-lg-none">
            <div class="text-center">
                <div class="spinner-border text-warning" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            <iframe class="embed-responsive-item w-100 p-0" src="" allowfullscreen></iframe>
        </div>

        <!-- left_second_block -->
		<div class="row left_second_block text-center">
			@foreach (__('plaster/second_block.advantages') as $advantage)
				<div class="col-6 col-md-4 col-lg-3">
					{!!$advantage['img']!!}
					<h3 class="mt-3">{!!$advantage['h3']!!}</h3>
					<p class="mt-4">{!!$advantage['p']!!}</p>
				</div>

				@if ($loop->iteration == 3)
					<div class="d-none d-lg-block col-ld-3"></div>
				@endif
			@endforeach
		</div>

        <!-- Modal -->
        <div class="modal fade" id="aboutUsModal" tabindex="-1" aria-labelledby="aboutUsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl h-75">
                <div class="modal-content h-100 rounded-3">
                    <div class="text-center h-100 d-flex align-items-center justify-content-center">
                        <div class="spinner-border text-warning" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
	</div>
</section>
